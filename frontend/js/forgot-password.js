// Forgot Password Page JavaScript
// Handles email submission, OTP verification, and timer functionality

function initForgotPassword(flashSuccess, flashError) {
    // Handle flash messages from PHP session
    if (flashSuccess) {
        AlertUtils.successWithTimer('Success!', flashSuccess, 5000);
    } else if (flashError) {
        AlertUtils.error('Error!', flashError);
    }

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
}
