<?php
/**
 * Verify OTP Script
 * Verifies the OTP and generates a password reset token
 */

session_start();
require_once '../../config.php';
require_once '../../utils/mailer.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$email = trim($_POST['email'] ?? '');
$otp = trim($_POST['otp'] ?? '');

if (empty($email) || empty($otp)) {
    echo json_encode(['success' => false, 'message' => 'Email and OTP are required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

if (strlen($otp) !== 6) {
    echo json_encode(['success' => false, 'message' => 'OTP must be 6 digits']);
    exit;
}

if ($mysqli->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

try {
    // Get user and verify OTP
    $stmt = $mysqli->prepare("
        SELECT o.id, o.admin_user_id, o.otp_code, o.expires_at, o.is_used, u.email, u.registration_type
        FROM crime_department_otp_verification o
        JOIN crime_department_admin_users u ON o.admin_user_id = u.id
        WHERE u.email = ? AND o.is_used = 0
        ORDER BY o.created_at DESC
        LIMIT 1
    ");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'No active OTP found. Please request a new one.']);
        $stmt->close();
        exit;
    }

    $otp_data = $result->fetch_assoc();
    $stmt->close();

    // Check if OTP has expired
    if (strtotime($otp_data['expires_at']) < time()) {
        echo json_encode(['success' => false, 'message' => 'OTP has expired. Please request a new one.']);
        exit;
    }

    // Verify OTP
    if ($otp !== $otp_data['otp_code']) {
        // Increment attempt count
        $attempt_stmt = $mysqli->prepare("UPDATE crime_department_otp_verification SET attempt_count = attempt_count + 1, last_attempt_at = NOW() WHERE id = ?");
        $attempt_stmt->bind_param("i", $otp_data['id']);
        $attempt_stmt->execute();
        $attempt_stmt->close();

        // Log failed attempt
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        $description = "Failed OTP verification attempt for email: " . $email;
        $log_stmt = $mysqli->prepare("INSERT INTO crime_department_activity_logs (admin_user_id, activity_type, description, ip_address, user_agent) VALUES (?, 'password_reset_otp_failed', ?, ?, ?)");
        $log_stmt->bind_param("isss", $otp_data['admin_user_id'], $description, $ip_address, $user_agent);
        $log_stmt->execute();
        $log_stmt->close();

        echo json_encode(['success' => false, 'message' => 'Invalid OTP. Please try again.']);
        exit;
    }

    // Mark OTP as used
    $mark_used_stmt = $mysqli->prepare("UPDATE crime_department_otp_verification SET is_used = 1 WHERE id = ?");
    $mark_used_stmt->bind_param("i", $otp_data['id']);
    $mark_used_stmt->execute();
    $mark_used_stmt->close();

    // Generate password reset token
    $reset_token = bin2hex(random_bytes(32));
    $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // Delete any existing unused reset tokens for this user
    $delete_stmt = $mysqli->prepare("DELETE FROM crime_department_password_resets WHERE admin_user_id = ? AND is_used = 0");
    $delete_stmt->bind_param("i", $otp_data['admin_user_id']);
    $delete_stmt->execute();
    $delete_stmt->close();

    // Insert new reset token
    $insert_stmt = $mysqli->prepare("INSERT INTO crime_department_password_resets (admin_user_id, reset_token, expires_at) VALUES (?, ?, ?)");
    $insert_stmt->bind_param("iss", $otp_data['admin_user_id'], $reset_token, $expires_at);
    $insert_stmt->execute();
    $insert_stmt->close();

    // Get user full name for email
    $user_stmt = $mysqli->prepare("SELECT full_name FROM crime_department_admin_users WHERE id = ?");
    $user_stmt->bind_param("i", $otp_data['admin_user_id']);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    $user_data = $user_result->fetch_assoc();
    $user_stmt->close();

    // Send reset link email
    try {
        $mailer = new Mailer();
        $mailer->sendResetLink($email, $user_data['full_name'] ?? 'User', $reset_token);
    } catch (Exception $e) {
        error_log("Failed to send reset link email: " . $e->getMessage());
        // Continue anyway - user can still use the token
    }

    // Log activity
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
    $description = "Password reset OTP verified and reset link sent for email: " . $email;
    $log_stmt = $mysqli->prepare("INSERT INTO crime_department_activity_logs (admin_user_id, activity_type, description, ip_address, user_agent) VALUES (?, 'password_reset_otp_verified', ?, ?, ?)");
    $log_stmt->bind_param("isss", $otp_data['admin_user_id'], $description, $ip_address, $user_agent);
    $log_stmt->execute();
    $log_stmt->close();

    echo json_encode([
        'success' => true,
        'message' => 'OTP verified successfully! Check your email for the password reset link.',
        'token' => $reset_token
    ]);

} catch (Exception $e) {
    error_log("Verify OTP error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
}

$mysqli->close();
?>
