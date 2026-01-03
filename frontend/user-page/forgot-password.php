<?php
/**
 * Forgot Password View
 * Displays forgot password form with OTP verification
 */

// Load forgot password data controller
require_once '../../api/retrieve/forgot-password-data.php';
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
    <script src="../js/forgot-password.js"></script>
    <script>
        // Initialize forgot password functionality
        initForgotPassword(
            <?php echo $flash_success ? json_encode($flash_success) : 'null'; ?>,
            <?php echo $flash_error ? json_encode($flash_error) : 'null'; ?>
        );
    </script>
</body>
</html>
