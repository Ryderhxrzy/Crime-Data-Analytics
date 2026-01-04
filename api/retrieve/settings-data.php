<?php
/**
 * Settings Data Controller
 * Retrieves user settings and profile information
 */
session_start();
// Authentication check
require_once '../../api/middleware/auth.php';
require_once '../../api/config.php';
require_once '../../api/helpers/url-helper.php';

// Get user ID from session
$user_id = $_SESSION['user']['id'] ?? null;

if (!$user_id) {
    header('Location: ../../index.php');
    exit;
}

// Fetch user information
$stmt = $mysqli->prepare("SELECT id, email, full_name, profile_picture, role, registration_type FROM crime_department_admin_users WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$stmt->close();

if (!$user_data) {
    header('Location: ../../index.php');
    exit;
}

// Fetch additional information
$stmt = $mysqli->prepare("SELECT phone_number, address, department, position, bio FROM crime_department_admin_information WHERE admin_user_id = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$additional_info = $result->fetch_assoc();
$stmt->close();

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

// Fetch user settings from database
$settings_stmt = $mysqli->prepare("SELECT email_notifications, push_notifications, crime_alerts, weekly_reports, system_updates, two_factor_auth, theme, language, timezone FROM crime_department_user_settings WHERE admin_user_id = ? LIMIT 1");
$settings_stmt->bind_param("i", $user_id);
$settings_stmt->execute();
$settings_result = $settings_stmt->get_result();
$user_settings = $settings_result->fetch_assoc();
$settings_stmt->close();

// If no settings exist, create default settings
if (!$user_settings) {
    $default_stmt = $mysqli->prepare("INSERT INTO crime_department_user_settings (admin_user_id, email_notifications, push_notifications, crime_alerts, weekly_reports, system_updates, two_factor_auth, theme, language, timezone) VALUES (?, 1, 1, 1, 1, 1, 0, 'light', 'en', 'UTC')");
    $default_stmt->bind_param("i", $user_id);
    $default_stmt->execute();
    $default_stmt->close();

    // Fetch the newly created settings
    $settings_stmt = $mysqli->prepare("SELECT email_notifications, push_notifications, crime_alerts, weekly_reports, system_updates, two_factor_auth, theme, language, timezone FROM crime_department_user_settings WHERE admin_user_id = ? LIMIT 1");
    $settings_stmt->bind_param("i", $user_id);
    $settings_stmt->execute();
    $settings_result = $settings_stmt->get_result();
    $user_settings = $settings_result->fetch_assoc();
    $settings_stmt->close();
}

// Check if production environment
$is_production = isProduction();
$php_ext = $is_production ? '' : '.php';
