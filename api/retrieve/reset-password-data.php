<?php
/**
 * Reset Password Data Controller
 * Validates reset token and retrieves user data
 */

session_start();
require_once '../../api/config.php';

// Get token from URL
$token = $_GET['token'] ?? '';

if (empty($token)) {
    $_SESSION['flash_error'] = 'Invalid reset link';
    header('Location: ../../index.php');
    exit;
}

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
    $_SESSION['flash_error'] = 'Invalid or expired reset link';
    $stmt->close();
    header('Location: ../../index.php');
    exit;
}

$reset_data = $result->fetch_assoc();
$stmt->close();

// Check if token has expired
if (strtotime($reset_data['expires_at']) < time()) {
    $_SESSION['flash_error'] = 'This reset link has expired. Please request a new one.';
    header('Location: ../../frontend/user-page/forgot-password.php');
    exit;
}

// Check if account is registered with email
if ($reset_data['registration_type'] !== 'email') {
    $_SESSION['flash_error'] = 'This account is registered with Google. Please use Google Sign-In.';
    header('Location: ../../index.php');
    exit;
}

// Get flash messages
$flash_error = $_SESSION['flash_error'] ?? null;
$flash_success = $_SESSION['flash_success'] ?? null;

// Clear flash messages
unset($_SESSION['flash_error']);
unset($_SESSION['flash_success']);
