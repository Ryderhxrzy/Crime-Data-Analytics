<?php
/**
 * OTP Verification Controller
 * Handles OTP verification logic for 2FA login
 */

session_start();
require_once '../../config.php';
require_once '../../helpers/otp-view-helper.php';

// Check if user is in 2FA verification process
if (!isset($_SESSION['2fa_user_id'])) {
    header('Location: ../../../index.php?error=Invalid access');
    exit;
}

$user_id = $_SESSION['2fa_user_id'];
$user_email = $_SESSION['2fa_email'] ?? '';
$error = null;

// Get OTP remaining seconds and prepare view data
$remaining_seconds = getOtpRemainingSeconds($mysqli, $user_id);
$view_data = prepareOtpTimerData($remaining_seconds);

// Handle OTP verification POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp_input = trim($_POST['otp'] ?? '');

    if (empty($otp_input)) {
        $error = 'OTP code is required';
    } else {
        // Get user info
        $user_stmt = $mysqli->prepare("SELECT id, email, full_name, role FROM crime_department_admin_users WHERE id = ? LIMIT 1");
        $user_stmt->bind_param("i", $user_id);
        $user_stmt->execute();
        $user_result = $user_stmt->get_result();
        $user = $user_result->fetch_assoc();
        $user_stmt->close();

        if (!$user) {
            header('Location: ../../../index.php?error=User not found');
            exit;
        }

        // Verify OTP from otp_verification table
        $otp_stmt = $mysqli->prepare("SELECT otp_code, expires_at, is_used FROM crime_department_otp_verification WHERE admin_user_id = ? AND is_used = 0 ORDER BY created_at DESC LIMIT 1");
        $otp_stmt->bind_param("i", $user_id);
        $otp_stmt->execute();
        $otp_result = $otp_stmt->get_result();
        $otp_data = $otp_result->fetch_assoc();
        $otp_stmt->close();

        if (!$otp_data) {
            $error = 'No valid OTP found. Please login again.';
        } elseif (strtotime($otp_data['expires_at']) < time()) {
            $error = 'OTP has expired. Please login again.';
        } elseif ($otp_data['otp_code'] !== $otp_input) {
            $error = 'Invalid OTP code. Please try again.';

            // Log failed OTP attempt
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
            $log_stmt = $mysqli->prepare("INSERT INTO crime_department_activity_logs (admin_user_id, activity_type, description, ip_address, user_agent) VALUES (?, '2fa_otp_failed', 'Failed OTP verification attempt', ?, ?)");
            $log_stmt->bind_param("iss", $user_id, $ip_address, $user_agent);
            $log_stmt->execute();
            $log_stmt->close();
        } else {
            // OTP is valid - complete login
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;

            // Mark OTP as used
            $mark_used = $mysqli->prepare("UPDATE crime_department_otp_verification SET is_used = 1 WHERE admin_user_id = ? AND otp_code = ?");
            $mark_used->bind_param("is", $user_id, $otp_input);
            $mark_used->execute();
            $mark_used->close();

            // Update user status to 'active', reset attempt count, update IP address and last_login timestamp
            $update_stmt = $mysqli->prepare("UPDATE crime_department_admin_users SET status = 'active', attempt_count = 0, ip_address = ?, last_login = NOW() WHERE id = ?");
            $update_stmt->bind_param("si", $ip_address, $user_id);
            $update_stmt->execute();
            $update_stmt->close();

            // Insert activity log for successful login
            $log_stmt = $mysqli->prepare("INSERT INTO crime_department_activity_logs (admin_user_id, activity_type, description, ip_address, user_agent) VALUES (?, 'login', 'User logged in via email/password with 2FA', ?, ?)");
            $log_stmt->bind_param("iss", $user_id, $ip_address, $user_agent);
            $log_stmt->execute();
            $log_stmt->close();

            // Clear 2FA session variables
            unset($_SESSION['2fa_user_id']);
            unset($_SESSION['2fa_email']);
            unset($_SESSION['2fa_ip']);

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
                time() + 3600,
                $cookie_params['path'],
                $cookie_params['domain'],
                $is_https,
                true
            );

            // Redirect to system overview page
            header('Location: ../../../frontend/admin-page/system-overview.php');
            exit;
        }
    }

    // Recalculate view data after error (timer may have changed)
    $remaining_seconds = getOtpRemainingSeconds($mysqli, $user_id);
    $view_data = prepareOtpTimerData($remaining_seconds);
}

// Extract view variables for the template
extract($view_data);
