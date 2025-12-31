<?php
/**
 * Password Reset Page
 * Allows users to set a new password using a valid reset token
 */

session_start();
require_once '../../api/config.php';

// Get token from URL
$token = $_GET['token'] ?? '';

if (empty($token)) {
    $_SESSION['flash_error'] = 'Invalid reset link';
    redirect('../../index');
}

// Verify token exists and is valid
$stmt = $mysqli->prepare("
    SELECT pr.id, pr.admin_user_id, pr.expires_at, pr.is_used, u.email, u.registration_type
    FROM crime_department_password_resets pr
    JOIN crime_department_admin_users u ON pr.admin_user_id = u.id
    WHERE pr.reset_token = ? AND pr.is_used = 0
    LIMIT 1
");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['flash_error'] = 'Invalid or expired reset link';
    $stmt->close();
    redirect('../../index');
}

$reset_data = $result->fetch_assoc();
$stmt->close();

// Check if token has expired
if (strtotime($reset_data['expires_at']) < time()) {
    $_SESSION['flash_error'] = 'This reset link has expired. Please request a new one.';
    redirect('forgot-password');
}

// Check if account is registered with email
if ($reset_data['registration_type'] !== 'email') {
    $_SESSION['flash_error'] = 'This account is registered with Google. Please use Google Sign-In.';
    redirect('../../index');
}

// Get flash messages
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
    <title>Reset Password - AlerTaraQC</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="icon" type="image/x-icon" href="../image/favicon.ico">
</head>
<body>
    <!-- Reset Password Container -->
    <div class="login-wrapper">
        <div class="login-container">
            <!-- Reset Password Card -->
            <div class="login-card">
                <!-- Logo Section -->
                <div class="login-header">
                    <div class="login-logo">
                        <img src="../image/logo.svg" alt="Crime Data Analytics Logo">
                    </div>
                    <h1 class="login-title">Reset Password</h1>
                    <p class="login-subtitle">Enter your new password for <?php echo htmlspecialchars($reset_data['email']); ?></p>
                </div>

                <!-- Reset Password Form -->
                <form id="resetPasswordForm" class="login-form" action="../../api/action/forgot-password/reset.php" method="POST" novalidate>
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                    <!-- New Password Field -->
                    <div class="form-group">
                        <div class="input-wrapper">
                            <input
                                type="password"
                                id="newPassword"
                                name="new_password"
                                class="form-input"
                                placeholder="New password"
                                required
                                minlength="8"
                                autocomplete="new-password"
                            >
                            <span class="input-icon">
                                <i class="fas fa-lock"></i>
                            </span>
                            <button type="button" class="toggle-password" id="toggleNewPassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <span class="field-error" id="newPasswordError"></span>
                        <small style="color: #6b7280; font-size: 0.875rem; margin-top: 5px; display: block;">
                            Password must be at least 8 characters long
                        </small>
                    </div>

                    <!-- Confirm Password Field -->
                    <div class="form-group">
                        <div class="input-wrapper">
                            <input
                                type="password"
                                id="confirmPassword"
                                name="confirm_password"
                                class="form-input"
                                placeholder="Confirm new password"
                                required
                                minlength="8"
                                autocomplete="new-password"
                            >
                            <span class="input-icon">
                                <i class="fas fa-lock"></i>
                            </span>
                            <button type="button" class="toggle-password" id="toggleConfirmPassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <span class="field-error" id="confirmPasswordError"></span>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-login" id="submitButton">
                        <span class="btn-text">Reset Password</span>
                        <span class="btn-loader" style="display: none;">
                            <span class="spinner"></span>
                            Resetting...
                        </span>
                    </button>

                    <!-- Back to Login -->
                    <div class="form-options" style="justify-content: center; margin-top: 20px;">
                        <a href="<?php echo url('../../index'); ?>" class="forgot-password" style="text-decoration: none;">
                            <i class="fas fa-arrow-left" style="margin-right: 5px;"></i>
                            Back to Login
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Handle flash messages
        <?php if ($flash_success): ?>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: <?php echo json_encode($flash_success); ?>,
            confirmButtonColor: '#4c8a89',
            confirmButtonText: 'OK'
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

        // Toggle password visibility
        document.getElementById('toggleNewPassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('newPassword');
            const icon = this.querySelector('i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('confirmPassword');
            const icon = this.querySelector('i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Form validation
        document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const newPasswordError = document.getElementById('newPasswordError');
            const confirmPasswordError = document.getElementById('confirmPasswordError');

            // Clear previous errors
            newPasswordError.textContent = '';
            confirmPasswordError.textContent = '';

            let hasError = false;

            // Validate new password
            if (!newPassword) {
                e.preventDefault();
                newPasswordError.textContent = 'New password is required';
                hasError = true;
            } else if (newPassword.length < 8) {
                e.preventDefault();
                newPasswordError.textContent = 'Password must be at least 8 characters long';
                hasError = true;
            }

            // Validate confirm password
            if (!confirmPassword) {
                e.preventDefault();
                confirmPasswordError.textContent = 'Please confirm your password';
                hasError = true;
            } else if (newPassword !== confirmPassword) {
                e.preventDefault();
                confirmPasswordError.textContent = 'Passwords do not match';
                hasError = true;
            }

            if (hasError) {
                return;
            }

            // Show loading state
            const submitButton = document.getElementById('submitButton');
            const btnText = submitButton.querySelector('.btn-text');
            const btnLoader = submitButton.querySelector('.btn-loader');

            btnText.style.display = 'none';
            btnLoader.style.display = 'inline-flex';
            submitButton.disabled = true;
        });
    </script>
</body>
</html>
