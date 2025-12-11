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
                                placeholder="Email Address"
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
                                placeholder="Password"
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
