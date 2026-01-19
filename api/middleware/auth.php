<?php
/**
 * Authentication Middleware
 *
 * This middleware protects pages by ensuring:
 * 1. User session is active
 * 2. Session has not expired
 * 3. User data exists in session
 * 4. Session is not hijacked (IP and User Agent validation)
 *
 * Features:
 * - Session timeout (1 hour)
 * - Session hijacking protection (IP and User Agent check)
 * - Automatic session regeneration (every 30 minutes)
 * - Helper functions for user data and role checking
 *
 * Usage: Include this file at the top of any protected page
 * From frontend/admin-page/: require_once '../../api/middleware/auth.php';
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuration
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds (change as needed)

/**
 * Check if user is authenticated
 */
function isAuthenticated() {
    return isset($_SESSION['user']) &&
           isset($_SESSION['user']['id']) &&
           isset($_SESSION['last_activity']);
}

/**
 * Check if session has expired
 */
function isSessionExpired() {
    if (!isset($_SESSION['last_activity'])) {
        return true;
    }

    $inactive_time = time() - $_SESSION['last_activity'];
    return $inactive_time > SESSION_TIMEOUT;
}

/**
 * Check for session hijacking attempts
 */
function isSessionHijacked() {
    $current_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

    // Note: IP check removed because mobile networks frequently change IP addresses
    // as users switch between cell towers, WiFi, or due to carrier NAT.
    // This was causing "Security violation detected" errors on mobile devices.

    // Check user agent (more reliable than IP for mobile)
    if (isset($_SESSION['user_agent']) && $_SESSION['user_agent'] !== $current_agent) {
        return true;
    }

    return false;
}

/**
 * Destroy session and redirect to login
 */
function destroySessionAndRedirect($message = 'Please login to continue') {
    // Store message before destroying session
    $redirect_message = $message;

    // Destroy session
    session_unset();
    session_destroy();

    // Start new session for flash message
    session_start();
    $_SESSION['flash_error'] = $redirect_message;

    // Redirect to login page (relative path from frontend/admin-page/)
    header('Location: ../../index.php');
    exit;
}

/**
 * Update last activity timestamp
 */
function updateLastActivity() {
    $_SESSION['last_activity'] = time();
}

// Main authentication check
if (!isAuthenticated()) {
    destroySessionAndRedirect('You must be logged in to access this page');
}

// Check session expiry
if (isSessionExpired()) {
    destroySessionAndRedirect('Your session has expired. Please login again');
}

// Check for session hijacking
if (isSessionHijacked()) {
    destroySessionAndRedirect('Security violation detected. Please login again');
}

// Update last activity time
updateLastActivity();

// Optional: Regenerate session ID periodically for security (every 30 minutes)
if (!isset($_SESSION['session_created'])) {
    $_SESSION['session_created'] = time();
} else if (time() - $_SESSION['session_created'] > 1800) { // 30 minutes
    session_regenerate_id(true);
    $_SESSION['session_created'] = time();
}

// Helper function to get current user data
function getCurrentUser() {
    return $_SESSION['user'] ?? null;
}

// Helper function to check user role
function hasRole($role) {
    return isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === $role;
}

// Helper function to check if user is admin
function isAdmin() {
    return hasRole('admin');
}

?>