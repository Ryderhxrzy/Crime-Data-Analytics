<?php
session_start();
require_once '../../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../../index.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    header('Location: ../../../index.php?error=Email and password are required');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../../../index.php?error=Invalid email format');
    exit;
}

if ($mysqli->connect_error) {
    header('Location: ../../../index.php?error=Database connection failed');
    exit;
}

try {
    $stmt = $mysqli->prepare("SELECT id, email, password, full_name, role, status, account_status, registration_type, attempt_count FROM crime_department_admin_users WHERE email = ? LIMIT 1");

    if (!$stmt) {
        throw new Exception("Database preparation failed: " . $mysqli->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        header('Location: ../../../index.php?error=Account not found. Please contact administrator.');
        exit;
    }

    $user = $result->fetch_assoc();

    // Check if account is locked
    if ($user['status'] === 'lock') {
        header('Location: ../../../index.php?error=Your account has been locked due to multiple failed login attempts. Please check your email for unlock instructions.');
        exit;
    }

    // Check if account is verified
    if ($user['account_status'] === 'unverified') {
        header('Location: ../../../index.php?error=Your account is not verified. Please contact the super admin to verify your account.');
        exit;
    }

    // Check registration type - only allow email login for email-registered accounts
    if ($user['registration_type'] === 'google') {
        header('Location: ../../../index.php?error=This account is registered with Google. Please use Google Sign-In.');
        exit;
    }

    // Check password (should not be null for email-registered accounts)
    if ($user['password'] === null) {
        header('Location: ../../../index.php?error=Please use Google Sign-In for this account.');
        exit;
    }

    if (!password_verify($password, $user['password'])) {
        // Get user IP address and user agent for failed login log
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;

        // Increment attempt count
        $current_attempts = intval($user['attempt_count']) + 1;
        $new_status = $user['status'];

        // Lock account if attempts reach 3
        if ($current_attempts >= 3) {
            $new_status = 'lock';
        }

        // Update attempt count and status, and log IP address
        $update_stmt = $mysqli->prepare("UPDATE crime_department_admin_users SET attempt_count = ?, status = ?, ip_address = ? WHERE id = ?");
        $update_stmt->bind_param("issi", $current_attempts, $new_status, $ip_address, $user['id']);
        $update_stmt->execute();
        $update_stmt->close();

        // Log failed login attempt
        $description = "Failed login attempt - incorrect password (Attempt #" . $current_attempts . ")";

        $log_stmt = $mysqli->prepare("INSERT INTO crime_department_activity_logs (admin_user_id, activity_type, description, ip_address, user_agent) VALUES (?, 'failed_login', ?, ?, ?)");
        $log_stmt->bind_param("isss", $user['id'], $description, $ip_address, $user_agent);
        $log_stmt->execute();
        $log_stmt->close();

        // If account is now locked, send unlock email
        if ($new_status === 'lock') {
            // Send unlock email
            require_once __DIR__ . '/../../utils/mailer.php';
            require_once __DIR__ . '/../../helpers/url-helper.php';

            $unlock_token = bin2hex(random_bytes(32));
            $unlock_link = getUrl("frontend/user-page/unlock-account.php?token=" . $unlock_token);

            // Store unlock token in database
            $token_stmt = $mysqli->prepare("UPDATE crime_department_admin_users SET unlock_token = ?, unlock_token_expiry = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE id = ?");
            $token_stmt->bind_param("si", $unlock_token, $user['id']);
            $token_stmt->execute();
            $token_stmt->close();

            // Prepare email content
            $subject = "Account Locked - Unlock Required";
            $emailBody = "
                <h2 style='margin: 0 0 20px 0; color: #333333; font-size: 20px;'>Account Locked</h2>
                <p style='margin: 0 0 20px 0;'>Your account has been locked due to 3 failed login attempts.</p>
                <p style='margin: 0 0 20px 0;'>For security reasons, please unlock your account by clicking the button below:</p>
                <table width='100%' cellpadding='0' cellspacing='0'>
                    <tr>
                        <td align='center' style='padding: 20px 0;'>
                            <a href='" . $unlock_link . "' style='background-color: #4c8a89; color: #ffffff; text-decoration: none; padding: 15px 30px; border-radius: 5px; font-size: 16px; font-weight: bold; display: inline-block;'>Unlock Account</a>
                        </td>
                    </tr>
                </table>
                <p style='margin: 20px 0; color: #666666; font-size: 14px;'>
                    Or copy and paste this link into your browser:
                </p>
                <p style='margin: 0 0 20px 0; color: #4c8a89; font-size: 12px; word-break: break-all;'>
                    " . $unlock_link . "
                </p>
                <p style='margin: 20px 0; font-size: 14px;'><strong>Important:</strong> This link will expire in <strong>1 hour</strong>.</p>
                <p style='margin: 20px 0 0 0; font-size: 14px;'>If you did not attempt to login, please contact the administrator immediately.</p>
                <div style='margin: 20px 0; padding: 15px; background-color: #f8f9fa; border-left: 4px solid #4c8a89; border-radius: 4px;'>
                    <p style='margin: 0; font-size: 14px;'><strong>IP Address:</strong> " . htmlspecialchars($ip_address) . "</p>
                    <p style='margin: 10px 0 0 0; font-size: 14px;'><strong>Time:</strong> " . date('Y-m-d H:i:s') . "</p>
                </div>
            ";

            // Send email
            try {
                $mailer = new Mailer();
                $mailer->sendGenericEmail($user['email'], $user['full_name'], $subject, $emailBody);
            } catch (Exception $e) {
                error_log("Failed to send unlock email: " . $e->getMessage());
            }

            header('Location: ../../../index.php?error=Account locked due to multiple failed login attempts. Please check your email for unlock instructions.');
        } else {
            $remaining_attempts = 3 - $current_attempts;
            header('Location: ../../../index.php?error=Invalid email or password. ' . $remaining_attempts . ' attempt(s) remaining before account lock.');
        }
        exit;
    }

    $stmt->close();

    // Check if 2FA is enabled
    $settings_stmt = $mysqli->prepare("SELECT two_factor_auth FROM crime_department_user_settings WHERE admin_user_id = ? LIMIT 1");
    $settings_stmt->bind_param("i", $user['id']);
    $settings_stmt->execute();
    $settings_result = $settings_stmt->get_result();
    $user_settings = $settings_result->fetch_assoc();
    $settings_stmt->close();

    $two_factor_enabled = $user_settings['two_factor_auth'] ?? 0;

    // Get user IP address and user agent
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;

    if ($two_factor_enabled == 1) {
        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $otp_expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        // Delete any existing unused OTP for this user
        $delete_otp = $mysqli->prepare("DELETE FROM crime_department_otp_verification WHERE admin_user_id = ? AND is_used = 0");
        $delete_otp->bind_param("i", $user['id']);
        $delete_otp->execute();
        $delete_otp->close();

        // Insert new OTP into database
        $otp_stmt = $mysqli->prepare("INSERT INTO crime_department_otp_verification (admin_user_id, otp_code, expires_at) VALUES (?, ?, ?)");
        $otp_stmt->bind_param("iss", $user['id'], $otp, $otp_expiry);
        $otp_stmt->execute();
        $otp_stmt->close();

        // Send OTP email
        require_once __DIR__ . '/../../utils/mailer.php';

        try {
            $mailer = new Mailer();
            $mailer->sendOTP($user['email'], $user['full_name'], $otp);
        } catch (Exception $e) {
            error_log("Failed to send OTP email: " . $e->getMessage());
        }

        // Store user data in session for OTP verification
        $_SESSION['2fa_user_id'] = $user['id'];
        $_SESSION['2fa_email'] = $user['email'];
        $_SESSION['2fa_ip'] = $ip_address;

        // Log OTP sent
        $log_stmt = $mysqli->prepare("INSERT INTO crime_department_activity_logs (admin_user_id, activity_type, description, ip_address, user_agent) VALUES (?, '2fa_otp_sent', 'OTP sent for login verification', ?, ?)");
        $log_stmt->bind_param("iss", $user['id'], $ip_address, $user_agent);
        $log_stmt->execute();
        $log_stmt->close();

        // Redirect to OTP verification page
        header('Location: verify-otp.php');
        exit;
    }

    // Direct login (no 2FA)
    // Update user status to 'active', reset attempt count, update IP address and last_login timestamp
    $update_stmt = $mysqli->prepare("UPDATE crime_department_admin_users SET status = 'active', attempt_count = 0, ip_address = ?, last_login = NOW() WHERE id = ?");
    $update_stmt->bind_param("si", $ip_address, $user['id']);
    $update_stmt->execute();
    $update_stmt->close();

    // Insert activity log for login
    $log_stmt = $mysqli->prepare("INSERT INTO crime_department_activity_logs (admin_user_id, activity_type, description, ip_address, user_agent) VALUES (?, 'login', 'User logged in via email/password', ?, ?)");
    $log_stmt->bind_param("iss", $user['id'], $ip_address, $user_agent);
    $log_stmt->execute();
    $log_stmt->close();

    // Regenerate session ID for security
    session_regenerate_id(true);

    // Store user information in session
    $_SESSION['user'] = [
        'id' => $user['id'],
        'email' => $user['email'],
        'full_name' => $user['full_name'],
        'role' => $user['role']
    ];

    // Set session activity timestamps
    $_SESSION['last_activity'] = time();
    $_SESSION['session_created'] = time();

    // Store user IP and user agent for security
    $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'] ?? '';
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';

    // Set session cookie parameters for security (1 hour)
    $cookie_params = session_get_cookie_params();
    $is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
    setcookie(
        session_name(),
        session_id(),
        time() + 3600, // 1 hour expiry
        $cookie_params['path'],
        $cookie_params['domain'],
        $is_https, // Secure - only use HTTPS flag when actually on HTTPS
        true  // HttpOnly - prevents JavaScript access
    );

    // Redirect to system overview page
    header('Location: ../../../frontend/admin-page/system-overview.php');
    exit;

} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    header('Location: ../../../index.php?error=Login failed. Please try again.');
    exit;
}

$mysqli->close();
?>
