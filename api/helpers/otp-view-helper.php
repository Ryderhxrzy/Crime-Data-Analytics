<?php
/**
 * OTP View Helper Functions
 * Provides reusable functions for OTP timer display and validation
 */

/**
 * Get the remaining seconds for OTP validity
 * @param mysqli $mysqli Database connection
 * @param int $user_id User ID to check OTP for
 * @return int Remaining seconds (0 if expired or no OTP found)
 */
function getOtpRemainingSeconds($mysqli, $user_id) {
    $stmt = $mysqli->prepare("SELECT expires_at FROM crime_department_otp_verification WHERE admin_user_id = ? AND is_used = 0 ORDER BY created_at DESC LIMIT 1");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $otp_data = $result->fetch_assoc();
    $stmt->close();

    if (!$otp_data) {
        return 0;
    }

    $expires_at = strtotime($otp_data['expires_at']);
    $remaining = $expires_at - time();

    return max(0, $remaining);
}

/**
 * Prepare OTP timer display data for the view
 * @param int $remaining_seconds Remaining seconds for OTP validity
 * @return array View data with timer_class, timer_display, timer_expired_display, input_disabled, button_disabled
 */
function prepareOtpTimerData($remaining_seconds) {
    $is_expired = $remaining_seconds <= 0;

    if ($is_expired) {
        return [
            'timer_class' => 'expired',
            'timer_display' => '00:00',
            'timer_expired_display' => 'block',
            'input_disabled' => 'disabled',
            'button_disabled' => 'disabled'
        ];
    }

    $minutes = floor($remaining_seconds / 60);
    $seconds = $remaining_seconds % 60;
    $timer_display = sprintf('%02d:%02d', $minutes, $seconds);

    // Add warning class if less than 60 seconds remaining
    $timer_class = $remaining_seconds <= 60 ? 'warning' : '';

    return [
        'timer_class' => $timer_class,
        'timer_display' => $timer_display,
        'timer_expired_display' => 'none',
        'input_disabled' => '',
        'button_disabled' => ''
    ];
}
?>
