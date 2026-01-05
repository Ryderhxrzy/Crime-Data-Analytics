<?php
/**
 * Login Page Data Controller
 * Handles session validation and flash message retrieval for login page
 */

session_start();

// Check if user is already logged in
if (isset($_SESSION['user']) && isset($_SESSION['last_activity'])) {
    // Check if session is still valid (not expired)
    $session_timeout = 3600; // 1 hour
    if ((time() - $_SESSION['last_activity']) <= $session_timeout) {
        // Update last activity
        $_SESSION['last_activity'] = time();

        // Redirect to system overview page
        header('Location: frontend/admin-page/system-overview.php');
        exit;
    }
}

// Get flash messages from session
$flash_error = $_SESSION['flash_error'] ?? null;
$flash_success = $_SESSION['flash_success'] ?? null;

// Clear flash messages
unset($_SESSION['flash_error']);
unset($_SESSION['flash_success']);
