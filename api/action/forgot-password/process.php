<?php
/**
 * Forgot Password Processing Script
 * Generates a password reset token and sends it to the user's email
 */

session_start();
require_once '../../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../../forgot-password.php');
    exit;
}

$email = trim($_POST['email'] ?? '');

if (empty($email)) {
    $_SESSION['flash_error'] = 'Email is required';
    header('Location: ../../../forgot-password.php');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['flash_error'] = 'Invalid email format';
    header('Location: ../../../forgot-password.php');
    exit;
}

if ($mysqli->connect_error) {
    $_SESSION['flash_error'] = 'Database connection failed';
    header('Location: ../../../forgot-password.php');
    exit;
}

try {
    // Check if user exists
    $stmt = $mysqli->prepare("SELECT id, email, registration_type, account_status FROM crime_department_admin_users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Don't reveal if email exists or not for security
        $_SESSION['flash_success'] = 'If an account with that email exists, we have sent a password reset link.';
        $stmt->close();
        header('Location: ../../../forgot-password.php');
        exit;
    }

    $user = $result->fetch_assoc();
    $stmt->close();

    // Check if account is verified
    if ($user['account_status'] === 'unverified') {
        $_SESSION['flash_error'] = 'Your account is not verified. Please contact the administrator.';
        header('Location: ../../../forgot-password.php');
        exit;
    }

    // Check if account is registered with email (not Google)
    if ($user['registration_type'] !== 'email') {
        $_SESSION['flash_error'] = 'This account is registered with Google. Please use Google Sign-In.';
        header('Location: ../../../forgot-password.php');
        exit;
    }

    // Generate a unique reset token
    $reset_token = bin2hex(random_bytes(32));
    $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // Delete any existing unused reset tokens for this user
    $delete_stmt = $mysqli->prepare("DELETE FROM crime_department_password_resets WHERE admin_user_id = ? AND is_used = 0");
    $delete_stmt->bind_param("i", $user['id']);
    $delete_stmt->execute();
    $delete_stmt->close();

    // Insert new reset token
    $insert_stmt = $mysqli->prepare("INSERT INTO crime_department_password_resets (admin_user_id, reset_token, expires_at) VALUES (?, ?, ?)");
    $insert_stmt->bind_param("iss", $user['id'], $reset_token, $expires_at);
    $insert_stmt->execute();
    $insert_stmt->close();

    // Create reset link
    $reset_link = "http://" . $_SERVER['HTTP_HOST'] . dirname(dirname(dirname($_SERVER['PHP_SELF']))) . "/reset-password.php?token=" . $reset_token;

    // Log activity
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
    $description = "Password reset requested for email: " . $email;
    $log_stmt = $mysqli->prepare("INSERT INTO crime_department_activity_logs (admin_user_id, activity_type, description, ip_address, user_agent) VALUES (?, 'password_reset_request', ?, ?, ?)");
    $log_stmt->bind_param("isss", $user['id'], $description, $ip_address, $user_agent);
    $log_stmt->execute();
    $log_stmt->close();

    // TODO: Send email with reset link
    // For now, we'll just display a success message
    // In production, integrate with an email service (PHPMailer, SendGrid, etc.)

    // For development/testing - log the reset link
    error_log("Password Reset Link for " . $email . ": " . $reset_link);

    $_SESSION['flash_success'] = 'Password reset link has been sent to your email address. Please check your inbox. (For testing: check error log)';
    header('Location: ../../../forgot-password.php');
    exit;

} catch (Exception $e) {
    error_log("Forgot password error: " . $e->getMessage());
    $_SESSION['flash_error'] = 'An error occurred. Please try again.';
    header('Location: ../../../forgot-password.php');
    exit;
}

$mysqli->close();
?>
