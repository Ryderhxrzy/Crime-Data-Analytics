<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - Crime Data Analytics</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/legal.css">
    <link rel="icon" type="image/x-icon" href="../image/favicon.ico">
</head>
<body>
    <div class="legal-container">
        <div class="legal-header">
            <a href="../../index.php" class="back-link">← Back to Login</a>
            <h1>Privacy Policy</h1>
            <p class="last-updated">Last Updated: December 26, 2025</p>
        </div>

        <div class="legal-content">
            <section>
                <h2>1. Introduction</h2>
                <p>
                    Welcome to Crime Data Analytics ("we," "our," or "us"). This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our crime data analytics platform, including when you authenticate using Google OAuth.
                </p>
                <p>
                    Crime Data Analytics is a platform designed for AlerTaraQC to analyze, visualize, and manage crime-related data. We are committed to protecting your privacy and handling your data in an open and transparent manner.
                </p>
            </section>

            <section>
                <h2>2. Information We Collect</h2>

                <h3>2.1 Information You Provide Directly</h3>
                <ul>
                    <li><strong>Account Information:</strong> When you create an account or log in, we collect your email address, full name, and create a unique user ID.</li>
                    <li><strong>Login Credentials:</strong> If you choose to login with email/password, we store your hashed password securely using bcrypt encryption.</li>
                </ul>

                <h3>2.2 Information from Google OAuth</h3>
                <p>When you sign in using Google OAuth, we collect:</p>
                <ul>
                    <li>Your email address</li>
                    <li>Your full name</li>
                    <li>Your Google account ID (for verification purposes)</li>
                    <li>Profile picture URL (optional, not currently stored)</li>
                </ul>
                <p>
                    We only request the minimum permissions necessary: <code>userinfo.email</code> and <code>userinfo.profile</code>. We do not access your Google Drive, Calendar, Contacts, or any other Google services.
                </p>

                <h3>2.3 Automatically Collected Information</h3>
                <ul>
                    <li><strong>Login Activity:</strong> We record the date and time of your last login for security purposes.</li>
                    <li><strong>Session Data:</strong> We use PHP sessions to maintain your login state. Sessions expire after 1 hour of inactivity.</li>
                    <li><strong>Usage Data:</strong> We may collect information about how you interact with the platform (pages visited, features used) for analytics and improvement purposes.</li>
                </ul>
            </section>

            <section>
                <h2>3. How We Use Your Information</h2>
                <p>We use the collected information for the following purposes:</p>
                <ul>
                    <li><strong>Authentication & Access Control:</strong> To verify your identity and provide access to the platform.</li>
                    <li><strong>Account Management:</strong> To create, maintain, and manage your user account.</li>
                    <li><strong>Security:</strong> To protect against unauthorized access, detect fraudulent activity, and ensure platform security.</li>
                    <li><strong>Communication:</strong> To send you important notifications about your account or the service.</li>
                    <li><strong>Analytics & Improvement:</strong> To understand how the platform is used and improve functionality.</li>
                    <li><strong>Crime Data Analysis:</strong> To provide you with crime analytics, trends, and visualizations as part of the core service.</li>
                </ul>
            </section>

            <section>
                <h2>4. How We Store Your Information</h2>
                <ul>
                    <li><strong>Database Storage:</strong> Your account information is stored in a MySQL database (<code>admin_users</code> table).</li>
                    <li><strong>Password Security:</strong> Passwords are hashed using PHP's <code>PASSWORD_DEFAULT</code> algorithm (bcrypt) and are never stored in plain text.</li>
                    <li><strong>Session Storage:</strong> Active sessions are stored server-side using PHP sessions.</li>
                    <li><strong>Environment Variables:</strong> Sensitive configuration data (database credentials, OAuth secrets) are stored in environment files that are not publicly accessible.</li>
                </ul>
            </section>

            <section>
                <h2>5. Data Sharing and Disclosure</h2>
                <p>We do not sell, rent, or trade your personal information. We may share your information only in the following circumstances:</p>
                <ul>
                    <li><strong>With Your Consent:</strong> When you explicitly authorize us to share information.</li>
                    <li><strong>Legal Obligations:</strong> When required by law, court order, or government request.</li>
                    <li><strong>Service Providers:</strong> With trusted third-party service providers who assist in operating our platform (e.g., hosting providers), under strict confidentiality agreements.</li>
                    <li><strong>Security & Fraud Prevention:</strong> To protect the rights, property, or safety of our users and the public.</li>
                </ul>
            </section>

            <section>
                <h2>6. Google OAuth and Third-Party Services</h2>
                <p>
                    When you use Google OAuth to sign in, your authentication is handled by Google. Google's use of information received from Google APIs adheres to the <a href="https://developers.google.com/terms/api-services-user-data-policy" target="_blank">Google API Services User Data Policy</a>, including the Limited Use requirements.
                </p>
                <p>We use Google OAuth solely for authentication purposes and do not:</p>
                <ul>
                    <li>Access your Google account beyond basic profile information</li>
                    <li>Store your Google password</li>
                    <li>Access other Google services (Gmail, Drive, Calendar, etc.)</li>
                    <li>Share your Google account information with third parties</li>
                </ul>
            </section>

            <section>
                <h2>7. Data Retention</h2>
                <ul>
                    <li><strong>Active Accounts:</strong> We retain your account information as long as your account is active.</li>
                    <li><strong>Inactive Accounts:</strong> Accounts may be marked as inactive or suspended but are not automatically deleted.</li>
                    <li><strong>Session Data:</strong> Sessions expire after 1 hour of inactivity and are automatically cleared.</li>
                    <li><strong>Deletion Requests:</strong> You may request account deletion by contacting the system administrator.</li>
                </ul>
            </section>

            <section>
                <h2>8. Your Rights and Choices</h2>
                <p>You have the following rights regarding your personal information:</p>
                <ul>
                    <li><strong>Access:</strong> You can request access to the personal information we hold about you.</li>
                    <li><strong>Correction:</strong> You can request correction of inaccurate information.</li>
                    <li><strong>Deletion:</strong> You can request deletion of your account and personal data.</li>
                    <li><strong>Withdraw Consent:</strong> You can revoke Google OAuth access at any time through your Google Account settings.</li>
                    <li><strong>Data Portability:</strong> You can request a copy of your data in a structured format.</li>
                </ul>
                <p>To exercise these rights, please contact the system administrator.</p>
            </section>

            <section>
                <h2>9. Security Measures</h2>
                <p>We implement appropriate technical and organizational measures to protect your information:</p>
                <ul>
                    <li>Password hashing using industry-standard bcrypt algorithm</li>
                    <li>CSRF protection for OAuth authentication flows</li>
                    <li>Secure session management with automatic timeouts</li>
                    <li>Database access controls and authentication</li>
                    <li>Environment-based configuration for sensitive credentials</li>
                    <li>Regular security updates and monitoring</li>
                </ul>
                <p>However, no method of transmission over the Internet or electronic storage is 100% secure. We cannot guarantee absolute security.</p>
            </section>

            <section>
                <h2>10. Cookies and Tracking</h2>
                <p>
                    We use PHP sessions (which may use cookies) to maintain your login state. These session cookies are essential for the platform to function and are deleted when you log out or after 1 hour of inactivity.
                </p>
                <p>
                    We may use local storage for the "Remember Me" feature, which stores your email address locally on your device if you choose to enable this option.
                </p>
            </section>

            <section>
                <h2>11. Children's Privacy</h2>
                <p>
                    Our service is not intended for individuals under the age of 18. We do not knowingly collect personal information from children. If we become aware that we have collected information from a child, we will take steps to delete such information.
                </p>
            </section>

            <section>
                <h2>12. International Data Transfers</h2>
                <p>
                    Your information may be transferred to and processed in countries other than your own. By using our service, you consent to such transfers. We ensure appropriate safeguards are in place for such transfers.
                </p>
            </section>

            <section>
                <h2>13. Changes to This Privacy Policy</h2>
                <p>
                    We may update this Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "Last Updated" date. We encourage you to review this Privacy Policy periodically.
                </p>
            </section>

            <section>
                <h2>14. Contact Information</h2>
                <p>If you have questions or concerns about this Privacy Policy or our data practices, please contact:</p>
                <div class="contact-info">
                    <p><strong>AlerTaraQC - Crime Data Analytics</strong></p>
                    <p>Email: <a href="mailto:admin@alertaraqc.com">admin@alertaraqc.com</a></p>
                    <p>Website: <a href="https://www.alertaraqc.com" target="_blank">www.alertaraqc.com</a></p>
                </div>
            </section>

            <section>
                <h2>15. Compliance</h2>
                <p>
                    This Privacy Policy is designed to comply with applicable data protection laws and regulations, including but not limited to the General Data Protection Regulation (GDPR) and the Data Privacy Act of 2012 (Philippines).
                </p>
            </section>
        </div>

        <div class="legal-footer">
            <p>&copy; 2025 Crime Data Analytics - AlerTaraQC. All rights reserved.</p>
            <div class="footer-links">
                <a href="privacy-policy.php">Privacy Policy</a>
                <span>•</span>
                <a href="terms-of-use.php">Terms of Use</a>
            </div>
        </div>
    </div>
</body>
</html>
