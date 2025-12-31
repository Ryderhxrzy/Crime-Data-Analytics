<?php
session_start();
require_once '../config.php';

// Check if user is logged in
if (!isset($_SESSION['user']['id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user']['id'];

// Check if file was uploaded
if (!isset($_FILES['profile_picture']) || $_FILES['profile_picture']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
    exit;
}

$file = $_FILES['profile_picture'];
$file_name = $file['name'];
$file_tmp = $file['tmp_name'];
$file_size = $file['size'];
$file_type = $file['type'];

// Validate file type
$allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
if (!in_array($file_type, $allowed_types)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed']);
    exit;
}

// Validate file size (max 5MB)
if ($file_size > 5 * 1024 * 1024) {
    echo json_encode(['success' => false, 'message' => 'File size must be less than 5MB']);
    exit;
}

// Get file extension
$file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

// Generate unique filename: userid_timestamp.extension
$new_filename = $user_id . '_' . time() . '.' . $file_ext;

// Define upload directory - use absolute path for production compatibility
$upload_dir = __DIR__ . '/../../frontend/image/profile/';

// Create directory if it doesn't exist
if (!file_exists($upload_dir)) {
    if (!mkdir($upload_dir, 0775, true)) {
        error_log("Failed to create upload directory: " . $upload_dir);
        echo json_encode(['success' => false, 'message' => 'Failed to create upload directory']);
        exit;
    }
}

// Check if directory is writable
if (!is_writable($upload_dir)) {
    error_log("Upload directory is not writable: " . $upload_dir);
    echo json_encode(['success' => false, 'message' => 'Upload directory is not writable. Please check permissions.']);
    exit;
}

$upload_path = $upload_dir . $new_filename;

// Get old profile picture to delete it
$stmt = $mysqli->prepare("SELECT profile_picture FROM crime_department_admin_users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$old_data = $result->fetch_assoc();
$stmt->close();

// Move uploaded file
if (move_uploaded_file($file_tmp, $upload_path)) {
    // Set proper permissions on uploaded file
    chmod($upload_path, 0644);

    // Update database with only filename
    $stmt = $mysqli->prepare("UPDATE crime_department_admin_users SET profile_picture = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("si", $new_filename, $user_id);

    if ($stmt->execute()) {
        $stmt->close();

        // Delete old profile picture if it exists and is not a default avatar
        if (!empty($old_data['profile_picture']) && !preg_match('/^https?:\/\//', $old_data['profile_picture'])) {
            $old_file_path = $upload_dir . $old_data['profile_picture'];
            if (file_exists($old_file_path)) {
                @unlink($old_file_path);
            }
        }

        // Log activity
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        $description = "Profile picture updated";

        $log_stmt = $mysqli->prepare("INSERT INTO crime_department_activity_logs (admin_user_id, activity_type, description, ip_address, user_agent) VALUES (?, 'profile_update', ?, ?, ?)");
        $log_stmt->bind_param("isss", $user_id, $description, $ip_address, $user_agent);
        $log_stmt->execute();
        $log_stmt->close();

        echo json_encode([
            'success' => true,
            'message' => 'Profile picture uploaded successfully',
            'filename' => $new_filename,
            'url' => '../image/profile/' . $new_filename
        ]);
    } else {
        // Failed to update database, delete uploaded file
        @unlink($upload_path);
        error_log("Database update failed for user $user_id: " . $stmt->error);
        echo json_encode(['success' => false, 'message' => 'Database update failed']);
    }
} else {
    // Log detailed error information
    $upload_error = error_get_last();
    error_log("File upload failed for user $user_id. Temp: $file_tmp, Target: $upload_path, Error: " . json_encode($upload_error));
    error_log("Directory writable: " . (is_writable($upload_dir) ? 'yes' : 'no'));
    error_log("Directory exists: " . (file_exists($upload_dir) ? 'yes' : 'no'));
    error_log("Temp file exists: " . (file_exists($file_tmp) ? 'yes' : 'no'));

    echo json_encode(['success' => false, 'message' => 'Failed to upload file. Please check server permissions.']);
}

$mysqli->close();
?>