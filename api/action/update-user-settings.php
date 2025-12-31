<?php
session_start();
require_once '../config.php';

// Check if user is logged in
if (!isset($_SESSION['user']['id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user']['id'];

// Get user registration type to check for 2FA eligibility
$user_stmt = $mysqli->prepare("SELECT registration_type FROM crime_department_admin_users WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user_data = $user_result->fetch_assoc();
$user_stmt->close();

if (!$user_data) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}

// Get POST data
$setting_type = $_POST['type'] ?? '';

try {
    if ($setting_type === 'notifications') {
        // Update notification settings
        $email_notifications = isset($_POST['email_notifications']) ? 1 : 0;
        $push_notifications = isset($_POST['push_notifications']) ? 1 : 0;
        $crime_alerts = isset($_POST['crime_alerts']) ? 1 : 0;
        $weekly_reports = isset($_POST['weekly_reports']) ? 1 : 0;
        $system_updates = isset($_POST['system_updates']) ? 1 : 0;

        // Check if settings exist
        $check_stmt = $mysqli->prepare("SELECT id FROM crime_department_user_settings WHERE admin_user_id = ?");
        $check_stmt->bind_param("i", $user_id);
        $check_stmt->execute();
        $exists = $check_stmt->get_result()->num_rows > 0;
        $check_stmt->close();

        if ($exists) {
            $stmt = $mysqli->prepare("UPDATE crime_department_user_settings SET email_notifications = ?, push_notifications = ?, crime_alerts = ?, weekly_reports = ?, system_updates = ? WHERE admin_user_id = ?");
            $stmt->bind_param("iiiiii", $email_notifications, $push_notifications, $crime_alerts, $weekly_reports, $system_updates, $user_id);
        } else {
            $stmt = $mysqli->prepare("INSERT INTO crime_department_user_settings (admin_user_id, email_notifications, push_notifications, crime_alerts, weekly_reports, system_updates) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iiiiii", $user_id, $email_notifications, $push_notifications, $crime_alerts, $weekly_reports, $system_updates);
        }

        if ($stmt->execute()) {
            $stmt->close();

            // Log activity
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
            $description = "Notification settings updated";

            $log_stmt = $mysqli->prepare("INSERT INTO crime_department_activity_logs (admin_user_id, activity_type, description, ip_address, user_agent) VALUES (?, 'notification_update', ?, ?, ?)");
            $log_stmt->bind_param("isss", $user_id, $description, $ip_address, $user_agent);
            $log_stmt->execute();
            $log_stmt->close();

            echo json_encode(['success' => true, 'message' => 'Notification settings updated successfully']);
        } else {
            throw new Exception('Failed to update notification settings');
        }

    } elseif ($setting_type === 'preferences') {
        // Update preference settings
        $theme = trim($_POST['theme'] ?? 'light');
        $language = trim($_POST['language'] ?? 'en');
        $timezone = trim($_POST['timezone'] ?? 'UTC');

        // Validate theme
        if (!in_array($theme, ['light', 'dark', 'auto'])) {
            $theme = 'light';
        }

        // Check if settings exist
        $check_stmt = $mysqli->prepare("SELECT id FROM crime_department_user_settings WHERE admin_user_id = ?");
        $check_stmt->bind_param("i", $user_id);
        $check_stmt->execute();
        $exists = $check_stmt->get_result()->num_rows > 0;
        $check_stmt->close();

        if ($exists) {
            $stmt = $mysqli->prepare("UPDATE crime_department_user_settings SET theme = ?, language = ?, timezone = ? WHERE admin_user_id = ?");
            $stmt->bind_param("sssi", $theme, $language, $timezone, $user_id);
        } else {
            $stmt = $mysqli->prepare("INSERT INTO crime_department_user_settings (admin_user_id, theme, language, timezone) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $user_id, $theme, $language, $timezone);
        }

        if ($stmt->execute()) {
            $stmt->close();

            // Log activity
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
            $description = "Preference settings updated (Theme: $theme, Language: $language, Timezone: $timezone)";

            $log_stmt = $mysqli->prepare("INSERT INTO crime_department_activity_logs (admin_user_id, activity_type, description, ip_address, user_agent) VALUES (?, 'preference_update', ?, ?, ?)");
            $log_stmt->bind_param("isss", $user_id, $description, $ip_address, $user_agent);
            $log_stmt->execute();
            $log_stmt->close();

            echo json_encode(['success' => true, 'message' => 'Preference settings updated successfully']);
        } else {
            throw new Exception('Failed to update preference settings');
        }

    } elseif ($setting_type === '2fa') {
        // Check if user is email-registered
        if ($user_data['registration_type'] !== 'email') {
            echo json_encode(['success' => false, 'message' => 'Two-factor authentication is only available for email-registered accounts']);
            exit;
        }

        $two_factor_auth = isset($_POST['two_factor_auth']) ? 1 : 0;

        // Check if settings exist
        $check_stmt = $mysqli->prepare("SELECT id FROM crime_department_user_settings WHERE admin_user_id = ?");
        $check_stmt->bind_param("i", $user_id);
        $check_stmt->execute();
        $exists = $check_stmt->get_result()->num_rows > 0;
        $check_stmt->close();

        if ($exists) {
            $stmt = $mysqli->prepare("UPDATE crime_department_user_settings SET two_factor_auth = ? WHERE admin_user_id = ?");
            $stmt->bind_param("ii", $two_factor_auth, $user_id);
        } else {
            $stmt = $mysqli->prepare("INSERT INTO crime_department_user_settings (admin_user_id, two_factor_auth) VALUES (?, ?)");
            $stmt->bind_param("ii", $user_id, $two_factor_auth);
        }

        if ($stmt->execute()) {
            $stmt->close();

            // Log activity
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
            $description = $two_factor_auth ? "Two-factor authentication enabled" : "Two-factor authentication disabled";

            $log_stmt = $mysqli->prepare("INSERT INTO crime_department_activity_logs (admin_user_id, activity_type, description, ip_address, user_agent) VALUES (?, 'security_update', ?, ?, ?)");
            $log_stmt->bind_param("isss", $user_id, $description, $ip_address, $user_agent);
            $log_stmt->execute();
            $log_stmt->close();

            echo json_encode([
                'success' => true,
                'message' => $two_factor_auth ? 'Two-factor authentication enabled successfully' : 'Two-factor authentication disabled successfully'
            ]);
        } else {
            throw new Exception('Failed to update 2FA settings');
        }

    } elseif ($setting_type === 'theme') {
        // Update theme setting
        $theme = trim($_POST['theme'] ?? 'light');

        // Validate theme
        if (!in_array($theme, ['light', 'dark', 'auto'])) {
            $theme = 'light';
        }

        // Check if settings exist
        $check_stmt = $mysqli->prepare("SELECT id FROM crime_department_user_settings WHERE admin_user_id = ?");
        $check_stmt->bind_param("i", $user_id);
        $check_stmt->execute();
        $exists = $check_stmt->get_result()->num_rows > 0;
        $check_stmt->close();

        if ($exists) {
            $stmt = $mysqli->prepare("UPDATE crime_department_user_settings SET theme = ? WHERE admin_user_id = ?");
            $stmt->bind_param("si", $theme, $user_id);
        } else {
            $stmt = $mysqli->prepare("INSERT INTO crime_department_user_settings (admin_user_id, theme) VALUES (?, ?)");
            $stmt->bind_param("is", $user_id, $theme);
        }

        if ($stmt->execute()) {
            $stmt->close();

            // Log activity
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
            $description = "Theme changed to " . $theme;

            $log_stmt = $mysqli->prepare("INSERT INTO crime_department_activity_logs (admin_user_id, activity_type, description, ip_address, user_agent) VALUES (?, 'theme_change', ?, ?, ?)");
            $log_stmt->bind_param("isss", $user_id, $description, $ip_address, $user_agent);
            $log_stmt->execute();
            $log_stmt->close();

            echo json_encode(['success' => true, 'message' => 'Theme updated successfully']);
        } else {
            throw new Exception('Failed to update theme');
        }

    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid setting type']);
    }

} catch (Exception $e) {
    error_log("Update settings error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while updating settings']);
}

$mysqli->close();
?>
