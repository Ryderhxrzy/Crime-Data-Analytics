<?php
/**
 * Settings Page View
 * Displays user settings and preferences
 */

// Load settings data controller
require_once '../../api/retrieve/settings-data.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings | Crime Dep.</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/admin-header.css">
    <link rel="stylesheet" href="../css/hero.css">
    <link rel="stylesheet" href="../css/sidebar-footer.css">
    <link rel="stylesheet" href="../css/settings.css">
    <link rel="icon" type="image/x-icon" href="../image/favicon.ico">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Initialize theme from database before page renders -->
    <script>
        (function() {
            const dbTheme = '<?php echo htmlspecialchars($user_settings['theme'] ?? 'light'); ?>';
            const footerTheme = dbTheme === 'auto' ? 'system' : dbTheme;

            // Set HTML attribute for immediate theme application
            document.documentElement.setAttribute('data-theme', footerTheme);

            // Sync with localStorage
            localStorage.setItem('theme', footerTheme);
        })();
    </script>
</head>
<body>
    <?php include '../includes/sidebar.php' ?>
    <?php include '../includes/admin-header.php'; ?>

    <div class="main-content">
        <div class="main-container">
            <div class="title">
                <nav class="breadcrumb" aria-label="Breadcrumb">
                    <ol class="breadcrumb-list">
                        <li class="breadcrumb-item">
                            <a href="/" class="breadcrumb-link">
                                <span>Account</span>
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <span>Settings</span>
                        </li>
                    </ol>
                </nav>
                <h1>Settings</h1>
                <p>Manage your admin account preferences, security, and system settings</p>
            </div>

            <div class="sub-container">
                <div class="page-content">
                    <!-- Settings Navigation Tabs -->
                    <div class="settings-tabs">
                        <button class="settings-tab active" data-tab="account">
                            <i class="fas fa-user-circle"></i>
                            Account
                        </button>
                        <button class="settings-tab" data-tab="security">
                            <i class="fas fa-shield-alt"></i>
                            Security
                        </button>
                        <button class="settings-tab" data-tab="notifications">
                            <i class="fas fa-bell"></i>
                            Notifications
                        </button>
                        <button class="settings-tab" data-tab="preferences">
                            <i class="fas fa-cog"></i>
                            Preferences
                        </button>
                    </div>

                    <!-- Account Settings Tab -->
                    <div class="settings-content active" id="account-tab">
                        <div class="settings-section">
                            <div class="section-header">
                                <h3 class="section-title">
                                    <i class="fas fa-user-circle"></i>
                                    Admin Account Information
                                </h3>
                                <p class="section-description">View and manage your admin account details</p>
                            </div>

                            <form id="accountForm" class="settings-form">
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="fas fa-envelope"></i>
                                            Email Address
                                        </label>
                                        <input type="email" class="form-input" value="<?php echo htmlspecialchars($user_data['email']); ?>" readonly>
                                        <small class="form-hint">Email cannot be changed. Contact administrator if needed.</small>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="fas fa-user"></i>
                                            Full Name
                                        </label>
                                        <input type="text" id="full_name" name="full_name" class="form-input" value="<?php echo htmlspecialchars($user_data['full_name']); ?>" required>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="fas fa-phone"></i>
                                            Phone Number
                                        </label>
                                        <input type="tel" id="phone_number" name="phone_number" class="form-input" value="<?php echo htmlspecialchars($additional_info['phone_number'] ?? ''); ?>">
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="fas fa-map-marker-alt"></i>
                                            Address
                                        </label>
                                        <input type="text" id="address" name="address" class="form-input" value="<?php echo htmlspecialchars($additional_info['address'] ?? ''); ?>">
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="fas fa-building"></i>
                                            Department
                                        </label>
                                        <input type="text" id="department" name="department" class="form-input" value="<?php echo htmlspecialchars($additional_info['department'] ?? ''); ?>">
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="fas fa-briefcase"></i>
                                            Position
                                        </label>
                                        <input type="text" id="position" name="position" class="form-input" value="<?php echo htmlspecialchars($additional_info['position'] ?? ''); ?>">
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="fas fa-shield-alt"></i>
                                            Role
                                        </label>
                                        <input type="text" class="form-input" value="<?php echo ucfirst(str_replace('_', ' ', $user_data['role'])); ?>" readonly>
                                        <small class="form-hint">Role is assigned by system administrators</small>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="fas fa-sign-in-alt"></i>
                                            Login Method
                                        </label>
                                        <input type="text" class="form-input" value="<?php echo ucfirst($user_data['registration_type']); ?>" readonly>
                                    </div>

                                    <div class="form-group" style="grid-column: 1 / -1;">
                                        <label class="form-label">
                                            <i class="fas fa-info-circle"></i>
                                            Bio
                                        </label>
                                        <textarea id="bio" name="bio" class="form-input" rows="4" style="resize: vertical;"><?php echo htmlspecialchars($additional_info['bio'] ?? ''); ?></textarea>
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="btn-login">
                                        <span class="btn-text">
                                            <i class="fas fa-save"></i>
                                            Save Changes
                                        </span>
                                        <span class="btn-loader" style="display: none;">
                                            <span class="spinner"></span>
                                            Saving...
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Profile Picture Section -->
                        <div class="settings-section">
                            <div class="section-header">
                                <h3 class="section-title">
                                    <i class="fas fa-image"></i>
                                    Profile Picture
                                </h3>
                                <p class="section-description">Upload a new profile picture</p>
                            </div>

                            <div class="profile-picture-section">
                                <div class="current-picture">
                                    <img src="<?php echo htmlspecialchars('../image/profile/' . $profile_picture); ?>" alt="Profile Picture" id="profilePicturePreview">
                                </div>
                                <div class="picture-actions">
                                    <button type="button" class="btn-login" id="uploadPictureBtn">
                                        <span class="btn-text">
                                            <i class="fas fa-upload"></i>
                                            Upload New Picture
                                        </span>
                                    </button>
                                    <input type="file" id="profilePictureInput" accept="image/*" style="display: none;">
                                    <small class="form-hint">Recommended: Square image, at least 256x256px. Max 5MB</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Security Settings Tab -->
                    <div class="settings-content" id="security-tab">
                        <!-- Change Password Section -->
                        <?php if ($user_data['registration_type'] === 'email'): ?>
                        <div class="settings-section">
                            <div class="section-header">
                                <h3 class="section-title">
                                    <i class="fas fa-lock"></i>
                                    Change Password
                                </h3>
                                <p class="section-description">Update your password to keep your account secure</p>
                            </div>

                            <form id="passwordForm" class="settings-form">
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="fas fa-key"></i>
                                            Current Password
                                        </label>
                                        <div class="password-input-wrapper">
                                            <input type="password" id="current_password" name="current_password" class="form-input" required>
                                            <button type="button" class="toggle-password" data-target="current_password">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="fas fa-lock"></i>
                                            New Password
                                        </label>
                                        <div class="password-input-wrapper">
                                            <input type="password" id="new_password" name="new_password" class="form-input" minlength="8" required>
                                            <button type="button" class="toggle-password" data-target="new_password">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        <small class="form-hint">Minimum 8 characters</small>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="fas fa-lock"></i>
                                            Confirm New Password
                                        </label>
                                        <div class="password-input-wrapper">
                                            <input type="password" id="confirm_password" name="confirm_password" class="form-input" minlength="8" required>
                                            <button type="button" class="toggle-password" data-target="confirm_password">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="btn-login">
                                        <span class="btn-text">
                                            <i class="fas fa-save"></i>
                                            Update Password
                                        </span>
                                        <span class="btn-loader" style="display: none;">
                                            <span class="spinner"></span>
                                            Updating...
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </div>
                        <?php else: ?>
                        <div class="settings-section">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <div>
                                    <strong>Google Account</strong>
                                    <p>You're signed in with Google. Password management is handled through your Google account.</p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Two-Factor Authentication -->
                        <div class="settings-section">
                            <div class="section-header">
                                <h3 class="section-title">
                                    <i class="fas fa-mobile-alt"></i>
                                    Two-Factor Authentication
                                </h3>
                                <p class="section-description">Add an extra layer of security to your account</p>
                            </div>

                            <?php if ($user_data['registration_type'] === 'email'): ?>
                            <div class="setting-item">
                                <div class="setting-info">
                                    <div class="setting-label">
                                        <i class="fas fa-shield-alt"></i>
                                        Enable 2FA
                                    </div>
                                    <div class="setting-description">
                                        Require a verification code in addition to your password when signing in
                                    </div>
                                </div>
                                <div class="setting-control">
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="two_factor_auth" <?php echo $user_settings['two_factor_auth'] ? 'checked' : ''; ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="alert alert-warning" style="margin-top: 1rem;">
                                <i class="fas fa-exclamation-triangle"></i>
                                <div>
                                    <strong>Coming Soon</strong>
                                    <p>Two-factor authentication feature is currently under development.</p>
                                </div>
                            </div>
                            <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <div>
                                    <strong>Google Account - 2FA Not Available</strong>
                                    <p>Two-factor authentication is only available for email-registered accounts. Google accounts use Google's own security features.</p>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Active Sessions -->
                        <div class="settings-section">
                            <div class="section-header">
                                <h3 class="section-title">
                                    <i class="fas fa-desktop"></i>
                                    Active Sessions
                                </h3>
                                <p class="section-description">Manage your active login sessions</p>
                            </div>

                            <div class="session-list">
                                <div class="session-item current">
                                    <div class="session-icon">
                                        <i class="fas fa-laptop"></i>
                                    </div>
                                    <div class="session-info">
                                        <div class="session-device">Current Session</div>
                                        <div class="session-details">
                                            <span><i class="fas fa-globe"></i> <?php echo $_SERVER['REMOTE_ADDR']; ?></span>
                                            <span><i class="fas fa-clock"></i> Active now</span>
                                        </div>
                                    </div>
                                    <div class="session-badge">Current</div>
                                </div>
                            </div>

                            <button type="button" class="btn-login" id="logoutAllBtn">
                                <span class="btn-text">
                                    <i class="fas fa-sign-out-alt"></i>
                                    Logout All Other Sessions
                                </span>
                            </button>
                        </div>
                    </div>

                    <!-- Notifications Settings Tab -->
                    <div class="settings-content" id="notifications-tab">
                        <div class="settings-section">
                            <div class="section-header">
                                <h3 class="section-title">
                                    <i class="fas fa-bell"></i>
                                    Admin Notification Preferences
                                </h3>
                                <p class="section-description">Configure alerts and notifications for crime incidents and system updates</p>
                            </div>

                            <form id="notificationsForm" class="settings-form">
                                <div class="settings-list">
                                    <div class="setting-item">
                                        <div class="setting-info">
                                            <div class="setting-label">
                                                <i class="fas fa-envelope"></i>
                                                Email Notifications
                                            </div>
                                            <div class="setting-description">
                                                Receive admin alerts and updates via email
                                            </div>
                                        </div>
                                        <div class="setting-control">
                                            <label class="toggle-switch">
                                                <input type="checkbox" name="email_notifications" <?php echo $user_settings['email_notifications'] ? 'checked' : ''; ?>>
                                                <span class="toggle-slider"></span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="setting-item">
                                        <div class="setting-info">
                                            <div class="setting-label">
                                                <i class="fas fa-mobile-alt"></i>
                                                Push Notifications
                                            </div>
                                            <div class="setting-description">
                                                Receive real-time push notifications for critical incidents
                                            </div>
                                        </div>
                                        <div class="setting-control">
                                            <label class="toggle-switch">
                                                <input type="checkbox" name="push_notifications" <?php echo $user_settings['push_notifications'] ? 'checked' : ''; ?>>
                                                <span class="toggle-slider"></span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="setting-item">
                                        <div class="setting-info">
                                            <div class="setting-label">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                Crime Incident Alerts
                                            </div>
                                            <div class="setting-description">
                                                Instant notifications for new crime reports and high-priority incidents
                                            </div>
                                        </div>
                                        <div class="setting-control">
                                            <label class="toggle-switch">
                                                <input type="checkbox" name="crime_alerts" <?php echo $user_settings['crime_alerts'] ? 'checked' : ''; ?>>
                                                <span class="toggle-slider"></span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="setting-item">
                                        <div class="setting-info">
                                            <div class="setting-label">
                                                <i class="fas fa-chart-line"></i>
                                                Weekly Analytics Reports
                                            </div>
                                            <div class="setting-description">
                                                Automated weekly crime statistics, trends, and analytics summaries
                                            </div>
                                        </div>
                                        <div class="setting-control">
                                            <label class="toggle-switch">
                                                <input type="checkbox" name="weekly_reports" <?php echo $user_settings['weekly_reports'] ? 'checked' : ''; ?>>
                                                <span class="toggle-slider"></span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="setting-item">
                                        <div class="setting-info">
                                            <div class="setting-label">
                                                <i class="fas fa-sync"></i>
                                                System Maintenance Updates
                                            </div>
                                            <div class="setting-description">
                                                Notifications about system updates, maintenance schedules, and downtimes
                                            </div>
                                        </div>
                                        <div class="setting-control">
                                            <label class="toggle-switch">
                                                <input type="checkbox" name="system_updates" <?php echo $user_settings['system_updates'] ? 'checked' : ''; ?>>
                                                <span class="toggle-slider"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-actions" style="margin-top: 2rem;">
                                    <button type="submit" class="btn-login">
                                        <span class="btn-text">
                                            <i class="fas fa-save"></i>
                                            Save Notification Settings
                                        </span>
                                        <span class="btn-loader" style="display: none;">
                                            <span class="spinner"></span>
                                            Saving...
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Preferences Settings Tab -->
                    <div class="settings-content" id="preferences-tab">
                        <div class="settings-section">
                            <div class="section-header">
                                <h3 class="section-title">
                                    <i class="fas fa-paint-brush"></i>
                                    Dashboard Appearance
                                </h3>
                                <p class="section-description">Customize how the admin dashboard looks</p>
                            </div>

                            <form id="preferencesForm" class="settings-form">
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="fas fa-palette"></i>
                                            Theme
                                        </label>
                                        <select name="theme" id="themeSelect" class="form-input">
                                            <option value="light" <?php echo $user_settings['theme'] === 'light' ? 'selected' : ''; ?>>Light Mode</option>
                                            <option value="dark" <?php echo $user_settings['theme'] === 'dark' ? 'selected' : ''; ?>>Dark Mode</option>
                                            <option value="auto" <?php echo $user_settings['theme'] === 'auto' ? 'selected' : ''; ?>>Auto (System Preference)</option>
                                        </select>
                                        <small class="form-hint">Theme changes apply immediately (preview only)</small>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="fas fa-language"></i>
                                            System Language
                                        </label>
                                        <select name="language" class="form-input">
                                            <option value="en" <?php echo $user_settings['language'] === 'en' ? 'selected' : ''; ?>>English (US)</option>
                                            <option value="es" <?php echo $user_settings['language'] === 'es' ? 'selected' : ''; ?>>Español (Spanish)</option>
                                            <option value="fr" <?php echo $user_settings['language'] === 'fr' ? 'selected' : ''; ?>>Français (French)</option>
                                            <option value="tl" <?php echo $user_settings['language'] === 'tl' ? 'selected' : ''; ?>>Tagalog (Filipino)</option>
                                        </select>
                                        <small class="form-hint">Language for admin dashboard interface</small>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="fas fa-clock"></i>
                                            Report Timezone
                                        </label>
                                        <select name="timezone" class="form-input">
                                            <option value="UTC" <?php echo $user_settings['timezone'] === 'UTC' ? 'selected' : ''; ?>>UTC (Coordinated Universal Time)</option>
                                            <option value="America/New_York" <?php echo $user_settings['timezone'] === 'America/New_York' ? 'selected' : ''; ?>>Eastern Time (US & Canada)</option>
                                            <option value="America/Chicago" <?php echo $user_settings['timezone'] === 'America/Chicago' ? 'selected' : ''; ?>>Central Time (US & Canada)</option>
                                            <option value="America/Denver" <?php echo $user_settings['timezone'] === 'America/Denver' ? 'selected' : ''; ?>>Mountain Time (US & Canada)</option>
                                            <option value="America/Los_Angeles" <?php echo $user_settings['timezone'] === 'America/Los_Angeles' ? 'selected' : ''; ?>>Pacific Time (US & Canada)</option>
                                            <option value="Asia/Manila" <?php echo $user_settings['timezone'] === 'Asia/Manila' ? 'selected' : ''; ?>>Philippine Time (Manila)</option>
                                        </select>
                                        <small class="form-hint">Timezone for crime reports and analytics timestamps</small>
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="btn-login">
                                        <span class="btn-text">
                                            <i class="fas fa-save"></i>
                                            Save Preferences
                                        </span>
                                        <span class="btn-loader" style="display: none;">
                                            <span class="spinner"></span>
                                            Saving...
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Danger Zone -->
                        <div class="settings-section danger-zone">
                            <div class="section-header">
                                <h3 class="section-title">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Danger Zone
                                </h3>
                                <p class="section-description">Irreversible and destructive actions</p>
                            </div>

                            <div class="danger-actions">
                                <div class="danger-item">
                                    <div class="danger-info">
                                        <div class="danger-label">Delete Account</div>
                                        <div class="danger-description">
                                            Permanently delete your account and all associated data. This action cannot be undone.
                                        </div>
                                    </div>
                                    <button type="button" class="btn-login" id="deleteAccountBtn" style="background-color: #ef4444; border-color: #ef4444;">
                                        <span class="btn-text">
                                            <i class="fas fa-trash-alt"></i>
                                            Delete Account
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include('../includes/admin-footer.php') ?>
    </div>

    <script>

        // Tab switching functionality
        document.querySelectorAll('.settings-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                const targetTab = this.dataset.tab;

                // Update active tab
                document.querySelectorAll('.settings-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');

                // Update active content
                document.querySelectorAll('.settings-content').forEach(content => {
                    content.classList.remove('active');
                });
                document.getElementById(`${targetTab}-tab`).classList.add('active');
            });
        });

        // Initialize theme dropdown from database on page load
        (function initializeTheme() {
            const themeSelect = document.getElementById('themeSelect');
            const dbTheme = '<?php echo htmlspecialchars($user_settings['theme'] ?? 'light'); ?>';

            if (themeSelect) {
                // Database already has 'auto', 'light', or 'dark'
                themeSelect.value = dbTheme;
            }
        })();

        // Password visibility toggle
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.dataset.target;
                const input = document.getElementById(targetId);
                const icon = this.querySelector('i');

                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });

        // Account form submission
        document.getElementById('accountForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const btn = this.querySelector('button[type="submit"]');
            const btnText = btn.querySelector('.btn-text');
            const btnLoader = btn.querySelector('.btn-loader');

            // Show loader
            btnText.style.display = 'none';
            btnLoader.style.display = 'flex';
            btn.disabled = true;

            const formData = new FormData(this);

            fetch('../../api/action/update-account-settings.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        confirmButtonColor: '#4c8a89'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message,
                        confirmButtonColor: '#4c8a89'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred. Please try again.',
                    confirmButtonColor: '#4c8a89'
                });
            })
            .finally(() => {
                // Hide loader
                btnText.style.display = 'flex';
                btnLoader.style.display = 'none';
                btn.disabled = false;
            });
        });

        // Password form submission
        <?php if ($user_data['registration_type'] === 'email'): ?>
        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (newPassword !== confirmPassword) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Passwords do not match',
                    confirmButtonColor: '#4c8a89'
                });
                return;
            }

            if (newPassword.length < 8) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Password must be at least 8 characters long',
                    confirmButtonColor: '#4c8a89'
                });
                return;
            }

            Swal.fire({
                icon: 'info',
                title: 'Feature Not Available',
                text: 'Password change feature is currently disabled. Please use the forgot password feature on the login page.',
                confirmButtonColor: '#4c8a89'
            });
        });
        <?php endif; ?>

        // Notifications form submission
        document.getElementById('notificationsForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const btn = this.querySelector('button[type="submit"]');
            const btnText = btn.querySelector('.btn-text');
            const btnLoader = btn.querySelector('.btn-loader');

            // Show loader
            btnText.style.display = 'none';
            btnLoader.style.display = 'flex';
            btn.disabled = true;

            const formData = new FormData(this);
            formData.append('type', 'notifications');

            fetch('../../api/action/update-user-settings.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        confirmButtonColor: '#4c8a89'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message,
                        confirmButtonColor: '#4c8a89'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred. Please try again.',
                    confirmButtonColor: '#4c8a89'
                });
            })
            .finally(() => {
                // Hide loader
                btnText.style.display = 'flex';
                btnLoader.style.display = 'none';
                btn.disabled = false;
            });
        });

        // Real-time theme switching
        document.getElementById('themeSelect').addEventListener('change', function(e) {
            const theme = this.value;

            // Map 'auto' to 'system' for footer compatibility
            const footerTheme = theme === 'auto' ? 'system' : theme;

            // Apply theme
            if (theme === 'dark') {
                document.documentElement.setAttribute('data-theme', 'dark');
            } else if (theme === 'light') {
                document.documentElement.setAttribute('data-theme', 'light');
            } else if (theme === 'auto') {
                // Auto detect system preference
                const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                document.documentElement.setAttribute('data-theme', prefersDark ? 'dark' : 'light');
            }

            // Sync with footer toggle
            localStorage.setItem('theme', footerTheme);

            // Update footer buttons
            const footerButtons = document.querySelectorAll('.theme-toggle .theme-toggle-btn');
            footerButtons.forEach(btn => {
                if (btn.getAttribute('data-theme') === footerTheme) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });

            // Show preview notification
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });

            Toast.fire({
                icon: 'info',
                title: 'Theme changed (preview only)'
            });
        });

        // Listen for theme changes from footer
        document.addEventListener('themeChanged', function(e) {
            const footerTheme = e.detail;
            const themeSelect = document.getElementById('themeSelect');

            if (themeSelect) {
                // Map 'system' to 'auto' for settings dropdown
                const settingsTheme = footerTheme === 'system' ? 'auto' : footerTheme;
                themeSelect.value = settingsTheme;
            }
        });

        // Preferences form submission
        document.getElementById('preferencesForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const btn = this.querySelector('button[type="submit"]');
            const btnText = btn.querySelector('.btn-text');
            const btnLoader = btn.querySelector('.btn-loader');

            // Show loader
            btnText.style.display = 'none';
            btnLoader.style.display = 'flex';
            btn.disabled = true;

            const formData = new FormData(this);
            formData.append('type', 'preferences');

            fetch('../../api/action/update-user-settings.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        confirmButtonColor: '#4c8a89'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message,
                        confirmButtonColor: '#4c8a89'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred. Please try again.',
                    confirmButtonColor: '#4c8a89'
                });
            })
            .finally(() => {
                // Hide loader
                btnText.style.display = 'flex';
                btnLoader.style.display = 'none';
                btn.disabled = false;
            });
        });

        // 2FA Toggle
        <?php if ($user_data['registration_type'] === 'email'): ?>
        document.getElementById('two_factor_auth').addEventListener('change', function(e) {
            const isEnabled = this.checked;

            const formData = new FormData();
            formData.append('type', '2fa');
            if (isEnabled) {
                formData.append('two_factor_auth', '1');
            }

            fetch('../../api/action/update-user-settings.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        confirmButtonColor: '#4c8a89'
                    });
                } else {
                    // Revert toggle if failed
                    this.checked = !isEnabled;
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message,
                        confirmButtonColor: '#4c8a89'
                    });
                }
            })
            .catch(error => {
                // Revert toggle if error
                this.checked = !isEnabled;
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred. Please try again.',
                    confirmButtonColor: '#4c8a89'
                });
            });
        });
        <?php endif; ?>

        // Profile picture upload
        document.getElementById('uploadPictureBtn').addEventListener('click', function() {
            document.getElementById('profilePictureInput').click();
        });

        document.getElementById('profilePictureInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            // Validate file type
            if (!file.type.startsWith('image/')) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Please select an image file',
                    confirmButtonColor: '#4c8a89'
                });
                this.value = '';
                return;
            }

            // Validate file size (max 5MB)
            if (file.size > 5 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'File size must be less than 5MB',
                    confirmButtonColor: '#4c8a89'
                });
                this.value = '';
                return;
            }

            // Show loading
            Swal.fire({
                title: 'Uploading...',
                text: 'Please wait while we upload your profile picture',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Upload file
            const formData = new FormData();
            formData.append('profile_picture', file);

            fetch('../../api/action/upload-profile-picture.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update preview image
                    document.getElementById('profilePicturePreview').src = data.url + '?' + new Date().getTime();

                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        confirmButtonColor: '#4c8a89'
                    }).then(() => {
                        // Reload page to update all profile pictures
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message,
                        confirmButtonColor: '#4c8a89'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred while uploading. Please try again.',
                    confirmButtonColor: '#4c8a89'
                });
            })
            .finally(() => {
                // Clear the file input
                e.target.value = '';
            });
        });

        // Logout all sessions
        document.getElementById('logoutAllBtn').addEventListener('click', function() {
            Swal.fire({
                title: 'Logout All Sessions?',
                text: 'This will log you out from all devices except this one.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4c8a89',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, logout all'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'All other sessions have been logged out',
                        confirmButtonColor: '#4c8a89'
                    });
                }
            });
        });

        // Delete account
        document.getElementById('deleteAccountBtn').addEventListener('click', function() {
            Swal.fire({
                title: 'Delete Account?',
                html: '<p>This will permanently delete your account and all associated data.</p><p style="color: #ef4444; font-weight: 600;">This action cannot be undone!</p>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete my account',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Feature Disabled',
                        text: 'Account deletion is currently disabled. Please contact an administrator.',
                        confirmButtonColor: '#4c8a89'
                    });
                }
            });
        });
    </script>
</body>
</html>
