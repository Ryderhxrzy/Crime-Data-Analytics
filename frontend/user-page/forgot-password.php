<?php
// Start session
session_start();

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
    <title>Forgot Password - AlerTaraQC</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="icon" type="image/x-icon" href="../image/favicon.ico">
</head>
<body>
    <!-- Forgot Password Container -->
    <div class="login-wrapper">
        <div class="login-container">
            <!-- Forgot Password Card -->
            <div class="login-card">
                <!-- Logo Section -->
                <div class="login-header">
                    <div class="login-logo">
                        <img src="../image/logo.svg" alt="Crime Data Analytics Logo">
                    </div>
                    <h1 class="login-title" id="formTitle">Forgot Password?</h1>
                    <p class="login-subtitle" id="formSubtitle">Enter your email address and we'll send you an OTP</p>
                </div>

                <!-- Step 1: Email Input Form -->
                <form id="emailForm" class="login-form" style="display: block;" novalidate>
                    <!-- Email Field -->
                    <div class="form-group" style="margin-bottom: 25px;">
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

                    <!-- Submit Button -->
                    <button type="submit" class="btn-login" id="sendOtpButton" style="margin-top: 10px;">
                        <span class="btn-text">Send OTP</span>
                        <span class="btn-loader" style="display: none;">
                            <span class="spinner"></span>
                            Sending...
                        </span>
                    </button>
                </form>

                <!-- Step 2: OTP Verification Form -->
                <form id="otpForm" class="login-form" style="display: none;" novalidate>
                    <input type="hidden" id="verifyEmail" name="email">

                    <!-- OTP Field -->
                    <div class="form-group">
                        <div class="input-wrapper">
                            <input
                                type="text"
                                id="otp"
                                name="otp"
                                class="form-input"
                                placeholder="Enter 6-digit OTP"
                                required
                                maxlength="6"
                                autocomplete="off"
                            >
                            <span class="input-icon">
                                <i class="fas fa-key"></i>
                            </span>
                        </div>
                        <span class="field-error" id="otpError"></span>
                        <small style="color: #6b7280; font-size: 0.875rem; margin-top: 5px; display: block;">
                            OTP sent to <span id="displayEmail" style="font-weight: 600;"></span>
                        </small>
                    </div>

                    <!-- Resend OTP -->
                    <div class="form-options" style="justify-content: center; margin-bottom: 20px;">
                        <button type="button" id="resendOtpButton" class="forgot-password" style="background: none; border: none; cursor: pointer; text-decoration: none;">
                            <i class="fas fa-redo" style="margin-right: 5px;"></i>
                            Resend OTP
                        </button>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-login" id="verifyOtpButton">
                        <span class="btn-text">Verify OTP</span>
                        <span class="btn-loader" style="display: none;">
                            <span class="spinner"></span>
                            Verifying...
                        </span>
                    </button>

                    <!-- Back to Email -->
                    <div class="form-options" style="justify-content: center; margin-top: 20px;">
                        <button type="button" id="backToEmailButton" class="forgot-password" style="background: none; border: none; cursor: pointer; text-decoration: none;">
                            <i class="fas fa-arrow-left" style="margin-right: 5px;"></i>
                            Change Email
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Handle flash messages from PHP session
        <?php if ($flash_success): ?>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: <?php echo json_encode($flash_success); ?>,
            confirmButtonColor: '#4c8a89',
            confirmButtonText: 'OK',
            timer: 5000,
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

        // Step 1: Email Form Submission
        document.getElementById('emailForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const email = document.getElementById('email').value.trim();
            const emailError = document.getElementById('emailError');
            const sendOtpButton = document.getElementById('sendOtpButton');
            const btnText = sendOtpButton.querySelector('.btn-text');
            const btnLoader = sendOtpButton.querySelector('.btn-loader');

            // Clear previous errors
            emailError.textContent = '';

            // Validate email
            if (!email) {
                emailError.textContent = 'Email is required';
                return;
            }

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                emailError.textContent = 'Please enter a valid email address';
                return;
            }

            // Show loading state
            btnText.style.display = 'none';
            btnLoader.style.display = 'inline-flex';
            sendOtpButton.disabled = true;

            try {
                // Send OTP request
                const response = await fetch('../../api/action/forgot-password/send-otp.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'email=' + encodeURIComponent(email)
                });

                const data = await response.json();

                if (data.success) {
                    // Show OTP form
                    document.getElementById('emailForm').style.display = 'none';
                    document.getElementById('otpForm').style.display = 'block';
                    document.getElementById('verifyEmail').value = email;
                    document.getElementById('displayEmail').textContent = email;
                    document.getElementById('formTitle').textContent = 'Verify OTP';
                    document.getElementById('formSubtitle').textContent = 'Enter the 6-digit code sent to your email';

                    Swal.fire({
                        icon: 'success',
                        title: 'OTP Sent!',
                        text: data.message,
                        confirmButtonColor: '#4c8a89',
                        confirmButtonText: 'OK',
                        timer: 3000
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message,
                        confirmButtonColor: '#4c8a89',
                        confirmButtonText: 'OK'
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred. Please try again.',
                    confirmButtonColor: '#4c8a89',
                    confirmButtonText: 'OK'
                });
            } finally {
                // Reset button state
                btnText.style.display = 'inline';
                btnLoader.style.display = 'none';
                sendOtpButton.disabled = false;
            }
        });

        // Step 2: OTP Verification
        document.getElementById('otpForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const email = document.getElementById('verifyEmail').value;
            const otp = document.getElementById('otp').value.trim();
            const otpError = document.getElementById('otpError');
            const verifyOtpButton = document.getElementById('verifyOtpButton');
            const btnText = verifyOtpButton.querySelector('.btn-text');
            const btnLoader = verifyOtpButton.querySelector('.btn-loader');

            // Clear previous errors
            otpError.textContent = '';

            // Validate OTP
            if (!otp) {
                otpError.textContent = 'OTP is required';
                return;
            }

            if (otp.length !== 6) {
                otpError.textContent = 'OTP must be 6 digits';
                return;
            }

            // Show loading state
            btnText.style.display = 'none';
            btnLoader.style.display = 'inline-flex';
            verifyOtpButton.disabled = true;

            try {
                // Verify OTP
                const response = await fetch('../../api/action/forgot-password/verify-otp.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'email=' + encodeURIComponent(email) + '&otp=' + encodeURIComponent(otp)
                });

                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'OTP verified! Redirecting to reset password...',
                        confirmButtonColor: '#4c8a89',
                        confirmButtonText: 'OK',
                        timer: 2000
                    }).then(() => {
                        // Redirect to reset password page
                        window.location.href = 'reset-password.php?token=' + data.token;
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message,
                        confirmButtonColor: '#4c8a89',
                        confirmButtonText: 'OK'
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred. Please try again.',
                    confirmButtonColor: '#4c8a89',
                    confirmButtonText: 'OK'
                });
            } finally {
                // Reset button state
                btnText.style.display = 'inline';
                btnLoader.style.display = 'none';
                verifyOtpButton.disabled = false;
            }
        });

        // Resend OTP
        document.getElementById('resendOtpButton').addEventListener('click', async function() {
            const email = document.getElementById('verifyEmail').value;
            const button = this;
            button.disabled = true;

            try {
                const response = await fetch('../../api/action/forgot-password/send-otp.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'email=' + encodeURIComponent(email)
                });

                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'OTP Resent!',
                        text: data.message,
                        confirmButtonColor: '#4c8a89',
                        confirmButtonText: 'OK',
                        timer: 3000
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message,
                        confirmButtonColor: '#4c8a89',
                        confirmButtonText: 'OK'
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred. Please try again.',
                    confirmButtonColor: '#4c8a89',
                    confirmButtonText: 'OK'
                });
            } finally {
                button.disabled = false;
            }
        });

        // Back to Email
        document.getElementById('backToEmailButton').addEventListener('click', function() {
            document.getElementById('otpForm').style.display = 'none';
            document.getElementById('emailForm').style.display = 'block';
            document.getElementById('formTitle').textContent = 'Forgot Password?';
            document.getElementById('formSubtitle').textContent = "Enter your email address and we'll send you an OTP";
            document.getElementById('otp').value = '';
        });

        // Allow only numbers in OTP field
        document.getElementById('otp').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
</body>
</html>
