<?php
session_start();
require_once '../../config.php';

// Check if user is in 2FA verification process
if (!isset($_SESSION['2fa_user_id'])) {
    header('Location: ../../../index.php?error=Invalid access');
    exit;
}

$user_id = $_SESSION['2fa_user_id'];
$user_email = $_SESSION['2fa_email'] ?? '';

// Get OTP expiry time for timer
$otp_expiry_stmt = $mysqli->prepare("SELECT expires_at FROM crime_department_otp_verification WHERE admin_user_id = ? AND is_used = 0 ORDER BY created_at DESC LIMIT 1");
$otp_expiry_stmt->bind_param("i", $user_id);
$otp_expiry_stmt->execute();
$otp_expiry_result = $otp_expiry_stmt->get_result();
$otp_expiry_data = $otp_expiry_result->fetch_assoc();
$otp_expiry_stmt->close();

// Calculate remaining seconds
$remaining_seconds = 0;
if ($otp_expiry_data) {
    $remaining_seconds = max(0, strtotime($otp_expiry_data['expires_at']) - time());
}

// Handle OTP verification
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
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - AlerTaraQC</title>
    <link rel="stylesheet" href="../../../frontend/css/global.css">
    <link rel="stylesheet" href="../../../frontend/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="icon" type="image/x-icon" href="../../../frontend/image/favicon.ico">
</head>
<body>
    <div class="login-wrapper">
        <div class="login-container">
            <div class="login-card">
                <div class="login-header">
                    <div class="login-logo">
                        <img src="../../../frontend/image/logo.svg" alt="Crime Data Analytics Logo">
                    </div>
                    <h1 class="login-title">Verify OTP</h1>
                    <p class="login-subtitle">Enter the 6-digit code sent to <?php echo htmlspecialchars($user_email); ?></p>
                </div>

                <form class="login-form" method="POST" action="" id="otpForm">

                    <!-- Timer Display -->
                    <div class="otp-timer-container">
                        <div id="timerContainer" class="otp-timer-display">
                            <i class="fas fa-clock otp-timer-icon"></i>
                            <span id="otpTimer" class="otp-timer-text <?php echo $timer_class; ?>">
                                <?php echo $timer_display; ?>
                            </span>
                        </div>
                        <p id="timerExpired" class="otp-timer-expired" style="display: <?php echo $timer_expired_display; ?>;">
                            <i class="fas fa-exclamation-circle"></i> OTP has expired. Please login again.
                        </p>
                    </div>

                    <!-- OTP Input Boxes -->
                    <div class="otp-input-group">
                        <div class="otp-input-container">
                            <input type="text" class="otp-box" maxlength="1" data-index="0" autocomplete="off" inputmode="numeric" pattern="[0-9]" <?php echo $input_disabled; ?>>
                            <input type="text" class="otp-box" maxlength="1" data-index="1" autocomplete="off" inputmode="numeric" pattern="[0-9]" <?php echo $input_disabled; ?>>
                            <input type="text" class="otp-box" maxlength="1" data-index="2" autocomplete="off" inputmode="numeric" pattern="[0-9]" <?php echo $input_disabled; ?>>
                            <input type="text" class="otp-box" maxlength="1" data-index="3" autocomplete="off" inputmode="numeric" pattern="[0-9]" <?php echo $input_disabled; ?>>
                            <input type="text" class="otp-box" maxlength="1" data-index="4" autocomplete="off" inputmode="numeric" pattern="[0-9]" <?php echo $input_disabled; ?>>
                            <input type="text" class="otp-box" maxlength="1" data-index="5" autocomplete="off" inputmode="numeric" pattern="[0-9]" <?php echo $input_disabled; ?>>
                        </div>
                        <input type="hidden" name="otp" id="otpValue">
                    </div>

                    <button type="submit" class="btn-login" id="verifyOtpButton" <?php echo $button_disabled; ?>>
                        <span class="btn-text">Verify OTP</span>
                        <span class="btn-loader" style="display: none;">
                            <span class="spinner"></span>
                            Verifying...
                        </span>
                    </button>

                    <div class="form-options" style="justify-content: center; margin-top: 20px;">
                        <a href="../../../index.php" class="forgot-password" style="text-decoration: none;">
                            <i class="fas fa-arrow-left" style="margin-right: 5px;"></i>
                            Back to Login
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../../frontend/js/alert-utils.js"></script>
    <script src="../../../frontend/js/verify-otp.js"></script>
    <script>
        // Initialize OTP verification with remaining seconds and error message
        initVerifyOtp(
            <?php echo $remaining_seconds; ?>,
            <?php echo isset($error) ? json_encode($error) : 'null'; ?>
        );
    </script>
</body>
</html>
