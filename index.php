<?php
// Start session
session_start();

// Check if user is already logged in
if (isset($_SESSION['user']) && isset($_SESSION['last_activity'])) {
    // Check if session is still valid (not expired)
    $session_timeout = 3600; // 1 hour
    if ((time() - $_SESSION['last_activity']) <= $session_timeout) {
        // Update last activity
        $_SESSION['last_activity'] = time();

        // Redirect to dashboard (change this to your dashboard page)
        header('Location: frontend/admin-page/dashboard.php');
        exit;
    }
}

// Get flash messages from session
$flash_error = $_SESSION['flash_error'] ?? null;
$flash_success = $_SESSION['flash_success'] ?? null;

// Clear flash messages
unset($_SESSION['flash_error']);
unset($_SESSION['flash_success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AlerTaraQC</title>
    <link rel="stylesheet" href="frontend/css/global.css">
    <link rel="stylesheet" href="frontend/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="icon" type="image/x-icon" href="frontend/image/favicon.ico">
</head>
<body>
    <!-- Login Container -->
    <div class="login-wrapper">
        <div class="login-container">
            <!-- Login Card -->
            <div class="login-card">
                <!-- Logo Section -->
                <div class="login-header">
                    <div class="login-logo">
                        <img src="frontend/image/logo.svg" alt="Crime Data Analytics Logo">
                    </div>
                    <h1 class="login-title">Crime Data Analytics</h1>
                    <p class="login-subtitle">Sign in to continue</p>
                </div>

                <!-- Login Form -->
                <form id="loginForm" class="login-form" action="api/action/login/login.php" method="POST" novalidate>
                    <!-- Email Field -->
                    <div class="form-group">
                        <div class="input-wrapper">
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-input"
                                placeholder="youremail@alertaraqc.com"
                                required
                                autocomplete="email"
                            >
                            <span class="input-icon">
                                <i class="fas fa-envelope"></i>
                            </span>
                        </div>
                        <span class="field-error" id="emailError"></span>
                    </div>

                    <!-- Password Field -->
                    <div class="form-group">
                        <div class="input-wrapper">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-input"
                                placeholder="Your password"
                                required
                                autocomplete="current-password"
                            >
                            <span class="input-icon">
                                <i class="fas fa-lock"></i>
                            </span>
                            <button type="button" class="toggle-password" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <span class="field-error" id="passwordError"></span>
                    </div>

                    <!-- Remember Me and Forgot Password -->
                    <div class="form-options">
                        <label class="checkbox-label">
                            <input type="checkbox" id="rememberMe" name="rememberMe">
                            <span class="checkbox-custom"></span>
                            <span class="checkbox-text">Remember me</span>
                        </label>
                        <a href="#" class="forgot-password" id="forgotPasswordLink">
                            Forgot password?
                        </a>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-login" id="loginButton">
                        <span class="btn-text">Sign In</span>
                        <span class="btn-loader" style="display: none;">
                            <span class="spinner"></span>
                            Signing in...
                        </span>
                    </button>

                    <!-- Divider -->
                    <div class="login-divider">
                        <span class="divider-line"></span>
                        <span class="divider-text">or</span>
                        <span class="divider-line"></span>
                    </div>

                    <!-- Google Login Button -->
                    <button type="button" class="btn-google" id="googleLoginButton">
                        <svg class="google-icon" viewBox="0 0 24 24" width="20" height="20">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        <span>Sign in with Google</span>
                    </button>

                    <!-- Legal Links -->
                    <div class="legal-links">
                        <p>By signing in, you agree to our</p>
                        <div class="links">
                            <a href="frontend/legal/terms-of-use.php" target="_blank">Terms of Use</a>
                            <span>and</span>
                            <a href="frontend/legal/privacy-policy.php" target="_blank">Privacy Policy</a>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <!-- Reusable Loading Component -->
    <div id="loadingOverlay" class="loading-overlay" style="display: none;">
        <div class="loading-spinner">
            <div class="spinner-circle"></div>
            <div class="spinner-circle"></div>
            <div class="spinner-circle"></div>
            <p class="loading-text">Processing...</p>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="frontend/js/login.js"></script>
    <script>
        // Handle URL parameters for success/error messages
        const urlParams = new URLSearchParams(window.location.search);
        const success = urlParams.get('success');
        const error = urlParams.get('error');

        // Handle flash messages from PHP session
        <?php if ($flash_success): ?>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: <?php echo json_encode($flash_success); ?>,
            confirmButtonColor: '#4c8a89',
            confirmButtonText: 'OK',
            timer: 3000,
            timerProgressBar: true
        });
        <?php elseif ($flash_error): ?>
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: <?php echo json_encode($flash_error); ?>,
            confirmButtonColor: '#4c8a89',
            confirmButtonText: 'OK'
        });
        <?php endif; ?>

        if (success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: success,
                confirmButtonColor: '#4c8a89',
                confirmButtonText: 'OK',
                timer: 3000,
                timerProgressBar: true
            });

            // Clear URL parameters
            window.history.replaceState({}, document.title, window.location.pathname);
        }

        if (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: error,
                confirmButtonColor: '#4c8a89',
                confirmButtonText: 'OK'
            });

            // Clear URL parameters
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    </script>
</body>
</html>
