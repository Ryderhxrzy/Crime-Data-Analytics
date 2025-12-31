<?php
session_start();
require_once '../config.php';

// Check if user is logged in
if (!isset($_SESSION['user']['id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user']['id'];

// Get POST data
$full_name = trim($_POST['full_name'] ?? '');
$phone_number = trim($_POST['phone_number'] ?? '');
$address = trim($_POST['address'] ?? '');
$department = trim($_POST['department'] ?? '');
$position = trim($_POST['position'] ?? '');
$bio = trim($_POST['bio'] ?? '');

// Validate required fields
if (empty($full_name)) {
    echo json_encode(['success' => false, 'message' => 'Full name is required']);
    exit;
}

try {
    // Update full name in main users table
    $stmt = $mysqli->prepare("UPDATE crime_department_admin_users SET full_name = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("si", $full_name, $user_id);

    if (!$stmt->execute()) {
        throw new Exception("Failed to update full name");
    }
    $stmt->close();

    // Update session with new full name
    $_SESSION['user']['full_name'] = $full_name;

    // Check if additional info record exists
    $stmt = $mysqli->prepare("SELECT admin_user_id FROM crime_department_admin_information WHERE admin_user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $exists = $result->num_rows > 0;
    $stmt->close();

    if ($exists) {
        // Update existing record
        $stmt = $mysqli->prepare("UPDATE crime_department_admin_information SET phone_number = ?, address = ?, department = ?, position = ?, bio = ? WHERE admin_user_id = ?");
        $stmt->bind_param("sssssi", $phone_number, $address, $department, $position, $bio, $user_id);
    } else {
        // Insert new record
        $stmt = $mysqli->prepare("INSERT INTO crime_department_admin_information (admin_user_id, phone_number, address, department, position, bio) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $user_id, $phone_number, $address, $department, $position, $bio);
    }

    if (!$stmt->execute()) {
        throw new Exception("Failed to update additional information");
    }
    $stmt->close();

    // Log activity
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
    $description = "Account settings updated";

    $log_stmt = $mysqli->prepare("INSERT INTO crime_department_activity_logs (admin_user_id, activity_type, description, ip_address, user_agent) VALUES (?, 'profile_update', ?, ?, ?)");
    $log_stmt->bind_param("isss", $user_id, $description, $ip_address, $user_agent);
    $log_stmt->execute();
    $log_stmt->close();

    echo json_encode([
        'success' => true,
        'message' => 'Account settings updated successfully'
    ]);

} catch (Exception $e) {
    error_log("Update account settings error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while updating your settings'
    ]);
}

$mysqli->close();
?>
