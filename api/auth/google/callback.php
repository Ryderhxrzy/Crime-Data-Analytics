<?php
/**
 * Google OAuth Callback Handler
 *
 * Processes the OAuth callback from Google and authenticates the user
 */

// Start session
session_start();

// Include required files
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/GoogleOAuth.php';
require_once __DIR__ . '/../../config.php';

try {
    // Check for error in callback
    if (isset($_GET['error'])) {
        throw new Exception('Google OAuth error: ' . $_GET['error']);
    }

    // Check for required parameters
    if (!isset($_GET['code']) || !isset($_GET['state'])) {
        throw new Exception('Missing required OAuth parameters');
    }

    // Initialize Google OAuth
    $googleOAuth = new GoogleOAuth();

    // Verify state to prevent CSRF attacks
    if (!$googleOAuth->verifyState($_GET['state'])) {
        throw new Exception('Invalid OAuth state - possible CSRF attack');
    }

    // Exchange authorization code for access token
    $tokenResponse = $googleOAuth->getAccessToken($_GET['code']);

    if (!isset($tokenResponse['access_token'])) {
        throw new Exception('Failed to obtain access token');
    }

    // Get user information from Google
    $userInfo = $googleOAuth->getUserInfo($tokenResponse['access_token']);

    // Extract user data
    $email = $userInfo['email'] ?? null;
    $fullName = $userInfo['name'] ?? '';
    $googleId = $userInfo['id'] ?? null;
    $profilePicture = $userInfo['picture'] ?? null;

    if (!$email) {
        throw new Exception('Email not provided by Google');
    }

    // Check if user exists in crime_department_admin_users table
    $stmt = $mysqli->prepare("SELECT id, email, role, status, account_status, registration_type FROM crime_department_admin_users WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user) {
        // User exists - check account status
        if ($user['status'] !== 'active') {
            throw new Exception('Your account is ' . $user['status'] . '. Please contact administrator.');
        }

        if ($user['account_status'] !== 'verified') {
            throw new Exception('Your account is not verified. Please contact administrator.');
        }

        // Update last login
        $stmt = $mysqli->prepare("UPDATE crime_department_admin_users SET last_login = NOW() WHERE id = ?");
        $stmt->bind_param('i', $user['id']);
        $stmt->execute();
        $stmt->close();

        // Set session with Google profile data
        $_SESSION['user'] = [
            'id' => $user['id'],
            'email' => $email,
            'full_name' => $fullName,  // Use name from Google
            'role' => $user['role'],
            'profile_picture' => $profilePicture,  // Use picture from Google
            'login_method' => 'google'
        ];
        $_SESSION['last_activity'] = time();

        // Success - redirect to system overview
        $systemOverviewUrl = getRedirectUrl('frontend/admin-page/system-overview.php');
        header("Location: $systemOverviewUrl");
        exit;

    } else {
        // User does not exist - show error message
        throw new Exception('Account not found. Please contact administrator to create your account.');
    }

} catch (Exception $e) {
    // Log error
    error_log('Google OAuth Callback Error: ' . $e->getMessage());

    // Set error message in session
    $_SESSION['flash_error'] = 'Google login failed: ' . $e->getMessage();

    // Redirect back to login page
    $loginUrl = getRedirectUrl('index.php');
    header("Location: $loginUrl");
    exit;
}
