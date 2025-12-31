<?php
/**
 * Account Unlock Page
 * Allows users to unlock their account using a valid unlock token
 */

session_start();
require_once '../../api/config.php';

// Get token from URL
$token = $_GET['token'] ?? '';

if (empty($token)) {
    $_SESSION['flash_error'] = 'Invalid unlock link';
    header('Location: ../../index.php');
    exit;
}

// Verify token exists and is valid
$stmt = $mysqli->prepare("
    SELECT id, email, full_name, unlock_token_expiry, status
    FROM crime_department_admin_users
    WHERE unlock_token = ? AND status = 'lock'
    LIMIT 1
");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['flash_error'] = 'Invalid or expired unlock link';
    $stmt->close();
    header('Location: ../../index.php');
    exit;
}

$user_data = $result->fetch_assoc();
$stmt->close();

// Check if token has expired
if (strtotime($user_data['unlock_token_expiry']) < time()) {
    $_SESSION['flash_error'] = 'This unlock link has expired. Please contact administrator.';
    header('Location: ../../index.php');
    exit;
}

// Get user IP address and user agent
$ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;

// Unlock account and reset attempt count
$update_stmt = $mysqli->prepare("UPDATE crime_department_admin_users SET status = 'active', attempt_count = 0, unlock_token = NULL, unlock_token_expiry = NULL WHERE id = ?");
$update_stmt->bind_param("i", $user_data['id']);
$update_stmt->execute();
$update_stmt->close();

// Log the unlock activity
$description = "Account unlocked via email link";
$log_stmt = $mysqli->prepare("INSERT INTO crime_department_activity_logs (admin_user_id, activity_type, description, ip_address, user_agent) VALUES (?, 'account_unlock', ?, ?, ?)");
$log_stmt->bind_param("isss", $user_data['id'], $description, $ip_address, $user_agent);
$log_stmt->execute();
$log_stmt->close();

// Send confirmation email
require_once '../../api/utils/mailer.php';

// Generate login URL based on environment (same as Mailer class)
$app_env = $_ENV['APP_ENV'] ?? 'local';
if ($app_env === 'production') {
    $login_url = $_ENV['DEPLOY_LINK'] ?? 'https://crime.alertaraqc.com/';
} else {
    $login_url = 'http://localhost/Crime-Data-Analytics/';
}

$subject = "Account Unlocked Successfully";
$emailBody = "
    <h2 style='margin: 0 0 20px 0; color: #333333; font-size: 20px;'>Account Unlocked</h2>
    <p style='margin: 0 0 20px 0;'>Your account has been successfully unlocked.</p>
    <p style='margin: 0 0 20px 0;'>You can now login to your account using your credentials.</p>
    <div style='margin: 20px 0; padding: 15px; background-color: #f8f9fa; border-left: 4px solid #4c8a89; border-radius: 4px;'>
        <p style='margin: 0; font-size: 14px;'><strong>Unlock Time:</strong> " . date('Y-m-d H:i:s') . "</p>
        <p style='margin: 10px 0 0 0; font-size: 14px;'><strong>IP Address:</strong> " . htmlspecialchars($ip_address) . "</p>
    </div>
    <p style='margin: 20px 0; font-size: 14px;'>If you did not request this unlock, please contact the administrator immediately and change your password.</p>
    <table width='100%' cellpadding='0' cellspacing='0'>
        <tr>
            <td align='center' style='padding: 20px 0;'>
                <a href='" . $login_url . "' style='background-color: #4c8a89; color: #ffffff; text-decoration: none; padding: 15px 30px; border-radius: 5px; font-size: 16px; font-weight: bold; display: inline-block;'>Login Now</a>
            </td>
        </tr>
    </table>
";

try {
    $mailer = new Mailer();
    $mailer->sendGenericEmail($user_data['email'], $user_data['full_name'], $subject, $emailBody);
} catch (Exception $e) {
    error_log("Failed to send unlock confirmation email: " . $e->getMessage());
}

$mysqli->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Unlocked - AlerTaraQC</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="icon" type="image/x-icon" href="../image/favicon.ico">
</head>
<body>
    <!-- Unlock Account Container -->
    <div class="login-wrapper">
        <div class="login-container">
            <!-- Success Card -->
            <div class="login-card">
                <!-- Success Icon -->
                <div class="login-header" style="text-align: center;">
                    <div style="width: 80px; height: 80px; margin: 0 auto 20px; background-color: #10b981; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-check" style="font-size: 40px; color: white;"></i>
                    </div>
                    <h1 class="login-title">Account Unlocked!</h1>
                    <p class="login-subtitle">Your account has been successfully unlocked.</p>
                </div>

                <!-- Account Details -->
                <div style="background-color: #f3f4f6; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
                    <p style="margin: 0 0 10px 0; color: #4b5563;">
                        <i class="fas fa-envelope" style="color: #4c8a89; margin-right: 10px;"></i>
                        <strong>Email:</strong> <?php echo htmlspecialchars($user_data['email']); ?>
                    </p>
                    <p style="margin: 0 0 10px 0; color: #4b5563;">
                        <i class="fas fa-clock" style="color: #4c8a89; margin-right: 10px;"></i>
                        <strong>Unlocked at:</strong> <?php echo date('F j, Y, g:i a'); ?>
                    </p>
                    <p style="margin: 0; color: #4b5563;">
                        <i class="fas fa-network-wired" style="color: #4c8a89; margin-right: 10px;"></i>
                        <strong>IP Address:</strong> <?php echo htmlspecialchars($ip_address); ?>
                    </p>
                </div>

                <!-- Security Notice -->
                <div style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
                    <p style="margin: 0; color: #92400e; font-size: 14px;">
                        <i class="fas fa-exclamation-triangle" style="margin-right: 8px;"></i>
                        <strong>Security Notice:</strong> If you did not request this unlock, please contact the administrator immediately and change your password.
                    </p>
                </div>

                <!-- Information -->
                <div style="margin-bottom: 20px; padding: 15px; background-color: #e0f2f1; border-radius: 8px;">
                    <p style="margin: 0 0 10px 0; color: #00695c; font-size: 14px;">
                        <i class="fas fa-info-circle" style="margin-right: 8px;"></i>
                        <strong>What happened?</strong>
                    </p>
                    <p style="margin: 0; color: #00695c; font-size: 14px;">
                        Your account was locked after 3 failed login attempts for security reasons. You can now login with your correct credentials.
                    </p>
                </div>

                <!-- Login Button -->
                <a href="<?php echo htmlspecialchars($login_url); ?>" class="btn-login" style="text-align: center; text-decoration: none; display: block;">
                    <i class="fas fa-sign-in-alt" style="margin-right: 8px;"></i>
                    Continue to Login
                </a>

                <!-- Footer Note -->
                <div style="margin-top: 20px; text-align: center; color: #6b7280; font-size: 12px;">
                    <p style="margin: 0;">
                        A confirmation email has been sent to your email address.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Show success notification (no auto-redirect)
        Swal.fire({
            icon: 'success',
            title: 'Account Unlocked!',
            text: 'Your account has been successfully unlocked. Click the button below to login.',
            confirmButtonColor: '#4c8a89',
            confirmButtonText: 'OK',
            allowOutsideClick: true,
            allowEscapeKey: true
        });
    </script>
</body>
</html>
