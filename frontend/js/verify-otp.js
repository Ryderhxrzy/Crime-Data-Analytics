// OTP Verification Page JavaScript
// Handles OTP input boxes, timer, validation, and form submission

// Initialize OTP verification functionality
function initVerifyOtp(remainingSeconds, errorMessage) {
    // DOM Elements
    const otpBoxes = document.querySelectorAll('.otp-box');
    const otpForm = document.getElementById('otpForm');
    const otpValue = document.getElementById('otpValue');
    const verifyOtpButton = document.getElementById('verifyOtpButton');

    // OTP Box Event Handlers
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

    // OTP Timer Function
    let otpTimerInterval = null;
    let otpExpiryTime = Date.now() + (remainingSeconds * 1000);

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

    // Start timer on page load
    startOtpTimer();

    // Focus first box only if not expired
    if (remainingSeconds > 0) {
        otpBoxes[0].focus();
    }

    // Show error message if exists
    if (errorMessage) {
        AlertUtils.error('Verification Failed', errorMessage, function() {
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
    }
}
