<?php
/**
 * Send OTP Script
 * Generates and sends a 6-digit OTP to the user's email
 */

// Prevent any output before JSON response
ob_start();

session_start();

// Debug logging
error_log("=== SEND OTP START ===");
error_log("Request method: " . $_SERVER['REQUEST_METHOD']);
error_log("POST data: " . print_r($_POST, true));

require_once '../../config.php';
require_once '../../utils/mailer.php';

// Clean any previous output and set JSON header
ob_clean();
header('Content-Type: application/json');

error_log("Headers sent, JSON content type set");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$email = trim($_POST['email'] ?? '');

if (empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Email is required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

if ($mysqli->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

try {
    // Check if user exists
    $stmt = $mysqli->prepare("SELECT id, email, full_name, registration_type, account_status FROM crime_department_admin_users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Account not found. Please contact administrator.']);
        $stmt->close();
        exit;
    }

    $user = $result->fetch_assoc();
    $stmt->close();

    // Check if account is verified
    if ($user['account_status'] === 'unverified') {
        echo json_encode(['success' => false, 'message' => 'Your account is not verified. Please contact the administrator.']);
        exit;
    }

    // Check if account is registered with email (not Google)
    if ($user['registration_type'] !== 'email') {
        echo json_encode(['success' => false, 'message' => 'This account is registered with Google. Please use Google Sign-In.']);
        exit;
    }

    // Generate 6-digit OTP
    $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    $expires_at = date('Y-m-d H:i:s', strtotime('+10 minutes'));

    // Delete any existing unused OTPs for this user
    $delete_stmt = $mysqli->prepare("DELETE FROM crime_department_otp_verification WHERE admin_user_id = ? AND is_used = 0");
    $delete_stmt->bind_param("i", $user['id']);
    $delete_stmt->execute();
    $delete_stmt->close();

    // Insert new OTP
    $insert_stmt = $mysqli->prepare("INSERT INTO crime_department_otp_verification (admin_user_id, otp_code, expires_at) VALUES (?, ?, ?)");
    $insert_stmt->bind_param("iss", $user['id'], $otp, $expires_at);
    $insert_stmt->execute();
    $insert_stmt->close();

    // Send OTP email
    error_log("Attempting to send OTP email to: " . $email);
    try {
        $mailer = new Mailer();
        $mailer->sendOTP($email, $user['full_name'] ?? 'User', $otp);
        error_log("OTP email sent successfully");
    } catch (Exception $e) {
        error_log("Failed to send OTP email: " . $e->getMessage());
        error_log("Exception trace: " . $e->getTraceAsString());
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Failed to send OTP email. Please try again later.']);
        ob_end_flush();
        exit;
    }

    // Log activity
    error_log("Logging activity to database");
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
    $description = "Password reset OTP sent to email: " . $email;
    $log_stmt = $mysqli->prepare("INSERT INTO crime_department_activity_logs (admin_user_id, activity_type, description, ip_address, user_agent) VALUES (?, 'password_reset_otp_sent', ?, ?, ?)");
    $log_stmt->bind_param("isss", $user['id'], $description, $ip_address, $user_agent);
    $log_stmt->execute();
    $log_stmt->close();
    error_log("Activity logged successfully");

    // Ensure clean output
    error_log("Preparing success response");
    ob_clean();
    $response = [
        'success' => true,
        'message' => 'OTP has been sent to your email address. Please check your inbox.'
    ];
    $json = json_encode($response);
    error_log("JSON response: " . $json);
    echo $json;
    ob_end_flush();
    error_log("=== SEND OTP SUCCESS END ===");

} catch (Exception $e) {
    error_log("=== SEND OTP EXCEPTION ===");
    error_log("Send OTP error: " . $e->getMessage());
    error_log("Exception trace: " . $e->getTraceAsString());
    ob_clean();
    $errorResponse = ['success' => false, 'message' => 'An error occurred. Please try again.'];
    error_log("Error JSON response: " . json_encode($errorResponse));
    echo json_encode($errorResponse);
    ob_end_flush();
    error_log("=== SEND OTP EXCEPTION END ===");
}

$mysqli->close();
?>
