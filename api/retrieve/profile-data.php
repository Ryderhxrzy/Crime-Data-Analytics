<?php
/**
 * Profile Data Controller
 * Retrieves user profile information and additional details
 */

// Authentication check
require_once '../middleware/auth.php';
require_once '../config.php';

// Get user ID from session
$user_id = $_SESSION['user']['id'] ?? null;

if (!$user_id) {
    header('Location: ../../index.php');
    exit;
}

// Fetch complete user information from database
$stmt = $mysqli->prepare("SELECT id, email, password, full_name, profile_picture, role, registration_type, status, account_status, last_login, created_at, updated_at FROM crime_department_admin_users WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$stmt->close();

if (!$user_data) {
    header('Location: ../../index.php');
    exit;
}

// Fetch additional information from crime_department_admin_information table
$stmt = $mysqli->prepare("SELECT phone_number, address, department, position, bio FROM crime_department_admin_information WHERE admin_user_id = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$additional_info = $result->fetch_assoc();
$stmt->close();

// Check if user has incomplete profile (any field is empty)
$has_incomplete_profile = false;
if (!$additional_info ||
    empty($additional_info['phone_number']) ||
    empty($additional_info['address']) ||
    empty($additional_info['department']) ||
    empty($additional_info['position']) ||
    empty($additional_info['bio'])) {
    $has_incomplete_profile = true;
}

// Set profile picture path - handle both Google URLs and local paths
$profile_picture_data = $user_data['profile_picture'] ?? '';
if (!empty($profile_picture_data)) {
    // Check if it's a Google profile picture URL (starts with http:// or https://)
    if (preg_match('/^https?:\/\//', $profile_picture_data)) {
        $profile_picture = $profile_picture_data; // Use Google URL directly
    } else {
        // Local file path
        $profile_picture = '../image/profile/' . $profile_picture_data;
    }
} else {
    // Default to UI Avatars
    $profile_picture = 'https://ui-avatars.com/api/?name=' . urlencode($user_data['full_name']) . '&background=4c8a89&color=fff&size=256';
}
