<?php
/**
 * Logout Script
 *
 * This script handles user logout by:
 * 1. Destroying the current session
 * 2. Clearing all session data
 * 3. Removing session cookies
 * 4. Redirecting to login page
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get user info before destroying session (for logging purposes if needed)
$user_email = $_SESSION['user']['email'] ?? 'unknown';

// Unset all session variables
$_SESSION = array();

// Delete the session cookie if it exists
if (isset($_COOKIE[session_name()])) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// Destroy the session
session_destroy();

// Optional: Log the logout event
error_log("User logged out: " . $user_email);

// Redirect to login page with success message
header('Location: ../../index.php?success=You have been logged out successfully');
exit;
?>