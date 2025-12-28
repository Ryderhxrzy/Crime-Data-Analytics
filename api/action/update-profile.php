<?php
/**
 * Update Profile API Endpoint
 *
 * Handles profile update requests
 */

session_start();
header('Content-Type: application/json');

require_once '../config.php';

// Check if user is logged in
if (!isset($_SESSION['user']['id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access'
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit;
}

$user_id = $_SESSION['user']['id'];
$full_name = trim($_POST['full_name'] ?? '');
$phone_number = trim($_POST['phone_number'] ?? '');
$address = trim($_POST['address'] ?? '');
$department = trim($_POST['department'] ?? '');
$position = trim($_POST['position'] ?? '');
$bio = trim($_POST['bio'] ?? '');

// Validate required fields
if (empty($full_name)) {
    echo json_encode([
        'success' => false,
        'message' => 'Full name is required'
    ]);
    exit;
}

try {
    // Begin transaction
    $mysqli->begin_transaction();

    // Update full_name in crime_department_admin_users table
    $stmt = $mysqli->prepare("UPDATE crime_department_admin_users SET full_name = ? WHERE id = ?");
    $stmt->bind_param("si", $full_name, $user_id);
    $stmt->execute();
    $stmt->close();

    // Check if record exists in crime_department_admin_information
    $check_stmt = $mysqli->prepare("SELECT id FROM crime_department_admin_information WHERE admin_user_id = ?");
    $check_stmt->bind_param("i", $user_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $exists = $result->num_rows > 0;
    $check_stmt->close();

    if ($exists) {
        // Update existing record
        $update_stmt = $mysqli->prepare("UPDATE crime_department_admin_information SET phone_number = ?, address = ?, department = ?, position = ?, bio = ? WHERE admin_user_id = ?");
        $update_stmt->bind_param("sssssi", $phone_number, $address, $department, $position, $bio, $user_id);
        $update_stmt->execute();
        $update_stmt->close();
    } else {
        // Insert new record
        $insert_stmt = $mysqli->prepare("INSERT INTO crime_department_admin_information (admin_user_id, phone_number, address, department, position, bio) VALUES (?, ?, ?, ?, ?, ?)");
        $insert_stmt->bind_param("isssss", $user_id, $phone_number, $address, $department, $position, $bio);
        $insert_stmt->execute();
        $insert_stmt->close();
    }

    // Commit transaction
    $mysqli->commit();

    // Update session with new full_name
    $_SESSION['user']['full_name'] = $full_name;

    echo json_encode([
        'success' => true,
        'message' => 'Profile updated successfully!'
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    $mysqli->rollback();
    error_log("Profile update error: " . $e->getMessage());

    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while updating your profile. Please try again.'
    ]);
}

$mysqli->close();
?>
