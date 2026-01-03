// Unlock Account Page JavaScript
// Displays success message after account unlock

function initUnlockAccount() {
    // Show success notification (no auto-redirect)
    AlertUtils.success(
        'Account Unlocked!',
        'Your account has been successfully unlocked. Click the button below to login.'
    );
}
