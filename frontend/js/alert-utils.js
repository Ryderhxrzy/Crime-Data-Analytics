/**
 * Reusable SweetAlert2 Utilities
 * Provides consistent alert styling across the application
 */

const AlertUtils = {
    /**
     * Show success message
     * @param {string} title - Alert title
     * @param {string} text - Alert message
     * @param {function} callback - Optional callback after close
     */
    success: function(title, text, callback = null) {
        Swal.fire({
            icon: 'success',
            title: title,
            text: text,
            confirmButtonColor: '#4c8a89',
            confirmButtonText: 'OK',
            allowOutsideClick: true,
            allowEscapeKey: true
        }).then(() => {
            if (callback) callback();
        });
    },

    /**
     * Show error message
     * @param {string} title - Alert title
     * @param {string} text - Alert message
     * @param {function} callback - Optional callback after close
     */
    error: function(title, text, callback = null) {
        Swal.fire({
            icon: 'error',
            title: title,
            text: text,
            confirmButtonColor: '#4c8a89',
            confirmButtonText: 'OK',
            allowOutsideClick: true,
            allowEscapeKey: true
        }).then(() => {
            if (callback) callback();
        });
    },

    /**
     * Show warning message
     * @param {string} title - Alert title
     * @param {string} text - Alert message
     * @param {function} callback - Optional callback after close
     */
    warning: function(title, text, callback = null) {
        Swal.fire({
            icon: 'warning',
            title: title,
            text: text,
            confirmButtonColor: '#4c8a89',
            confirmButtonText: 'OK',
            allowOutsideClick: true,
            allowEscapeKey: true
        }).then(() => {
            if (callback) callback();
        });
    },

    /**
     * Show info message
     * @param {string} title - Alert title
     * @param {string} text - Alert message
     * @param {function} callback - Optional callback after close
     */
    info: function(title, text, callback = null) {
        Swal.fire({
            icon: 'info',
            title: title,
            text: text,
            confirmButtonColor: '#4c8a89',
            confirmButtonText: 'OK',
            allowOutsideClick: true,
            allowEscapeKey: true
        }).then(() => {
            if (callback) callback();
        });
    },

    /**
     * Show confirmation dialog
     * @param {string} title - Alert title
     * @param {string} text - Alert message
     * @param {function} onConfirm - Callback when confirmed
     * @param {function} onCancel - Optional callback when cancelled
     */
    confirm: function(title, text, onConfirm, onCancel = null) {
        Swal.fire({
            icon: 'question',
            title: title,
            text: text,
            showCancelButton: true,
            confirmButtonColor: '#4c8a89',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes',
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.isConfirmed && onConfirm) {
                onConfirm();
            } else if (result.isDismissed && onCancel) {
                onCancel();
            }
        });
    },

    /**
     * Show success with auto-close timer
     * @param {string} title - Alert title
     * @param {string} text - Alert message
     * @param {number} timer - Timer in milliseconds (default: 3000)
     * @param {function} callback - Optional callback after close
     */
    successWithTimer: function(title, text, timer = 3000, callback = null) {
        Swal.fire({
            icon: 'success',
            title: title,
            text: text,
            confirmButtonColor: '#4c8a89',
            confirmButtonText: 'OK',
            timer: timer,
            timerProgressBar: true,
            allowOutsideClick: true,
            allowEscapeKey: true
        }).then(() => {
            if (callback) callback();
        });
    },

    /**
     * Show loading message
     * @param {string} title - Alert title
     * @param {string} text - Alert message
     */
    loading: function(title = 'Please wait...', text = 'Processing your request') {
        Swal.fire({
            title: title,
            text: text,
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    },

    /**
     * Close any open alert
     */
    close: function() {
        Swal.close();
    }
};
