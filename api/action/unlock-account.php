<?php
session_start();
require_once '../config.php';

// Check if token is provided
if (!isset($_GET['token']) || empty($_GET['token'])) {
    header('Location: ../../index.php?error=Invalid unlock link');
    exit;
}

$token = trim($_GET['token']);

try {
    // Find user with this unlock token
    $stmt = $mysqli->prepare("SELECT id, email, full_name, unlock_token_expiry FROM crime_department_admin_users WHERE unlock_token = ? AND status = 'lock' LIMIT 1");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $stmt->close();
        header('Location: ../../index.php?error=Invalid or expired unlock link');
        exit;
    }

    $user = $result->fetch_assoc();
    $stmt->close();

    // Check if token has expired
    $expiry_time = strtotime($user['unlock_token_expiry']);
    if ($expiry_time < time()) {
        header('Location: ../../index.php?error=Unlock link has expired. Please contact administrator.');
        exit;
    }

    // Get user IP address and user agent
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;

    // Unlock account and reset attempt count
    $update_stmt = $mysqli->prepare("UPDATE crime_department_admin_users SET status = 'active', attempt_count = 0, unlock_token = NULL, unlock_token_expiry = NULL WHERE id = ?");
    $update_stmt->bind_param("i", $user['id']);
    $update_stmt->execute();
    $update_stmt->close();

    // Log the unlock activity
    $description = "Account unlocked via email link";
    $log_stmt = $mysqli->prepare("INSERT INTO crime_department_activity_logs (admin_user_id, activity_type, description, ip_address, user_agent) VALUES (?, 'account_unlock', ?, ?, ?)");
    $log_stmt->bind_param("isss", $user['id'], $description, $ip_address, $user_agent);
    $log_stmt->execute();
    $log_stmt->close();

    // Send confirmation email
    require_once '../utils/mailer.php';

    $login_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . "/Crime-Data-Analytics/";

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
        $mailer->sendGenericEmail($user['email'], $user['full_name'], $subject, $emailBody);
    } catch (Exception $e) {
        error_log("Failed to send unlock confirmation email: " . $e->getMessage());
    }

    // Redirect to login page with success message
    header('Location: ../../index.php?success=Your account has been unlocked successfully. You can now login.');
    exit;

} catch (Exception $e) {
    error_log("Unlock account error: " . $e->getMessage());
    header('Location: ../../index.php?error=Failed to unlock account. Please try again or contact administrator.');
    exit;
}

$mysqli->close();
?>
