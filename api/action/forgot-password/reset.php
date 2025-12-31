<?php
/**
 * Password Reset Processing Script
 * Processes the password reset form and updates the user's password
 */

session_start();
require_once '../../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../../index.php');
    exit;
}

$token = trim($_POST['token'] ?? '');
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Validate inputs
if (empty($token)) {
    $_SESSION['flash_error'] = 'Invalid reset link';
    header('Location: ../../../index.php');
    exit;
}

if (empty($new_password) || empty($confirm_password)) {
    $_SESSION['flash_error'] = 'All fields are required';
    header('Location: ../../../frontend/user-page/reset-password.php?token=' . urlencode($token));
    exit;
}

if (strlen($new_password) < 8) {
    $_SESSION['flash_error'] = 'Password must be at least 8 characters long';
    header('Location: ../../../frontend/user-page/reset-password.php?token=' . urlencode($token));
    exit;
}

if ($new_password !== $confirm_password) {
    $_SESSION['flash_error'] = 'Passwords do not match';
    header('Location: ../../../frontend/user-page/reset-password.php?token=' . urlencode($token));
    exit;
}

if ($mysqli->connect_error) {
    $_SESSION['flash_error'] = 'Database connection failed';
    header('Location: ../../../frontend/user-page/reset-password.php?token=' . urlencode($token));
    exit;
}

try {
    // Verify token exists and is valid
    $stmt = $mysqli->prepare("
        SELECT pr.id, pr.admin_user_id, pr.expires_at, pr.is_used, u.email, u.registration_type
        FROM crime_department_password_resets pr
        JOIN crime_department_admin_users u ON pr.admin_user_id = u.id
        WHERE pr.reset_token = ? AND pr.is_used = 0
        LIMIT 1
    ");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $_SESSION['flash_error'] = 'Invalid or already used reset link';
        $stmt->close();
        header('Location: ../../../index.php');
        exit;
    }

    $reset_data = $result->fetch_assoc();
    $stmt->close();

    // Check if token has expired
    if (strtotime($reset_data['expires_at']) < time()) {
        $_SESSION['flash_error'] = 'This reset link has expired. Please request a new one.';
        header('Location: ../../../frontend/user-page/forgot-password.php');
        exit;
    }

    // Check if account is registered with email
    if ($reset_data['registration_type'] !== 'email') {
        $_SESSION['flash_error'] = 'This account is registered with Google. Cannot reset password.';
        header('Location: ../../../index.php');
        exit;
    }

    // Hash the new password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Update user's password
    $update_stmt = $mysqli->prepare("UPDATE crime_department_admin_users SET password = ? WHERE id = ?");
    $update_stmt->bind_param("si", $hashed_password, $reset_data['admin_user_id']);
    $update_stmt->execute();
    $update_stmt->close();

    // Mark reset token as used
    $mark_used_stmt = $mysqli->prepare("UPDATE crime_department_password_resets SET is_used = 1 WHERE id = ?");
    $mark_used_stmt->bind_param("i", $reset_data['id']);
    $mark_used_stmt->execute();
    $mark_used_stmt->close();

    // Log activity
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
    $description = "Password reset completed for email: " . $reset_data['email'];
    $log_stmt = $mysqli->prepare("INSERT INTO crime_department_activity_logs (admin_user_id, activity_type, description, ip_address, user_agent) VALUES (?, 'password_reset_complete', ?, ?, ?)");
    $log_stmt->bind_param("isss", $reset_data['admin_user_id'], $description, $ip_address, $user_agent);
    $log_stmt->execute();
    $log_stmt->close();

    $_SESSION['flash_success'] = 'Your password has been reset successfully. You can now login with your new password.';
    header('Location: ../../../index.php');
    exit;

} catch (Exception $e) {
    error_log("Password reset error: " . $e->getMessage());
    $_SESSION['flash_error'] = 'An error occurred. Please try again.';
    header('Location: ../../../frontend/user-page/reset-password.php?token=' . urlencode($token));
    exit;
}

$mysqli->close();
?>
