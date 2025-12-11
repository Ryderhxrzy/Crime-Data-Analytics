/**
 * Crime Data Analytics - Login Page JavaScript
 * Handles form validation, UI feedback, and loading states
 */

// ===================================
// DOM ELEMENTS
// ===================================
const loginForm = document.getElementById('loginForm');
const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');
const loginButton = document.getElementById('loginButton');
const togglePasswordBtn = document.getElementById('togglePassword');
const emailError = document.getElementById('emailError');
const passwordError = document.getElementById('passwordError');
const loadingOverlay = document.getElementById('loadingOverlay');
const forgotPasswordLink = document.getElementById('forgotPasswordLink');

// ===================================
// PASSWORD TOGGLE
// ===================================
togglePasswordBtn.addEventListener('click', () => {
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);

    const icon = togglePasswordBtn.querySelector('i');
    if (type === 'text') {
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
});

// ===================================
// VALIDATION FUNCTIONS
// ===================================

/**
 * Validate email format
 */
function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Validate password (minimum 6 characters for demo)
 */
function validatePassword(password) {
    return password.length >= 6;
}

/**
 * Show field error
 */
function showFieldError(errorElement, message) {
    errorElement.textContent = message;
    errorElement.classList.add('show');
}

/**
 * Hide field error
 */
function hideFieldError(errorElement) {
    errorElement.textContent = '';
    errorElement.classList.remove('show');
}

/**
 * Show form error message using SweetAlert2
 */
function showFormError(message) {
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: message,
        confirmButtonColor: '#4c8a89',
        confirmButtonText: 'OK'
    });
}

/**
 * Show loading overlay (reusable component)
 */
function showLoadingOverlay(text = 'Processing...') {
    const loadingText = loadingOverlay.querySelector('.loading-text');
    loadingText.textContent = text;
    loadingOverlay.style.display = 'flex';
}

/**
 * Hide loading overlay
 */
function hideLoadingOverlay() {
    loadingOverlay.style.display = 'none';
}

/**
 * Show button loading state
 */
function showButtonLoading() {
    loginButton.disabled = true;
    loginButton.querySelector('.btn-text').style.display = 'none';
    loginButton.querySelector('.btn-loader').style.display = 'flex';
}

/**
 * Hide button loading state
 */
function hideButtonLoading() {
    loginButton.disabled = false;
    loginButton.querySelector('.btn-text').style.display = 'block';
    loginButton.querySelector('.btn-loader').style.display = 'none';
}

// ===================================
// REAL-TIME VALIDATION
// ===================================

// Email validation on blur
emailInput.addEventListener('blur', () => {
    const email = emailInput.value.trim();

    if (!email) {
        showFieldError(emailError, 'Email is required');
    } else if (!validateEmail(email)) {
        showFieldError(emailError, 'Please enter a valid email address');
    } else {
        hideFieldError(emailError);
    }
});

// Password validation on blur
passwordInput.addEventListener('blur', () => {
    const password = passwordInput.value;

    if (!password) {
        showFieldError(passwordError, 'Password is required');
    } else if (!validatePassword(password)) {
        showFieldError(passwordError, 'Password must be at least 6 characters');
    } else {
        hideFieldError(passwordError);
    }
});

// Clear errors on input
emailInput.addEventListener('input', () => {
    hideFieldError(emailError);
});

passwordInput.addEventListener('input', () => {
    hideFieldError(passwordError);
});

// ===================================
// FORM SUBMISSION
// ===================================

loginForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    // Hide any existing errors
    hideFieldError(emailError);
    hideFieldError(passwordError);

    // Get form values
    const email = emailInput.value.trim();
    const password = passwordInput.value;
    const rememberMe = document.getElementById('rememberMe').checked;

    // Validation
    let isValid = true;

    if (!email) {
        showFieldError(emailError, 'Email is required');
        isValid = false;
    } else if (!validateEmail(email)) {
        showFieldError(emailError, 'Please enter a valid email address');
        isValid = false;
    }

    if (!password) {
        showFieldError(passwordError, 'Password is required');
        isValid = false;
    } else if (!validatePassword(password)) {
        showFieldError(passwordError, 'Password must be at least 6 characters');
        isValid = false;
    }

    if (!isValid) {
        showFormError('Please fix the errors above');
        return;
    }

    // Show loading states
    showButtonLoading();

    // Simulate API call (replace with actual API call later)
    try {
        // Simulate network delay
        await new Promise(resolve => setTimeout(resolve, 2000));

        // For demo purposes - simulate login failure
        // In production, you would make an actual API call here
        const loginSuccess = simulateLogin(email, password);

        if (loginSuccess) {
            // Show success (optional full-page loading)
            showLoadingOverlay('Login successful! Redirecting...');

            // Store remember me preference
            if (rememberMe) {
                localStorage.setItem('rememberMe', 'true');
                localStorage.setItem('userEmail', email);
            } else {
                localStorage.removeItem('rememberMe');
                localStorage.removeItem('userEmail');
            }

            // Redirect after 1 second
            setTimeout(() => {
                // window.location.href = 'dashboard.php';
                console.log('Redirecting to dashboard...');
                hideLoadingOverlay();
                alert('Login successful! (Static demo - no database)');
                hideButtonLoading();
            }, 1000);
        } else {
            throw new Error('Invalid email or password');
        }

    } catch (error) {
        hideButtonLoading();
        showFormError(error.message || 'Login failed. Please try again.');

        // Shake animation for error
        loginForm.style.animation = 'shake 0.5s';
        setTimeout(() => {
            loginForm.style.animation = '';
        }, 500);
    }
});

// ===================================
// SIMULATE LOGIN (FOR DEMO)
// ===================================

/**
 * Simulate login validation
 * In production, replace this with actual API call
 */
function simulateLogin(email, password) {
    // Demo credentials (remove in production)
    const demoCredentials = [
        { email: 'admin@example.com', password: 'password123' },
        { email: 'user@example.com', password: 'user123' },
        { email: 'demo@example.com', password: 'demo123' }
    ];

    return demoCredentials.some(
        cred => cred.email === email && cred.password === password
    );
}

// ===================================
// FORGOT PASSWORD HANDLER
// ===================================

forgotPasswordLink.addEventListener('click', (e) => {
    e.preventDefault();

    const email = emailInput.value.trim();

    if (!email) {
        showFormError('Please enter your email address first');
        emailInput.focus();
        return;
    }

    if (!validateEmail(email)) {
        showFormError('Please enter a valid email address');
        emailInput.focus();
        return;
    }

    // Show loading
    showLoadingOverlay('Sending password reset email...');

    // Simulate sending reset email
    setTimeout(() => {
        hideLoadingOverlay();
        alert(`Password reset link has been sent to ${email}\n\n(Static demo - no email sent)`);
    }, 1500);
});

// ===================================
// AUTO-FILL REMEMBERED EMAIL
// ===================================

function autoFillRememberedEmail() {
    const rememberMe = localStorage.getItem('rememberMe');
    const userEmail = localStorage.getItem('userEmail');

    if (rememberMe === 'true' && userEmail) {
        emailInput.value = userEmail;
        document.getElementById('rememberMe').checked = true;
    }
}

// ===================================
// SHAKE ANIMATION (FOR ERRORS)
// ===================================

const style = document.createElement('style');
style.textContent = `
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
        20%, 40%, 60%, 80% { transform: translateX(5px); }
    }
`;
document.head.appendChild(style);

// ===================================
// KEYBOARD SHORTCUTS
// ===================================

document.addEventListener('keydown', (e) => {
    // Ctrl + Enter to submit form
    if (e.ctrlKey && e.key === 'Enter') {
        e.preventDefault();
        loginForm.dispatchEvent(new Event('submit'));
    }
});

// ===================================
// INITIALIZATION
// ===================================

document.addEventListener('DOMContentLoaded', () => {
    autoFillRememberedEmail();

    // Focus email input on load
    emailInput.focus();

    // Console info for demo
    console.log('='.repeat(50));
    console.log('Crime Data Analytics - Login Page (Static Demo)');
    console.log('='.repeat(50));
    console.log('Demo Credentials:');
    console.log('1. admin@example.com / password123');
    console.log('2. user@example.com / user123');
    console.log('3. demo@example.com / demo123');
    console.log('='.repeat(50));
    console.log('Keyboard Shortcuts:');
    console.log('Ctrl + Enter: Submit form');
    console.log('='.repeat(50));
});

// ===================================
// EXPORT LOADING OVERLAY FUNCTIONS
// (For use in other pages)
// ===================================

window.showLoading = showLoadingOverlay;
window.hideLoading = hideLoadingOverlay;
