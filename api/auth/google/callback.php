<?php
/**
 * Google OAuth Callback Handler
 *
 * Processes the OAuth callback from Google and authenticates the user
 */

// Start session
session_start();

// Include required files
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

    // Check if user already exists in database
    $stmt = $mysqli->prepare("SELECT id, email, full_name, role, status FROM admin_users WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user) {
        // User exists - check if account is active
        if ($user['status'] !== 'active') {
            throw new Exception('Your account is ' . $user['status'] . '. Please contact the administrator.');
        }

        // Update last login
        $stmt = $mysqli->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
        $stmt->bind_param('i', $user['id']);
        $stmt->execute();
        $stmt->close();

        // Set session variables
        $_SESSION['user'] = [
            'id' => $user['id'],
            'email' => $user['email'],
            'full_name' => $user['full_name'],
            'role' => $user['role'],
            'login_method' => 'google'
        ];
        $_SESSION['last_activity'] = time();

        // Success - redirect to dashboard
        header('Location: ../../../frontend/admin-page/dashboard.php');
        exit;

    } else {
        // New user - create account
        // For security, you might want to restrict who can create accounts via Google
        // Uncomment the line below to prevent auto-registration
        // throw new Exception('No account found with this email. Please contact the administrator.');

        // Create new user account (default role: admin)
        $stmt = $mysqli->prepare("INSERT INTO admin_users (email, password, full_name, role, status, last_login) VALUES (?, ?, ?, 'admin', 'active', NOW())");

        // Generate a random password (user won't need it as they login via Google)
        $randomPassword = password_hash(bin2hex(random_bytes(32)), PASSWORD_DEFAULT);

        $stmt->bind_param('sss', $email, $randomPassword, $fullName);

        if (!$stmt->execute()) {
            throw new Exception('Failed to create user account');
        }

        $userId = $stmt->insert_id;
        $stmt->close();

        // Set session variables
        $_SESSION['user'] = [
            'id' => $userId,
            'email' => $email,
            'full_name' => $fullName,
            'role' => 'admin',
            'login_method' => 'google'
        ];
        $_SESSION['last_activity'] = time();

        // Success - redirect to dashboard
        $_SESSION['flash_success'] = 'Welcome! Your account has been created successfully.';
        header('Location: ../../../frontend/admin-page/dashboard.php');
        exit;
    }

} catch (Exception $e) {
    // Log error
    error_log('Google OAuth Callback Error: ' . $e->getMessage());

    // Set error message in session
    $_SESSION['flash_error'] = 'Google login failed: ' . $e->getMessage();

    // Redirect back to login page
    header('Location: ../../../index.php');
    exit;
}
