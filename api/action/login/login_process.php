<?php
require_once '../../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../../index.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    header('Location: ../../../index.php?error=Email and password are required');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../../../index.php?error=Invalid email format');
    exit;
}

if ($mysqli->connect_error) {
    header('Location: ../../../index.php?error=Database connection failed');
    exit;
}

try {
    $stmt = $mysqli->prepare("SELECT id, email, password, full_name, role FROM admin_users WHERE email = ? LIMIT 1");
    
    if (!$stmt) {
        throw new Exception("Database preparation failed: " . $mysqli->error);
    }
    
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        header('Location: ../../../index.php?error=Invalid email or password');
        exit;
    }
    
    $user = $result->fetch_assoc();
    
    if (!password_verify($password, $user['password'])) {
        header('Location: ../../../index.php?error=Invalid email or password');
        exit;
    }
    
    $stmt->close();

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

    // Redirect to dashboard or home page
    header('Location: ../../../index.php?success=Login successful');
    exit;
    
} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    header('Location: ../../../index.php?error=Login failed. Please try again.');
    exit;
}

$mysqli->close();
?>
