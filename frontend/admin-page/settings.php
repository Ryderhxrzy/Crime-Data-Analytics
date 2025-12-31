<?php
// Authentication check - must be at the top of every admin page
require_once '../../api/middleware/auth.php';
require_once '../../api/config.php';

// Get user ID from session
$user_id = $_SESSION['user']['id'] ?? null;

if (!$user_id) {
    header('Location: ../../index.php');
    exit;
}

// Fetch user information
$stmt = $mysqli->prepare("SELECT id, email, full_name, profile_picture, role, registration_type FROM crime_department_admin_users WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$stmt->close();

if (!$user_data) {
    header('Location: ../../index.php');
    exit;
}

// Static default settings (no database table needed)
$user_settings = [
    'email_notifications' => 1,
    'push_notifications' => 1,
    'crime_alerts' => 1,
    'weekly_reports' => 1,
    'system_updates' => 1,
    'two_factor_auth' => 0,
    'theme' => 'light',
    'language' => 'en',
    'timezone' => 'UTC'
];

// Check if production environment
$is_production = ($_ENV['APP_ENV'] ?? 'local') === 'production';
$php_ext = $is_production ? '' : '.php';
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
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i>
                                        Save Changes
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
                                    <img src="<?php echo htmlspecialchars($user_data['profile_picture'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($user_data['full_name']) . '&background=4c8a89&color=fff&size=256'); ?>" alt="Profile Picture" id="profilePicturePreview">
                                </div>
                                <div class="picture-actions">
                                    <button type="button" class="btn btn-secondary" id="uploadPictureBtn">
                                        <i class="fas fa-upload"></i>
                                        Upload New Picture
                                    </button>
                                    <input type="file" id="profilePictureInput" accept="image/*" style="display: none;">
                                    <small class="form-hint">Recommended: Square image, at least 256x256px</small>
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
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i>
                                        Update Password
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

                            <button type="button" class="btn btn-secondary" id="logoutAllBtn">
                                <i class="fas fa-sign-out-alt"></i>
                                Logout All Other Sessions
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

                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i>
                                        Save Notification Settings
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
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i>
                                        Save Preferences
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
                                    <button type="button" class="btn btn-danger" id="deleteAccountBtn">
                                        <i class="fas fa-trash-alt"></i>
                                        Delete Account
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
        // Environment configuration
        const PHP_EXT = '<?php echo $php_ext; ?>';

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

            Swal.fire({
                icon: 'info',
                title: 'Feature Not Available',
                text: 'Account settings update is currently disabled. Please use the Profile page to update your information.',
                confirmButtonColor: '#4c8a89'
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

            Swal.fire({
                icon: 'info',
                title: 'Feature Not Available',
                text: 'Notification preferences are currently view-only. This feature will be available in a future update.',
                confirmButtonColor: '#4c8a89'
            });
        });

        // Real-time theme switching
        document.getElementById('themeSelect').addEventListener('change', function(e) {
            const theme = this.value;

            if (theme === 'dark') {
                document.documentElement.setAttribute('data-theme', 'dark');
            } else if (theme === 'light') {
                document.documentElement.removeAttribute('data-theme');
            } else if (theme === 'auto') {
                // Auto detect system preference
                if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    document.documentElement.setAttribute('data-theme', 'dark');
                } else {
                    document.documentElement.removeAttribute('data-theme');
                }
            }

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

        // Preferences form submission
        document.getElementById('preferencesForm').addEventListener('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                icon: 'info',
                title: 'Feature Not Available',
                text: 'Preference settings are currently view-only. This feature will be available in a future update.',
                confirmButtonColor: '#4c8a89'
            });
        });

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
                return;
            }

            Swal.fire({
                icon: 'info',
                title: 'Feature Not Available',
                text: 'Profile picture upload is currently disabled. Please use the Profile page to update your picture.',
                confirmButtonColor: '#4c8a89'
            });

            // Clear the file input
            this.value = '';
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
