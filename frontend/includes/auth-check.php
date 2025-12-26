<?php
/**
 * Authentication Check
 * Include this at the top of all admin pages
 *
 * Usage: require_once '../includes/auth-check.php';
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user']) || !isset($_SESSION['last_activity'])) {
    // User not logged in, redirect to login page
    header('Location: ../../index.php');
    exit;
}

// Check session timeout (1 hour)
$session_timeout = 3600;
if (time() - $_SESSION['last_activity'] > $session_timeout) {
    // Session expired
    session_destroy();
    header('Location: ../../index.php?error=Session expired. Please login again.');
    exit;
}

// Update last activity timestamp
$_SESSION['last_activity'] = time();

// Optional: Check for session hijacking
$user_ip = $_SERVER['REMOTE_ADDR'] ?? '';
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

if (isset($_SESSION['user_ip']) && $_SESSION['user_ip'] !== $user_ip) {
    // IP address changed - potential session hijacking
    session_destroy();
    header('Location: ../../index.php?error=Security violation detected. Please login again.');
    exit;
}

if (isset($_SESSION['user_agent']) && $_SESSION['user_agent'] !== $user_agent) {
    // User agent changed - potential session hijacking
    session_destroy();
    header('Location: ../../index.php?error=Security violation detected. Please login again.');
    exit;
}
