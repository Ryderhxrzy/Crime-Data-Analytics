<?php
/**
 * Google OAuth Configuration
 *
 * Reads credentials from .env file
 */

// Load environment variables
require_once __DIR__ . '/../../config.php';

// Determine base URL based on environment
$app_env = $_ENV['APP_ENV'] ?? 'local';
$base_url = ($app_env === 'production')
    ? rtrim($_ENV['DEPLOY_LINK'] ?? '', '/')
    : 'http://localhost/Crime-Data-Analytics';

return [
    // Google OAuth Client ID from .env
    'client_id' => $_ENV['GOOGLE_CLIENT_ID'] ?? '',

    // Google OAuth Client Secret from .env
    'client_secret' => $_ENV['GOOGLE_SECRET'] ?? '',

    // Redirect URI (automatically adjusts for local/production)
    'redirect_uri' => $base_url . '/api/auth/google/callback.php',

    // OAuth Scopes
    'scopes' => [
        'https://www.googleapis.com/auth/userinfo.email',
        'https://www.googleapis.com/auth/userinfo.profile',
    ],

    // OAuth Endpoints
    'auth_url' => 'https://accounts.google.com/o/oauth2/v2/auth',
    'token_url' => 'https://oauth2.googleapis.com/token',
    'userinfo_url' => 'https://www.googleapis.com/oauth2/v2/userinfo',
];
