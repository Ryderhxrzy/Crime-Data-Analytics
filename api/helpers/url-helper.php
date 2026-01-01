<?php
/**
 * URL Helper Functions
 * Provides reusable functions for generating environment-specific URLs
 */

/**
 * Get the base URL based on environment
 * @return string The base URL
 */
function getBaseUrl() {
    $app_env = $_ENV['APP_ENV'] ?? 'local';
    if ($app_env === 'production') {
        return $_ENV['DEPLOY_LINK'] ?? 'https://crime.alertaraqc.com/';
    } else {
        return 'http://localhost/Crime-Data-Analytics/';
    }
}

/**
 * Get the full URL for a specific path
 * @param string $path The path relative to base URL (e.g., 'frontend/user-page/unlock-account.php')
 * @return string The full URL
 */
function getUrl($path = '') {
    $baseUrl = getBaseUrl();
    $path = ltrim($path, '/');
    return $baseUrl . $path;
}

/**
 * Get redirect URL for success/error (Google OAuth compatibility)
 * @param string $path Relative path from root
 * @return string Full URL
 */
function getRedirectUrl($path) {
    $baseUrl = getBaseUrl();
    $path = ltrim($path, '/');
    return rtrim($baseUrl, '/') . '/' . $path;
}

/**
 * Get current protocol (http or https)
 * @return string The protocol
 */
function getProtocol() {
    return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
}

/**
 * Get current host
 * @return string The host
 */
function getHost() {
    return $_SERVER['HTTP_HOST'] ?? 'localhost';
}

/**
 * Check if running in production environment
 * @return bool True if production, false otherwise
 */
function isProduction() {
    $app_env = $_ENV['APP_ENV'] ?? 'local';
    return $app_env === 'production';
}

/**
 * Check if running in local environment
 * @return bool True if local, false otherwise
 */
function isLocal() {
    return !isProduction();
}
?>
