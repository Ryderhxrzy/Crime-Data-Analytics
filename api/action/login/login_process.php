<?php
require_once '../../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../../../index');
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    $redirect_url = url('../../../index') . '?error=' . urlencode('Email and password are required');
    header('Location: ' . $redirect_url);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $redirect_url = url('../../../index') . '?error=' . urlencode('Invalid email format');
    header('Location: ' . $redirect_url);
    exit;
}

if ($mysqli->connect_error) {
    $redirect_url = url('../../../index') . '?error=' . urlencode('Database connection failed');
    header('Location: ' . $redirect_url);
    exit;
}

try {
    $stmt = $mysqli->prepare("SELECT id, email, password, full_name, role, status, account_status, registration_type FROM crime_department_admin_users WHERE email = ? LIMIT 1");

    if (!$stmt) {
        throw new Exception("Database preparation failed: " . $mysqli->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $redirect_url = url('../../../index') . '?error=' . urlencode('Account not found. Please contact administrator.');
        header('Location: ' . $redirect_url);
        exit;
    }

    $user = $result->fetch_assoc();

    // Check if account is verified
    if ($user['account_status'] === 'unverified') {
        $redirect_url = url('../../../index') . '?error=' . urlencode('Your account is not verified. Please contact the super admin to verify your account.');
        header('Location: ' . $redirect_url);
        exit;
    }

    // Check registration type - only allow email login for email-registered accounts
    if ($user['registration_type'] === 'google') {
        $redirect_url = url('../../../index') . '?error=' . urlencode('This account is registered with Google. Please use Google Sign-In.');
        header('Location: ' . $redirect_url);
        exit;
    }

    // Check password (should not be null for email-registered accounts)
    if ($user['password'] === null) {
        $redirect_url = url('../../../index') . '?error=' . urlencode('Please use Google Sign-In for this account.');
        header('Location: ' . $redirect_url);
        exit;
    }

    if (!password_verify($password, $user['password'])) {
        // Get user IP address and user agent for failed login log
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;

        // Log failed login attempt without user_id (since credentials are wrong)
        $description = "Failed login attempt - incorrect password for email: " . $email;

        // Try direct insert without prepared statement to test
        try {
            $escaped_description = $mysqli->real_escape_string($description);
            $escaped_ip = $ip_address ? "'" . $mysqli->real_escape_string($ip_address) . "'" : "NULL";
            $escaped_agent = $user_agent ? "'" . $mysqli->real_escape_string($user_agent) . "'" : "NULL";

            $sql = "INSERT INTO crime_department_activity_logs (admin_user_id, activity_type, description, ip_address, user_agent)
                    VALUES (NULL, 'failed_login', '$escaped_description', $escaped_ip, $escaped_agent)";

            if (!$mysqli->query($sql)) {
                error_log("Failed login log error: " . $mysqli->error);
            } else {
                error_log("Failed login logged successfully. Insert ID: " . $mysqli->insert_id);
            }
        } catch (Exception $e) {
            error_log("Failed login log exception: " . $e->getMessage());
        }

        $redirect_url = url('../../../index') . '?error=' . urlencode('Invalid email or password');
        header('Location: ' . $redirect_url);
        exit;
    }

    $stmt->close();

    // Update user status to 'active' and last_login timestamp
    $update_stmt = $mysqli->prepare("UPDATE crime_department_admin_users SET status = 'active', last_login = NOW() WHERE id = ?");
    $update_stmt->bind_param("i", $user['id']);
    $update_stmt->execute();
    $update_stmt->close();

    // Get user IP address and user agent
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;

    // Insert activity log for login
    $log_stmt = $mysqli->prepare("INSERT INTO crime_department_activity_logs (admin_user_id, activity_type, description, ip_address, user_agent) VALUES (?, 'login', 'User logged in via email/password', ?, ?)");
    $log_stmt->bind_param("iss", $user['id'], $ip_address, $user_agent);
    $log_stmt->execute();
    $log_stmt->close();

    // Regenerate session ID for security
    session_regenerate_id(true);

    // Store user information in session
    $_SESSION['user'] = [
        'id' => $user['id'],
        'email' => $user['email'],
        'full_name' => $user['full_name'],
        'role' => $user['role']
    ];

    // Set session activity timestamps
    $_SESSION['last_activity'] = time();
    $_SESSION['session_created'] = time();

    // Store user IP and user agent for security
    $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'] ?? '';
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';

    // Set session cookie parameters for security (1 hour)
    $cookie_params = session_get_cookie_params();
    setcookie(
        session_name(),
        session_id(),
        time() + 3600, // 1 hour expiry
        $cookie_params['path'],
        $cookie_params['domain'],
        true, // Secure - requires HTTPS in production
        true  // HttpOnly - prevents JavaScript access
    );

    // Redirect to system overview page
    redirect('../../../frontend/admin-page/system-overview');

} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    $redirect_url = url('../../../index') . '?error=' . urlencode('Login failed. Please try again.');
    header('Location: ' . $redirect_url);
    exit;
}

$mysqli->close();
?>
