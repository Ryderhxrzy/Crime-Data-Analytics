<?php
/**
 * Email Mailer Utility
 * Handles sending emails using PHPMailer with credentials from .env
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load vendor autoload from project root
$vendorPath = __DIR__ . '/../../vendor/autoload.php';
if (!file_exists($vendorPath)) {
    error_log("Vendor autoload not found at: " . $vendorPath);
    throw new Exception("PHPMailer dependencies not found. Please run 'composer install'.");
}
require_once $vendorPath;
require_once __DIR__ . '/../config.php';

class Mailer {
    private $mail;
    private $app_env;
    private $base_url;

    public function __construct() {
        require_once __DIR__ . '/../helpers/url-helper.php';

        $this->mail = new PHPMailer(true);
        $this->app_env = $_ENV['APP_ENV'] ?? 'local';
        $this->base_url = getBaseUrl();
        $this->configure();
    }

    private function configure() {
        try {
            // Server settings
            $this->mail->isSMTP();
            $this->mail->Host = $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com';
            $this->mail->SMTPAuth = true;
            $this->mail->Username = $_ENV['MAIL_USERNAME'] ?? '';
            $this->mail->Password = $_ENV['MAIL_PASSWORD'] ?? '';
            $this->mail->SMTPSecure = $_ENV['MAIL_ENCRYPTION'] ?? 'ssl';
            $this->mail->Port = $_ENV['MAIL_PORT'] ?? 465;

            // Debug mode
            if (($_ENV['MAIL_DEBUG'] ?? 'false') === 'true') {
                $this->mail->SMTPDebug = 2;
            }

            // Default sender
            $fromAddress = $_ENV['MAIL_FROM_ADDRESS'] ?? 'no-reply@alertaraqc.com';
            $fromName = $_ENV['MAIL_FROM_NAME'] ?? 'AlerTara QC';
            $this->mail->setFrom($fromAddress, $fromName);

            // Character set
            $this->mail->CharSet = 'UTF-8';
            $this->mail->isHTML(true);

        } catch (Exception $e) {
            error_log("Mailer configuration error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Send OTP email for password reset
     */
    public function sendOTP($email, $fullName, $otp) {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($email, $fullName);

            $this->mail->Subject = 'Password Reset OTP - AlerTara QC Crime Data Analytics';

            $this->mail->Body = $this->getOTPEmailTemplate($fullName, $otp);
            $this->mail->AltBody = "Hello {$fullName},\n\n"
                . "Your password reset OTP code is: {$otp}\n\n"
                . "This code will expire in 10 minutes.\n\n"
                . "If you did not request a password reset, please ignore this email.\n\n"
                . "Best regards,\nAlerTara QC Team";

            $this->mail->send();
            return true;

        } catch (Exception $e) {
            error_log("Failed to send OTP email: " . $this->mail->ErrorInfo);
            throw new Exception("Failed to send email: " . $this->mail->ErrorInfo);
        }
    }

    /**
     * Send password reset link (when OTP is verified)
     */
    public function sendResetLink($email, $fullName, $resetToken) {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($email, $fullName);

            $resetUrl = $this->base_url . "frontend/user-page/reset-password.php?token=" . $resetToken;

            $this->mail->Subject = 'Reset Your Password - AlerTara QC Crime Data Analytics';

            $this->mail->Body = $this->getResetLinkEmailTemplate($fullName, $resetUrl);
            $this->mail->AltBody = "Hello {$fullName},\n\n"
                . "Your OTP has been verified. Click the link below to reset your password:\n\n"
                . "{$resetUrl}\n\n"
                . "This link will expire in 1 hour.\n\n"
                . "If you did not request a password reset, please ignore this email.\n\n"
                . "Best regards,\nAlerTara QC Team";

            $this->mail->send();
            return true;

        } catch (Exception $e) {
            error_log("Failed to send reset link email: " . $this->mail->ErrorInfo);
            throw new Exception("Failed to send email: " . $this->mail->ErrorInfo);
        }
    }

    /**
     * OTP Email HTML Template
     */
    private function getOTPEmailTemplate($fullName, $otp) {
        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset OTP</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background-color: #4c8a89; padding: 30px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 24px;">AlerTara QC</h1>
                            <p style="margin: 5px 0 0 0; color: #ffffff; font-size: 14px;">Crime Data Analytics</p>
                        </td>
                    </tr>
                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <h2 style="margin: 0 0 20px 0; color: #333333; font-size: 20px;">Password Reset Request</h2>
                            <p style="margin: 0 0 20px 0; color: #666666; font-size: 16px; line-height: 1.5;">
                                Hello <strong>{$fullName}</strong>,
                            </p>
                            <p style="margin: 0 0 20px 0; color: #666666; font-size: 16px; line-height: 1.5;">
                                We received a request to reset your password. Use the following One-Time Password (OTP) to verify your identity:
                            </p>
                            <!-- OTP Code -->
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding: 20px 0;">
                                        <div style="background-color: #f8f9fa; border: 2px dashed #4c8a89; border-radius: 8px; padding: 20px; display: inline-block;">
                                            <span style="font-size: 36px; font-weight: bold; color: #4c8a89; letter-spacing: 8px;">{$otp}</span>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin: 20px 0; color: #666666; font-size: 14px; line-height: 1.5;">
                                <strong>Important:</strong> This OTP will expire in <strong>10 minutes</strong>.
                            </p>
                            <p style="margin: 20px 0 0 0; color: #666666; font-size: 14px; line-height: 1.5;">
                                If you did not request a password reset, please ignore this email or contact your administrator if you have concerns.
                            </p>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 20px 30px; text-align: center; border-top: 1px solid #e0e0e0;">
                            <p style="margin: 0; color: #999999; font-size: 12px;">
                                This is an automated message from AlerTara QC Crime Data Analytics.<br>
                                Please do not reply to this email.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    }

    /**
     * Send generic email with custom subject and HTML body
     */
    public function sendGenericEmail($email, $fullName, $subject, $htmlBody) {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($email, $fullName);

            $this->mail->Subject = $subject;
            $this->mail->Body = $this->getGenericEmailTemplate($fullName, $htmlBody);
            $this->mail->AltBody = strip_tags($htmlBody);

            $this->mail->send();
            return true;

        } catch (Exception $e) {
            error_log("Failed to send email: " . $this->mail->ErrorInfo);
            throw new Exception("Failed to send email: " . $this->mail->ErrorInfo);
        }
    }

    /**
     * Generic Email HTML Template
     */
    private function getGenericEmailTemplate($fullName, $content) {
        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlerTara QC Crime Data Analytics</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background-color: #4c8a89; padding: 30px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 24px;">AlerTara QC</h1>
                            <p style="margin: 5px 0 0 0; color: #ffffff; font-size: 14px;">Crime Data Analytics</p>
                        </td>
                    </tr>
                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px; color: #666666; font-size: 16px; line-height: 1.5;">
                            {$content}
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 20px 30px; text-align: center; border-top: 1px solid #e0e0e0;">
                            <p style="margin: 0; color: #999999; font-size: 12px;">
                                This is an automated message from AlerTara QC Crime Data Analytics.<br>
                                Please do not reply to this email.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    }

    /**
     * Reset Link Email HTML Template
     */
    private function getResetLinkEmailTemplate($fullName, $resetUrl) {
        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background-color: #4c8a89; padding: 30px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 24px;">AlerTara QC</h1>
                            <p style="margin: 5px 0 0 0; color: #ffffff; font-size: 14px;">Crime Data Analytics</p>
                        </td>
                    </tr>
                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <h2 style="margin: 0 0 20px 0; color: #333333; font-size: 20px;">OTP Verified Successfully!</h2>
                            <p style="margin: 0 0 20px 0; color: #666666; font-size: 16px; line-height: 1.5;">
                                Hello <strong>{$fullName}</strong>,
                            </p>
                            <p style="margin: 0 0 20px 0; color: #666666; font-size: 16px; line-height: 1.5;">
                                Your OTP has been verified successfully. Click the button below to reset your password:
                            </p>
                            <!-- Reset Button -->
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding: 30px 0;">
                                        <a href="{$resetUrl}" style="background-color: #4c8a89; color: #ffffff; text-decoration: none; padding: 15px 40px; border-radius: 5px; font-size: 16px; font-weight: bold; display: inline-block;">Reset Password</a>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin: 20px 0; color: #666666; font-size: 14px; line-height: 1.5;">
                                Or copy and paste this link into your browser:
                            </p>
                            <p style="margin: 0 0 20px 0; color: #4c8a89; font-size: 12px; word-break: break-all;">
                                {$resetUrl}
                            </p>
                            <p style="margin: 20px 0; color: #666666; font-size: 14px; line-height: 1.5;">
                                <strong>Important:</strong> This link will expire in <strong>1 hour</strong>.
                            </p>
                            <p style="margin: 20px 0 0 0; color: #666666; font-size: 14px; line-height: 1.5;">
                                If you did not request a password reset, please contact your administrator immediately.
                            </p>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 20px 30px; text-align: center; border-top: 1px solid #e0e0e0;">
                            <p style="margin: 0; color: #999999; font-size: 12px;">
                                This is an automated message from AlerTara QC Crime Data Analytics.<br>
                                Please do not reply to this email.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    }
}
?>
