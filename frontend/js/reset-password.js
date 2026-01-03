// Reset Password Page JavaScript
// Handles password visibility toggle and form validation

function initResetPassword(flashSuccess, flashError) {
    // Handle flash messages
    if (flashSuccess) {
        AlertUtils.success('Success!', flashSuccess);
    } else if (flashError) {
        AlertUtils.error('Error!', flashError);
    }

    // Toggle password visibility for new password
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

    // Toggle password visibility for confirm password
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
}
