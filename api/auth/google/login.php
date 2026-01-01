<?php
/**
 * Google OAuth Login Handler
 *
 * Initiates the Google OAuth authentication flow
 */

// Start session
session_start();

// Include required files
require_once __DIR__ . '/../../helpers/url-helper.php';
require_once __DIR__ . '/GoogleOAuth.php';

try {
    // Initialize Google OAuth
    $googleOAuth = new GoogleOAuth();

    // Get authorization URL
    $authUrl = $googleOAuth->getAuthorizationUrl();

    // Redirect to Google OAuth
    header("Location: $authUrl");
    exit;

} catch (Exception $e) {
    // Log error
    error_log('Google OAuth Login Error: ' . $e->getMessage());

    // Redirect back to login with error
    $_SESSION['flash_error'] = 'Google login is not configured properly. Please contact the administrator.';
    $loginUrl = getRedirectUrl('index.php');
    header("Location: $loginUrl");
    exit;
}
