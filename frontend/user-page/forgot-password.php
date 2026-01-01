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
                    <input type="hidden" id="otpExpireTime" name="otpExpireTime">

                    <!-- OTP Info -->
                    <div style="text-align: center; margin-bottom: 25px;">
                        <p style="color: #6b7280; font-size: 0.9rem; margin-bottom: 8px;">
                            OTP sent to <strong id="displayEmail" style="color: #4c8a89;"></strong>
                        </p>

                        <!-- Timer Display -->
                        <div id="timerContainer" style="display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 12px;">
                            <i class="fas fa-clock" style="color: #4c8a89; font-size: 1rem;"></i>
                            <span id="otpTimer" style="color: #4c8a89; font-weight: 600; font-size: 1rem;">10:00</span>
                        </div>
                        <p id="timerExpired" style="color: #ef4444; font-size: 0.875rem; margin-top: 8px; display: none; font-weight: 500;">
                            <i class="fas fa-exclamation-circle"></i> OTP has expired. Please request a new one.
                        </p>
                    </div>

                    <!-- OTP Input Boxes -->
                    <div class="form-group" style="margin-bottom: 30px;">
                        <div class="otp-input-container" style="display: flex; justify-content: center; gap: 10px; margin-bottom: 15px;">
                            <input type="text" class="otp-box" maxlength="1" data-index="0" autocomplete="off" inputmode="numeric" pattern="[0-9]">
                            <input type="text" class="otp-box" maxlength="1" data-index="1" autocomplete="off" inputmode="numeric" pattern="[0-9]">
                            <input type="text" class="otp-box" maxlength="1" data-index="2" autocomplete="off" inputmode="numeric" pattern="[0-9]">
                            <input type="text" class="otp-box" maxlength="1" data-index="3" autocomplete="off" inputmode="numeric" pattern="[0-9]">
                            <input type="text" class="otp-box" maxlength="1" data-index="4" autocomplete="off" inputmode="numeric" pattern="[0-9]">
                            <input type="text" class="otp-box" maxlength="1" data-index="5" autocomplete="off" inputmode="numeric" pattern="[0-9]">
                        </div>
                        <span class="field-error" id="otpError" style="display: block; text-align: center;"></span>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-login" id="verifyOtpButton" style="margin-bottom: 20px;">
                        <span class="btn-text">Verify OTP</span>
                        <span class="btn-loader" style="display: none;">
                            <span class="spinner"></span>
                            Verifying...
                        </span>
                    </button>

                    <!-- Resend OTP -->
                    <div style="text-align: center; margin-bottom: 15px;">
                        <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 8px;">Didn't receive the code?</p>
                        <button type="button" id="resendOtpButton" class="resend-btn" style="background: none; border: none; color: #4c8a89; cursor: pointer; font-weight: 600; font-size: 0.9rem; text-decoration: none; transition: all 0.3s;">
                            <i class="fas fa-redo" style="margin-right: 5px;"></i>
                            <span id="resendText">Resend OTP</span>
                        </button>
                    </div>

                    <!-- Back to Email -->
                    <div style="text-align: center; padding-top: 15px; border-top: 1px solid #e5e7eb;">
                        <button type="button" id="backToEmailButton" class="forgot-password" style="background: none; border: none; cursor: pointer; text-decoration: none; color: #6b7280; font-size: 0.875rem;">
                            <i class="fas fa-arrow-left" style="margin-right: 5px;"></i>
                            Change Email Address
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/alert-utils.js"></script>
    <style>
        /* OTP Input Boxes Styling */
        .otp-box {
            width: 45px;
            height: 55px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 600;
            border: 2px solid #d1d5db;
            border-radius: 8px;
            outline: none;
            transition: all 0.3s ease;
            background-color: #f9fafb;
            color: #1f2937;
            caret-color: #4c8a89;
        }

        .otp-box:focus {
            border-color: #4c8a89;
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(76, 138, 137, 0.1);
            transform: scale(1.05);
        }

        .otp-box:disabled {
            background-color: #e5e7eb;
            cursor: not-allowed;
        }

        .otp-box.error {
            border-color: #ef4444;
            animation: shake 0.3s;
        }

        .otp-box.success {
            border-color: #10b981;
            background-color: #f0fdf4;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        /* Responsive Design */
        @media (max-width: 480px) {
            .otp-box {
                width: 38px;
                height: 48px;
                font-size: 1.25rem;
            }

            .otp-input-container {
                gap: 6px !important;
            }
        }

        @media (max-width: 360px) {
            .otp-box {
                width: 35px;
                height: 45px;
                font-size: 1.1rem;
            }

            .otp-input-container {
                gap: 4px !important;
            }
        }

        /* Timer Styles */
        #otpTimer.warning {
            color: #f59e0b;
            animation: pulse 1s infinite;
        }

        #otpTimer.danger {
            color: #ef4444;
            animation: pulse 0.5s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }

        /* Resend Button Hover */
        .resend-btn:hover {
            color: #3d6e6d !important;
            text-decoration: underline !important;
        }

        .resend-btn:disabled {
            color: #9ca3af !important;
            cursor: not-allowed !important;
        }
    </style>
    <script>
        // Handle flash messages from PHP session
        <?php if ($flash_success): ?>
        AlertUtils.successWithTimer('Success!', <?php echo json_encode($flash_success); ?>, 5000);
        <?php elseif ($flash_error): ?>
        AlertUtils.error('Error!', <?php echo json_encode($flash_error); ?>);
        <?php endif; ?>

        // Global timer variables
        let otpTimerInterval = null;
        let otpExpiryTime = null;

        // OTP Box Handling
        const otpBoxes = document.querySelectorAll('.otp-box');

        otpBoxes.forEach((box, index) => {
            // Auto-focus next box on input
            box.addEventListener('input', function(e) {
                // Only allow numbers
                this.value = this.value.replace(/[^0-9]/g, '');

                // Remove error styling
                otpBoxes.forEach(b => b.classList.remove('error'));
                document.getElementById('otpError').textContent = '';

                // Move to next box if current is filled
                if (this.value.length === 1 && index < 5) {
                    otpBoxes[index + 1].focus();
                }
            });

            // Handle backspace
            box.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && this.value === '' && index > 0) {
                    otpBoxes[index - 1].focus();
                }

                // Handle paste
                if (e.key === 'v' && (e.ctrlKey || e.metaKey)) {
                    e.preventDefault();
                    navigator.clipboard.readText().then(text => {
                        const numbers = text.replace(/[^0-9]/g, '').slice(0, 6);
                        numbers.split('').forEach((num, i) => {
                            if (otpBoxes[i]) {
                                otpBoxes[i].value = num;
                            }
                        });
                        if (numbers.length === 6) {
                            otpBoxes[5].focus();
                        }
                    });
                }
            });

            // Select all on focus
            box.addEventListener('focus', function() {
                this.select();
            });
        });

        // Get OTP value from boxes
        function getOtpValue() {
            return Array.from(otpBoxes).map(box => box.value).join('');
        }

        // Clear OTP boxes
        function clearOtpBoxes() {
            otpBoxes.forEach(box => {
                box.value = '';
                box.classList.remove('error', 'success');
            });
            otpBoxes[0].focus();
        }

        // OTP Timer Function
        function startOtpTimer(durationInSeconds = 600) {
            // Clear any existing timer
            if (otpTimerInterval) {
                clearInterval(otpTimerInterval);
            }

            // Set expiry time
            otpExpiryTime = Date.now() + (durationInSeconds * 1000);
            document.getElementById('otpExpireTime').value = otpExpiryTime;

            const timerElement = document.getElementById('otpTimer');
            const timerExpired = document.getElementById('timerExpired');
            const resendButton = document.getElementById('resendOtpButton');
            const verifyButton = document.getElementById('verifyOtpButton');

            // Reset display
            timerExpired.style.display = 'none';
            timerElement.classList.remove('warning', 'danger');

            otpTimerInterval = setInterval(() => {
                const now = Date.now();
                const remaining = Math.max(0, otpExpiryTime - now);
                const seconds = Math.floor(remaining / 1000);

                if (seconds <= 0) {
                    clearInterval(otpTimerInterval);
                    timerElement.textContent = '0:00';
                    timerElement.classList.add('danger');
                    timerExpired.style.display = 'block';
                    verifyButton.disabled = true;
                    otpBoxes.forEach(box => box.disabled = true);
                    return;
                }

                const minutes = Math.floor(seconds / 60);
                const secs = seconds % 60;
                timerElement.textContent = `${minutes}:${secs.toString().padStart(2, '0')}`;

                // Color coding
                if (seconds <= 60) {
                    timerElement.classList.remove('warning');
                    timerElement.classList.add('danger');
                } else if (seconds <= 180) {
                    timerElement.classList.remove('danger');
                    timerElement.classList.add('warning');
                } else {
                    timerElement.classList.remove('warning', 'danger');
                }
            }, 1000);
        }

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

                    // Start timer and focus first OTP box
                    startOtpTimer(600); // 10 minutes
                    clearOtpBoxes();

                    AlertUtils.successWithTimer('OTP Sent!', data.message, 3000);
                } else {
                    AlertUtils.error('Error!', data.message);
                }
            } catch (error) {
                AlertUtils.error('Error!', 'An error occurred. Please try again.');
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
            const otp = getOtpValue();
            const otpError = document.getElementById('otpError');
            const verifyOtpButton = document.getElementById('verifyOtpButton');
            const btnText = verifyOtpButton.querySelector('.btn-text');
            const btnLoader = verifyOtpButton.querySelector('.btn-loader');

            // Clear previous errors
            otpError.textContent = '';
            otpBoxes.forEach(box => box.classList.remove('error'));

            // Validate OTP
            if (!otp || otp.length === 0) {
                otpError.textContent = 'Please enter the OTP code';
                otpBoxes.forEach(box => box.classList.add('error'));
                otpBoxes[0].focus();
                return;
            }

            if (otp.length !== 6) {
                otpError.textContent = 'Please enter all 6 digits';
                otpBoxes.forEach(box => box.classList.add('error'));
                return;
            }

            // Show loading state
            btnText.style.display = 'none';
            btnLoader.style.display = 'inline-flex';
            verifyOtpButton.disabled = true;
            otpBoxes.forEach(box => box.disabled = true);

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
                    // Success styling
                    otpBoxes.forEach(box => box.classList.add('success'));

                    // Stop timer
                    if (otpTimerInterval) {
                        clearInterval(otpTimerInterval);
                    }

                    AlertUtils.success('OTP Verified!', 'A password reset link has been sent to your email. Please check your inbox and follow the link to reset your password.', function() {
                        // Redirect to login page
                        window.location.href = '../../index.php';
                    });
                } else {
                    // Error styling
                    otpBoxes.forEach(box => box.classList.add('error'));
                    otpError.textContent = data.message;

                    AlertUtils.error('Error!', data.message);

                    // Re-enable boxes and clear
                    otpBoxes.forEach(box => box.disabled = false);
                    setTimeout(() => clearOtpBoxes(), 1000);
                }
            } catch (error) {
                AlertUtils.error('Error!', 'An error occurred. Please try again.');
                otpBoxes.forEach(box => box.disabled = false);
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
            const resendText = document.getElementById('resendText');
            const originalText = resendText.textContent;

            button.disabled = true;
            resendText.textContent = 'Sending...';

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
                    // Clear boxes and restart timer
                    clearOtpBoxes();
                    startOtpTimer(600); // 10 minutes

                    // Re-enable verify button and boxes
                    document.getElementById('verifyOtpButton').disabled = false;
                    otpBoxes.forEach(box => box.disabled = false);

                    AlertUtils.successWithTimer('OTP Resent!', data.message, 3000);
                } else {
                    AlertUtils.error('Error!', data.message);
                }
            } catch (error) {
                AlertUtils.error('Error!', 'An error occurred. Please try again.');
            } finally {
                button.disabled = false;
                resendText.textContent = originalText;
            }
        });

        // Back to Email
        document.getElementById('backToEmailButton').addEventListener('click', function() {
            // Stop timer
            if (otpTimerInterval) {
                clearInterval(otpTimerInterval);
            }

            // Clear and reset form
            clearOtpBoxes();
            document.getElementById('otpForm').style.display = 'none';
            document.getElementById('emailForm').style.display = 'block';
            document.getElementById('formTitle').textContent = 'Forgot Password?';
            document.getElementById('formSubtitle').textContent = "Enter your email address and we'll send you an OTP";
            document.getElementById('otpError').textContent = '';

            // Reset timer display
            document.getElementById('otpTimer').textContent = '10:00';
            document.getElementById('otpTimer').classList.remove('warning', 'danger');
            document.getElementById('timerExpired').style.display = 'none';
        });
    </script>
</body>
</html>
