<?php
/**
 * Helper Functions for Google OAuth
 */

/**
 * Get the base URL of the application
 * Works for both local and production environments
 *
 * @return string Base URL without trailing slash
 */
function getBaseUrl() {
    // Check if running on HTTPS
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
                ($_SERVER['SERVER_PORT'] ?? 80) == 443 ? 'https://' : 'http://';

    // Get the host
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

    // Get the directory path (remove /api/auth/google from the current script path)
    $scriptPath = dirname($_SERVER['SCRIPT_NAME']);

    // Remove /api/auth/google from the path to get the base
    $basePath = str_replace('/api/auth/google', '', $scriptPath);

    // Clean up the path
    $basePath = rtrim($basePath, '/');

    return $protocol . $host . $basePath;
}

/**
 * Get redirect URL for success/error
 *
 * @param string $path Relative path from root (e.g., 'index.php' or 'frontend/admin-page/dashboard.php')
 * @return string Full URL
 */
function getRedirectUrl($path) {
    $baseUrl = getBaseUrl();
    $path = ltrim($path, '/');
    return $baseUrl . '/' . $path;
}
