<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Include the login process
    require_once 'login_process.php';
    exit;
}

// If not POST, redirect to main login page
header('Location: index.php');
exit;
?>
