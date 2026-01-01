<?php
session_start();
require_once '../../config.php';

// Check if user is in 2FA verification process
if (!isset($_SESSION['2fa_user_id'])) {
    header('Location: ../../../index.php?error=Invalid access');
    exit;
}

$user_id = $_SESSION['2fa_user_id'];
$user_email = $_SESSION['2fa_email'] ?? '';

// Handle OTP verification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp_input = trim($_POST['otp'] ?? '');

    if (empty($otp_input)) {
        $error = 'OTP code is required';
    } else {
        // Get user info
        $user_stmt = $mysqli->prepare("SELECT id, email, full_name, role FROM crime_department_admin_users WHERE id = ? LIMIT 1");
        $user_stmt->bind_param("i", $user_id);
        $user_stmt->execute();
        $user_result = $user_stmt->get_result();
        $user = $user_result->fetch_assoc();
        $user_stmt->close();

        if (!$user) {
            header('Location: ../../../index.php?error=User not found');
            exit;
        }

        // Verify OTP from otp_verification table
        $otp_stmt = $mysqli->prepare("SELECT otp_code, expires_at, is_used FROM crime_department_otp_verification WHERE admin_user_id = ? AND is_used = 0 ORDER BY created_at DESC LIMIT 1");
        $otp_stmt->bind_param("i", $user_id);
        $otp_stmt->execute();
        $otp_result = $otp_stmt->get_result();
        $otp_data = $otp_result->fetch_assoc();
        $otp_stmt->close();

        if (!$otp_data) {
            $error = 'No valid OTP found. Please login again.';
        } elseif (strtotime($otp_data['expires_at']) < time()) {
            $error = 'OTP has expired. Please login again.';
        } elseif ($otp_data['otp_code'] !== $otp_input) {
            $error = 'Invalid OTP code. Please try again.';

            // Log failed OTP attempt
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
            $log_stmt = $mysqli->prepare("INSERT INTO crime_department_activity_logs (admin_user_id, activity_type, description, ip_address, user_agent) VALUES (?, '2fa_otp_failed', 'Failed OTP verification attempt', ?, ?)");
            $log_stmt->bind_param("iss", $user_id, $ip_address, $user_agent);
            $log_stmt->execute();
            $log_stmt->close();
        } else {
            // OTP is valid - complete login
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;

            // Mark OTP as used
            $mark_used = $mysqli->prepare("UPDATE crime_department_otp_verification SET is_used = 1 WHERE admin_user_id = ? AND otp_code = ?");
            $mark_used->bind_param("is", $user_id, $otp_input);
            $mark_used->execute();
            $mark_used->close();

            // Update user status to 'active', reset attempt count, update IP address and last_login timestamp
            $update_stmt = $mysqli->prepare("UPDATE crime_department_admin_users SET status = 'active', attempt_count = 0, ip_address = ?, last_login = NOW() WHERE id = ?");
            $update_stmt->bind_param("si", $ip_address, $user_id);
            $update_stmt->execute();
            $update_stmt->close();

            // Insert activity log for successful login
            $log_stmt = $mysqli->prepare("INSERT INTO crime_department_activity_logs (admin_user_id, activity_type, description, ip_address, user_agent) VALUES (?, 'login', 'User logged in via email/password with 2FA', ?, ?)");
            $log_stmt->bind_param("iss", $user_id, $ip_address, $user_agent);
            $log_stmt->execute();
            $log_stmt->close();

            // Clear 2FA session variables
            unset($_SESSION['2fa_user_id']);
            unset($_SESSION['2fa_email']);
            unset($_SESSION['2fa_ip']);

            // Regenerate session ID for security
            session_regenerate_id(true);

            // Store user information in session
            $_SESSION['user'] = [
                'id' => $user['id'],
                'email' => $user['email'],
                'full_name' => $user['full_name'],
                'role' => $user['role']
            ];

            // Set session activity timestamps
            $_SESSION['last_activity'] = time();
            $_SESSION['session_created'] = time();

            // Store user IP and user agent for security
            $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'] ?? '';
            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';

            // Set session cookie parameters for security (1 hour)
            $cookie_params = session_get_cookie_params();
            $is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
            setcookie(
                session_name(),
                session_id(),
                time() + 3600,
                $cookie_params['path'],
                $cookie_params['domain'],
                $is_https,
                true
            );

            // Redirect to system overview page
            header('Location: ../../../frontend/admin-page/system-overview.php');
            exit;
        }
    }
}
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
                    <div style="text-align: center; margin-bottom: 25px;">
                        <div id="timerContainer" style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                            <i class="fas fa-clock" style="color: #4c8a89; font-size: 1rem;"></i>
                            <span id="otpTimer" style="color: #4c8a89; font-weight: 600; font-size: 1rem;">2:00</span>
                        </div>
                        <p id="timerExpired" style="color: #ef4444; font-size: 0.875rem; margin-top: 8px; display: none; font-weight: 500;">
                            <i class="fas fa-exclamation-circle"></i> OTP has expired. Please login again.
                        </p>
                    </div>

                    <!-- OTP Input Boxes -->
                    <div class="form-group" style="margin-bottom: 30px;">
                        <div class="otp-input-container" style="display: flex; justify-content: center; gap: 10px;">
                            <input type="text" class="otp-box" maxlength="1" data-index="0" autocomplete="off" inputmode="numeric" pattern="[0-9]">
                            <input type="text" class="otp-box" maxlength="1" data-index="1" autocomplete="off" inputmode="numeric" pattern="[0-9]">
                            <input type="text" class="otp-box" maxlength="1" data-index="2" autocomplete="off" inputmode="numeric" pattern="[0-9]">
                            <input type="text" class="otp-box" maxlength="1" data-index="3" autocomplete="off" inputmode="numeric" pattern="[0-9]">
                            <input type="text" class="otp-box" maxlength="1" data-index="4" autocomplete="off" inputmode="numeric" pattern="[0-9]">
                            <input type="text" class="otp-box" maxlength="1" data-index="5" autocomplete="off" inputmode="numeric" pattern="[0-9]">
                        </div>
                        <input type="hidden" name="otp" id="otpValue">
                    </div>

                    <button type="submit" class="btn-login" id="verifyOtpButton">
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

        /* Button Loader */
        .btn-loader {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .spinner {
            width: 16px;
            height: 16px;
            border: 2px solid #ffffff;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
    <script>
        // OTP Box Handling
        const otpBoxes = document.querySelectorAll('.otp-box');
        const otpForm = document.getElementById('otpForm');
        const otpValue = document.getElementById('otpValue');
        const verifyOtpButton = document.getElementById('verifyOtpButton');

        otpBoxes.forEach((box, index) => {
            // Auto-focus next box on input
            box.addEventListener('input', function(e) {
                // Only allow numbers
                this.value = this.value.replace(/[^0-9]/g, '');

                // Remove error styling
                otpBoxes.forEach(b => b.classList.remove('error'));

                // Move to next box if current is filled
                if (this.value.length === 1 && index < 5) {
                    otpBoxes[index + 1].focus();
                } else if (this.value.length === 1 && index === 5) {
                    // Auto-submit when all boxes are filled
                    const otp = getOtpValue();
                    if (otp.length === 6) {
                        // Trigger form submission programmatically
                        submitOtpForm();
                    }
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
                            // Auto-submit after paste
                            setTimeout(() => submitOtpForm(), 100);
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

        // OTP Timer Function (2 minutes)
        let otpTimerInterval = null;
        let otpExpiryTime = Date.now() + (120 * 1000); // 2 minutes

        function startOtpTimer() {
            const timerElement = document.getElementById('otpTimer');
            const timerExpired = document.getElementById('timerExpired');

            otpTimerInterval = setInterval(() => {
                const now = Date.now();
                const remaining = Math.max(0, otpExpiryTime - now);
                const seconds = Math.floor(remaining / 1000);

                if (seconds <= 0) {
                    clearInterval(otpTimerInterval);
                    timerElement.textContent = '0:00';
                    timerElement.classList.add('danger');
                    timerExpired.style.display = 'block';
                    verifyOtpButton.disabled = true;
                    otpBoxes.forEach(box => box.disabled = true);
                    return;
                }

                const minutes = Math.floor(seconds / 60);
                const secs = seconds % 60;
                timerElement.textContent = `${minutes}:${secs.toString().padStart(2, '0')}`;

                // Color coding
                if (seconds <= 30) {
                    timerElement.classList.remove('warning');
                    timerElement.classList.add('danger');
                } else if (seconds <= 60) {
                    timerElement.classList.remove('danger');
                    timerElement.classList.add('warning');
                } else {
                    timerElement.classList.remove('warning', 'danger');
                }
            }, 1000);
        }

        // Start timer on page load
        startOtpTimer();

        // Focus first box
        otpBoxes[0].focus();

        // Submit OTP form function
        function submitOtpForm() {
            const otp = getOtpValue();
            const btnText = verifyOtpButton.querySelector('.btn-text');
            const btnLoader = verifyOtpButton.querySelector('.btn-loader');

            // Clear previous errors
            otpBoxes.forEach(box => box.classList.remove('error'));

            // Validate OTP
            if (!otp || otp.length === 0) {
                AlertUtils.warning('Incomplete OTP', 'Please enter the OTP code');
                otpBoxes.forEach(box => box.classList.add('error'));
                otpBoxes[0].focus();
                return;
            }

            if (otp.length !== 6) {
                AlertUtils.warning('Incomplete OTP', 'Please enter all 6 digits');
                otpBoxes.forEach(box => box.classList.add('error'));
                return;
            }

            // Set hidden input value
            otpValue.value = otp;

            // Show loading state
            btnText.style.display = 'none';
            btnLoader.style.display = 'inline-flex';
            verifyOtpButton.disabled = true;
            otpBoxes.forEach(box => box.disabled = true);

            // Submit form natively
            otpForm.submit();
        }

        // Form submission
        otpForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitOtpForm();
        });

        // Show error message if exists
        <?php if (isset($error)): ?>
        AlertUtils.error('Verification Failed', <?php echo json_encode($error); ?>, function() {
            // Re-enable form after error
            const btnText = verifyOtpButton.querySelector('.btn-text');
            const btnLoader = verifyOtpButton.querySelector('.btn-loader');
            btnText.style.display = 'inline';
            btnLoader.style.display = 'none';
            verifyOtpButton.disabled = false;
            otpBoxes.forEach(box => box.disabled = false);

            // Clear boxes and refocus
            clearOtpBoxes();
        });

        // Show error styling on boxes
        otpBoxes.forEach(box => box.classList.add('error'));
        <?php endif; ?>
    </script>
</body>
</html>
