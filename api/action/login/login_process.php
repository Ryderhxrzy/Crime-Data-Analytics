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
    
    $_SESSION['user'] = [
        'id' => $user['id'],
        'email' => $user['email'],
        'full_name' => $user['full_name'],
        'role' => $user['role']
    ];
    
    header('Location: ../../../index.php?success=Login successful');
    exit;
    
} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    header('Location: ../../../index.php?error=Login failed. Please try again.');
    exit;
}

$mysqli->close();
?>
