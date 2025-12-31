<?php
/**
 * URL Helper Utility
 * Provides environment-aware URL generation for both local and production
 */

// Determine if we're in production
function isProduction() {
    return ($_ENV['APP_ENV'] ?? 'local') === 'production';
}

// Get PHP extension based on environment
function getPhpExt() {
    return isProduction() ? '' : '.php';
}

// Generate URL with proper extension
function url($path, $includeExtension = true) {
    $ext = '';

    if ($includeExtension && !isProduction()) {
        // Only add .php if the path doesn't already have an extension
        if (!preg_match('/\.(php|html|htm)$/', $path)) {
            $ext = '.php';
        }
    }

    return $path . $ext;
}

// Redirect to a URL with proper extension
function redirect($path, $statusCode = 302) {
    $url = url($path);
    header("Location: " . $url, true, $statusCode);
    exit;
}

// Get base URL
function baseUrl($path = '') {
    if (isProduction()) {
        $base = $_ENV['DEPLOY_LINK'] ?? 'https://crime.alertaraqc.com/';
    } else {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $base = $protocol . '://' . $host . '/Crime-Data-Analytics/';
    }

    return rtrim($base, '/') . '/' . ltrim($path, '/');
}

// Asset URL (for CSS, JS, images - no .php extension)
function asset($path) {
    return $path;
}

// API URL helper
function apiUrl($endpoint) {
    return url('../../api/action/' . ltrim($endpoint, '/'));
}
?>
