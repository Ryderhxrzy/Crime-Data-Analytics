<?php
/**
 * Google OAuth Helper Class
 *
 * Handles Google OAuth authentication flow
 */

class GoogleOAuth
{
    private $config;
    private $clientId;
    private $clientSecret;
    private $redirectUri;
    private $scopes;

    public function __construct()
    {
        $configPath = __DIR__ . '/config.php';
        if (!file_exists($configPath)) {
            throw new Exception('Google OAuth config file not found. Please copy config.example.php to config.php and configure it.');
        }

        $this->config = require $configPath;
        $this->clientId = $this->config['client_id'];
        $this->clientSecret = $this->config['client_secret'];
        $this->redirectUri = $this->config['redirect_uri'];
        $this->scopes = $this->config['scopes'];
    }

    /**
     * Generate authorization URL for Google OAuth
     *
     * @return string Authorization URL
     */
    public function getAuthorizationUrl()
    {
        $state = bin2hex(random_bytes(16));
        $_SESSION['oauth_state'] = $state;

        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'scope' => implode(' ', $this->scopes),
            'state' => $state,
            'access_type' => 'offline',
            'prompt' => 'consent',
        ];

        return $this->config['auth_url'] . '?' . http_build_query($params);
    }

    /**
     * Exchange authorization code for access token
     *
     * @param string $code Authorization code
     * @return array Token response
     */
    public function getAccessToken($code)
    {
        $params = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUri,
            'grant_type' => 'authorization_code',
            'code' => $code,
        ];

        $ch = curl_init($this->config['token_url']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception('Failed to get access token: ' . $response);
        }

        return json_decode($response, true);
    }

    /**
     * Get user information from Google
     *
     * @param string $accessToken Access token
     * @return array User information
     */
    public function getUserInfo($accessToken)
    {
        $ch = curl_init($this->config['userinfo_url']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception('Failed to get user info: ' . $response);
        }

        return json_decode($response, true);
    }

    /**
     * Verify OAuth state to prevent CSRF attacks
     *
     * @param string $state State parameter from callback
     * @return bool True if state is valid
     */
    public function verifyState($state)
    {
        if (!isset($_SESSION['oauth_state'])) {
            return false;
        }

        $valid = hash_equals($_SESSION['oauth_state'], $state);
        unset($_SESSION['oauth_state']);

        return $valid;
    }
}
