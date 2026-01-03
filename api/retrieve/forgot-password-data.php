<?php
/**
 * Forgot Password Page Data Controller
 * Handles flash message retrieval for forgot password page
 */

// Start session
session_start();

// Get flash messages from session
$flash_error = $_SESSION['flash_error'] ?? null;
$flash_success = $_SESSION['flash_success'] ?? null;

// Clear flash messages
unset($_SESSION['flash_error']);
unset($_SESSION['flash_success']);
