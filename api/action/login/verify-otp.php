<?php
/**
 * OTP Verification View
 * Displays OTP verification form for 2FA login
 */

// Load controller logic
require_once 'verify-otp-controller.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - AlerTaraQC</title>
    <link rel="stylesheet" href="../../../frontend/css/global.css">
    <link rel="stylesheet" href="../../../frontend/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="icon" type="image/x-icon" href="../../../frontend/image/favicon.ico">
</head>
<body>
    <div class="login-wrapper">
        <div class="login-container">
            <div class="login-card">
                <div class="login-header">
                    <div class="login-logo">
                        <img src="../../../frontend/image/logo.svg" alt="Crime Data Analytics Logo">
                    </div>
                    <h1 class="login-title">Verify OTP</h1>
                    <p class="login-subtitle">Enter the 6-digit code sent to <?php echo htmlspecialchars($user_email); ?></p>
                </div>

                <form class="login-form" method="POST" action="" id="otpForm">

                    <!-- Timer Display -->
                    <div class="otp-timer-container">
                        <div id="timerContainer" class="otp-timer-display">
                            <i class="fas fa-clock otp-timer-icon"></i>
                            <span id="otpTimer" class="otp-timer-text <?php echo $timer_class; ?>">
                                <?php echo $timer_display; ?>
                            </span>
                        </div>
                        <p id="timerExpired" class="otp-timer-expired" style="display: <?php echo $timer_expired_display; ?>;">
                            <i class="fas fa-exclamation-circle"></i> OTP has expired. Please login again.
                        </p>
                    </div>

                    <!-- OTP Input Boxes -->
                    <div class="otp-input-group">
                        <div class="otp-input-container">
                            <input type="text" class="otp-box" maxlength="1" data-index="0" autocomplete="off" inputmode="numeric" pattern="[0-9]" <?php echo $input_disabled; ?>>
                            <input type="text" class="otp-box" maxlength="1" data-index="1" autocomplete="off" inputmode="numeric" pattern="[0-9]" <?php echo $input_disabled; ?>>
                            <input type="text" class="otp-box" maxlength="1" data-index="2" autocomplete="off" inputmode="numeric" pattern="[0-9]" <?php echo $input_disabled; ?>>
                            <input type="text" class="otp-box" maxlength="1" data-index="3" autocomplete="off" inputmode="numeric" pattern="[0-9]" <?php echo $input_disabled; ?>>
                            <input type="text" class="otp-box" maxlength="1" data-index="4" autocomplete="off" inputmode="numeric" pattern="[0-9]" <?php echo $input_disabled; ?>>
                            <input type="text" class="otp-box" maxlength="1" data-index="5" autocomplete="off" inputmode="numeric" pattern="[0-9]" <?php echo $input_disabled; ?>>
                        </div>
                        <input type="hidden" name="otp" id="otpValue">
                    </div>

                    <button type="submit" class="btn-login" id="verifyOtpButton" <?php echo $button_disabled; ?>>
                        <span class="btn-text">Verify OTP</span>
                        <span class="btn-loader" style="display: none;">
                            <span class="spinner"></span>
                            Verifying...
                        </span>
                    </button>

                    <div class="form-options" style="justify-content: center; margin-top: 20px;">
                        <a href="../../../index.php" class="forgot-password" style="text-decoration: none;">
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
    <script src="../../../frontend/js/alert-utils.js"></script>
    <script src="../../../frontend/js/verify-otp.js"></script>
    <script>
        // Initialize OTP verification with remaining seconds and error message
        initVerifyOtp(
            <?php echo $remaining_seconds; ?>,
            <?php echo isset($error) ? json_encode($error) : 'null'; ?>
        );
    </script>
</body>
</html>
