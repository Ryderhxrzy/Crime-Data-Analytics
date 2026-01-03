// Login Page Flash Messages Handler
// Displays success and error messages from URL parameters or PHP session

function initLoginMessages(flashSuccess, flashError) {
    // Handle URL parameters for success/error messages
    const urlParams = new URLSearchParams(window.location.search);
    const success = urlParams.get('success');
    const error = urlParams.get('error');

    // Handle flash messages from PHP session
    if (flashSuccess) {
        AlertUtils.successWithTimer('Success!', flashSuccess, 3000);
    } else if (flashError) {
        AlertUtils.error('Error!', flashError);
    }

    // Handle URL parameter messages
    if (success) {
        AlertUtils.successWithTimer('Success!', success, 3000, function() {
            // Clear URL parameters
            window.history.replaceState({}, document.title, window.location.pathname);
        });
    }

    if (error) {
        AlertUtils.error('Error!', error, function() {
            // Clear URL parameters
            window.history.replaceState({}, document.title, window.location.pathname);
        });
    }
}
