<?php
/**
 * Profile Page View
 * Displays user profile information
 */

// Load profile data controller
require_once '../../api/retrieve/profile-data.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | Crime Dep.</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/admin-header.css">
    <link rel="stylesheet" href="../css/hero.css">
    <link rel="stylesheet" href="../css/sidebar-footer.css">
    <link rel="stylesheet" href="../css/profile.css">
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
                            <span>Profile</span>
                        </li>
                    </ol>
                </nav>
                <h1>My Profile</h1>
                <p>Manage your account information</p>
            </div>

            <div class="sub-container">
                <div class="page-content">
                    <div class="profile-card">
                    <div class="profile-header">
                        <div class="profile-avatar-section">
                            <div class="profile-avatar-wrapper">
                                <img src="<?php echo htmlspecialchars('../image/profile/' .$profile_picture); ?>" alt="<?php echo htmlspecialchars($user_data['full_name']); ?>" class="profile-avatar">
                                <div class="avatar-badge <?php echo $user_data['status'] === 'active' ? 'status-active' : 'status-inactive'; ?>">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                            <div class="profile-header-info">
                                <h2 class="profile-name"><?php echo htmlspecialchars($user_data['full_name']); ?></h2>
                                <p class="profile-email"><?php echo htmlspecialchars($user_data['email']); ?></p>
                                <div class="profile-badges">
                                    <span class="badge badge-role">
                                        <i class="fas fa-user-shield"></i>
                                        <?php echo ucfirst(str_replace('_', ' ', $user_data['role'])); ?>
                                    </span>
                                    <span class="badge badge-type">
                                        <i class="<?php echo $user_data['registration_type'] === 'google' ? 'fab fa-google' : 'fas fa-envelope'; ?>"></i>
                                        <?php echo ucfirst($user_data['registration_type']); ?>
                                    </span>
                                    <span class="badge badge-<?php echo strtolower($user_data['account_status']); ?>">
                                        <i class="fas fa-<?php echo $user_data['account_status'] === 'verified' ? 'check-circle' : 'clock'; ?>"></i>
                                        <?php echo ucfirst($user_data['account_status']); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Account Information Section -->
                    <div class="profile-section">
                        <div class="section-header">
                            <h3 class="section-title">
                                <i class="fas fa-user-circle"></i>
                                Account Information
                            </h3>
                        </div>
                        <div class="info-grid">
                            <div class="info-item">
                                <label class="info-label">
                                    <i class="fas fa-id-card"></i>
                                    User ID
                                </label>
                                <p class="info-value"><?php echo htmlspecialchars($user_data['id']); ?></p>
                            </div>
                            <div class="info-item">
                                <label class="info-label">
                                    <i class="fas fa-envelope"></i>
                                    Email Address
                                </label>
                                <p class="info-value"><?php echo htmlspecialchars($user_data['email']); ?></p>
                            </div>
                            <div class="info-item">
                                <label class="info-label">
                                    <i class="fas fa-user"></i>
                                    Full Name
                                </label>
                                <p class="info-value"><?php echo htmlspecialchars($user_data['full_name']); ?></p>
                            </div>
                            <div class="info-item">
                                <label class="info-label">
                                    <i class="fas fa-shield-alt"></i>
                                    Role
                                </label>
                                <p class="info-value"><?php echo ucfirst(str_replace('_', ' ', $user_data['role'])); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Account Status Section -->
                    <div class="profile-section">
                        <div class="section-header">
                            <h3 class="section-title">
                                <i class="fas fa-chart-line"></i>
                                Account Status
                            </h3>
                        </div>
                        <div class="info-grid">
                            <div class="info-item">
                                <label class="info-label">
                                    <i class="fas fa-toggle-on"></i>
                                    Status
                                </label>
                                <p class="info-value">
                                    <span class="status-badge status-<?php echo strtolower($user_data['status']); ?>">
                                        <?php echo ucfirst($user_data['status']); ?>
                                    </span>
                                </p>
                            </div>
                            <div class="info-item">
                                <label class="info-label">
                                    <i class="fas fa-check-double"></i>
                                    Verification Status
                                </label>
                                <p class="info-value">
                                    <span class="status-badge status-<?php echo strtolower($user_data['account_status']); ?>">
                                        <?php echo ucfirst($user_data['account_status']); ?>
                                    </span>
                                </p>
                            </div>
                            <div class="info-item">
                                <label class="info-label">
                                    <i class="fas fa-key"></i>
                                    Login Method
                                </label>
                                <p class="info-value">
                                    <span class="method-badge method-<?php echo $user_data['registration_type']; ?>">
                                        <i class="<?php echo $user_data['registration_type'] === 'google' ? 'fab fa-google' : 'fas fa-envelope'; ?>"></i>
                                        <?php echo ucfirst($user_data['registration_type']); ?>
                                    </span>
                                </p>
                            </div>
                            <div class="info-item">
                                <label class="info-label">
                                    <i class="fas fa-lock"></i>
                                    Password
                                </label>
                                <p class="info-value">
                                    <?php if ($user_data['password']): ?>
                                        <span class="text-muted"><i class="fas fa-check"></i> Set</span>
                                    <?php else: ?>
                                        <span class="text-muted"><i class="fas fa-times"></i> Not set (Google login)</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Activity Information Section -->
                    <div class="profile-section">
                        <div class="section-header">
                            <h3 class="section-title">
                                <i class="fas fa-history"></i>
                                Activity Information
                            </h3>
                        </div>
                        <div class="info-grid">
                            <div class="info-item">
                                <label class="info-label">
                                    <i class="fas fa-sign-in-alt"></i>
                                    Last Login
                                </label>
                                <p class="info-value">
                                    <?php
                                    if ($user_data['last_login']) {
                                        echo date('F j, Y g:i A', strtotime($user_data['last_login']));
                                    } else {
                                        echo '<span class="text-muted">Never</span>';
                                    }
                                    ?>
                                </p>
                            </div>
                            <div class="info-item">
                                <label class="info-label">
                                    <i class="fas fa-calendar-plus"></i>
                                    Account Created
                                </label>
                                <p class="info-value"><?php echo date('F j, Y g:i A', strtotime($user_data['created_at'])); ?></p>
                            </div>
                            <div class="info-item">
                                <label class="info-label">
                                    <i class="fas fa-edit"></i>
                                    Last Updated
                                </label>
                                <p class="info-value"><?php echo date('F j, Y g:i A', strtotime($user_data['updated_at'])); ?></p>
                            </div>
                            <div class="info-item">
                                <label class="info-label">
                                    <i class="fas fa-clock"></i>
                                    Member Since
                                </label>
                                <p class="info-value">
                                    <?php
                                    $created = new DateTime($user_data['created_at']);
                                    $now = new DateTime();
                                    $diff = $created->diff($now);
                                    if ($diff->y > 0) {
                                        echo $diff->y . ' year' . ($diff->y > 1 ? 's' : '');
                                    } elseif ($diff->m > 0) {
                                        echo $diff->m . ' month' . ($diff->m > 1 ? 's' : '');
                                    } elseif ($diff->d > 0) {
                                        echo $diff->d . ' day' . ($diff->d > 1 ? 's' : '');
                                    } else {
                                        echo 'Today';
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information Section (Read-only) -->
                    <div class="profile-section">
                        <div class="section-header">
                            <h3 class="section-title">
                                <i class="fas fa-info-circle"></i>
                                Additional Information
                                <?php if ($has_incomplete_profile): ?>
                                    <span class="badge badge-warning">Incomplete</span>
                                <?php endif; ?>
                            </h3>
                        </div>
                        <div class="info-grid">
                            <div class="info-item">
                                <label class="info-label">
                                    <i class="fas fa-phone"></i>
                                    Phone Number
                                </label>
                                <p class="info-value"><?php echo htmlspecialchars($additional_info['phone_number'] ?? 'Not provided'); ?></p>
                            </div>
                            <div class="info-item">
                                <label class="info-label">
                                    <i class="fas fa-building"></i>
                                    Department
                                </label>
                                <p class="info-value"><?php echo htmlspecialchars($additional_info['department'] ?? 'Not provided'); ?></p>
                            </div>
                            <div class="info-item">
                                <label class="info-label">
                                    <i class="fas fa-briefcase"></i>
                                    Position
                                </label>
                                <p class="info-value"><?php echo htmlspecialchars($additional_info['position'] ?? 'Not provided'); ?></p>
                            </div>
                            <div class="info-item">
                                <label class="info-label">
                                    <i class="fas fa-map-marker-alt"></i>
                                    Address
                                </label>
                                <p class="info-value"><?php echo htmlspecialchars($additional_info['address'] ?? 'Not provided'); ?></p>
                            </div>
                            <div class="info-item" style="grid-column: 1 / -1;">
                                <label class="info-label">
                                    <i class="fas fa-align-left"></i>
                                    Bio
                                </label>
                                <p class="info-value"><?php echo htmlspecialchars($additional_info['bio'] ?? 'Not provided'); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Actions -->
                    <div class="profile-actions">
                        <a href="settings.php" class="btn btn-primary">
                            <i class="fas fa-edit"></i>
                            Edit Profile
                        </a>
                    </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include('../includes/admin-footer.php') ?>
    </div>

    <script src="../js/profile.js"></script>
    <script>
        // Initialize profile page
        initProfile();
    </script>
</body>
</html>
