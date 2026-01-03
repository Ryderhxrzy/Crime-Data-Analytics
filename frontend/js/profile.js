// Profile Page JavaScript
// Handles success/error message display from URL parameters

function initProfile() {
    // Check for success/error messages in URL
    const urlParams = new URLSearchParams(window.location.search);
    const success = urlParams.get('success');
    const error = urlParams.get('error');

    if (success) {
        AlertUtils.success('Success!', success, function() {
            // Remove query parameters from URL
            window.history.replaceState({}, document.title, window.location.pathname);
        });
    }

    if (error) {
        AlertUtils.error('Error!', error, function() {
            // Remove query parameters from URL
            window.history.replaceState({}, document.title, window.location.pathname);
        });
    }
}
