<?php
/**
 * Reusable Sidebar Component
 * Include this file in your pages where you want a sidebar: <?php include 'frontend/includes/sidebar.php'; ?>
 *
 * Features:
 * - Responsive design with mobile toggle
 * - Admin-style navigation
 * - Collapsible sections
 * - Dark mode support
 * - Icon-based navigation
 * - Active state management
 */

// Check if user has incomplete profile
$sidebar_user_id = $_SESSION['user']['id'] ?? null;
$sidebar_has_incomplete_profile = false;

if ($sidebar_user_id) {
    require_once __DIR__ . '/../../api/config.php';
    $sidebar_stmt = $mysqli->prepare("SELECT phone_number, address, department, position, bio FROM crime_department_admin_information WHERE admin_user_id = ? LIMIT 1");
    $sidebar_stmt->bind_param("i", $sidebar_user_id);
    $sidebar_stmt->execute();
    $sidebar_result = $sidebar_stmt->get_result();
    $sidebar_additional_info = $sidebar_result->fetch_assoc();
    $sidebar_stmt->close();

    if (!$sidebar_additional_info ||
        empty($sidebar_additional_info['phone_number']) ||
        empty($sidebar_additional_info['address']) ||
        empty($sidebar_additional_info['department']) ||
        empty($sidebar_additional_info['position']) ||
        empty($sidebar_additional_info['bio'])) {
        $sidebar_has_incomplete_profile = true;
    }
}
?>

<!-- Sidebar Component -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <div class="brand-logo">
                <img src="../image/logo.svg" alt="Crime Data Analytics Logo" class="logo-img">
            </div>
        </div>
    </div>

    <div class="sidebar-content">
        <!-- Navigation Menu -->
        <nav class="sidebar-nav">
            <!-- Dashboard Section -->
            <div class="sidebar-section">
                <h3 class="sidebar-section-title">Admin Dashboard</h3>
                <ul class="sidebar-menu">
                    <li class="sidebar-menu-item">
                        <a href="system-overview.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'system-overview.php' ? 'active' : ''; ?>">
                            <i class="fas fa-home"></i>
                            <span>System Overview</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="analytics-summary.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'analytics-summary.php' ? 'active' : ''; ?>">
                            <i class="fas fa-chart-line"></i>
                            <span>Analytics Summary</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Crime Data Section -->
            <div class="sidebar-section">
                <h3 class="sidebar-section-title">Crime Mapping & Heatmaps</h3>
                <ul class="sidebar-menu">
                    <!-- Crime Mapping & Visualization -->
                    <li class="sidebar-menu-item">
                        <a href="#" class="sidebar-link sidebar-submenu-toggle">
                            <i class="fas fa-map-marked-alt"></i>
                            <span>Mapping</span>
                            <i class="fas fa-chevron-down submenu-icon"></i>
                        </a>
                        <ul class="sidebar-submenu">
                            <li>
                                <a href="crime-mapping.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'crime-mapping.php' ? 'active' : ''; ?>">
                                    <i class="fas fa-map"></i>
                                    <span>Crime Mapping</span>
                                </a>
                            </li>
                            <li>
                                <a href="heatmaps.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'heatmaps.php' ? 'active' : ''; ?>">
                                    <i class="fas fa-fire"></i>
                                    <span>Heatmaps</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>

            <div class="sidebar-section">
                <h3 class="sidebar-section-title">Trend Analysis</h3>
                <!-- Trend Analysis -->
                <ul class="sidebar-menu">
                    <li class="sidebar-menu-item">
                        <a href="#" class="sidebar-link sidebar-submenu-toggle">
                            <i class="fas fa-chart-area"></i>
                            <span>Trend Analytics</span>
                            <i class="fas fa-chevron-down submenu-icon"></i>
                        </a>
                        <ul class="sidebar-submenu">
                            <li>
                                <a href="timebased-trend.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'timebased-trend.php' ? 'active' : ''; ?>">
                                    <i class="fas fa-clock"></i>
                                    <span>Time-Based Trends</span>
                                </a>
                            </li>
                            <li>
                                <a href="location-trend.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'location-trend.php' ? 'active' : ''; ?>">
                                    <i class="fas fa-map-pin"></i>
                                    <span>Location Trends</span>
                                </a>
                            </li>
                            <li>
                                <a href="crime-type-trend.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'crime-type-trend.php' ? 'active' : ''; ?>">
                                    <i class="fas fa-chart-bar"></i>
                                    <span>Crime Type Trends</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>

            <div class="sidebar-section">
                <h3 class="sidebar-section-title">Predictive Policing Tools</h3>
                <ul class="sidebar-menu">
                    <!-- Predictive Policing -->
                    <li class="sidebar-menu-item">
                        <a href="#" class="sidebar-link sidebar-submenu-toggle">
                            <i class="fas fa-brain"></i>
                            <span>Predictive Analytics</span>
                            <i class="fas fa-chevron-down submenu-icon"></i>
                        </a>
                        <ul class="sidebar-submenu">
                            <li>
                                <a href="hotspot-prediction.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'hotspot-prediction.php' ? 'active' : ''; ?>">
                                    <i class="fas fa-crosshairs"></i>
                                    <span>Crime Hotspots</span>
                                </a>
                            </li>
                            <li>
                                <a href="risk-forecasting.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'risk-forecasting.php' ? 'active' : ''; ?>">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <span>Risk Forecasting</span>
                                </a>
                            </li>
                            <li>
                                <a href="pattern-detection.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'pattern-detection.php' ? 'active' : ''; ?>">
                                    <i class="fas fa-project-diagram"></i>
                                    <span>Pattern Detection</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>

            <div class="sidebar-section">
                <h3 class="sidebar-section-title">Key Metrics Dashboard</h3>
                <ul class="sidebar-menu">
                    <li class="sidebar-menu-item">
                        <a href="#" class="sidebar-link sidebar-submenu-toggle">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Key Metrics</span>
                            <i class="fas fa-chevron-down submenu-icon"></i>
                        </a>
                        <ul class="sidebar-submenu">
                            <li>
                                <a href="crime-rate-metrics.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'crime-rate-metrics.php' ? 'active' : ''; ?>">
                                    <i class="fas fa-percentage"></i>
                                    <span>Crime Rates</span>
                                </a>
                            </li>
                            <li>
                                <a href="clearance-metrics.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'clearance-metrics.php' ? 'active' : ''; ?>">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Clearance Rates</span>
                                </a>
                            </li>
                            <li>
                                <a href="response-metrics.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'response-metrics.php' ? 'active' : ''; ?>">
                                    <i class="fas fa-stopwatch"></i>
                                    <span>Response Times</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>

            <!-- Reports Section -->
            <div class="sidebar-section">
                <h3 class="sidebar-section-title">Reports & Alerts</h3>
                <ul class="sidebar-menu">
                    <!-- Reports -->
                    <li class="sidebar-menu-item">
                        <a href="#" class="sidebar-link sidebar-submenu-toggle">
                            <i class="fas fa-file-alt"></i>
                            <span>Reports</span>
                            <i class="fas fa-chevron-down submenu-icon"></i>
                        </a>
                        <ul class="sidebar-submenu">
                            <li>
                                <a href="report-builder.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'report-builder.php' ? 'active' : ''; ?>">
                                    <i class="fas fa-tools"></i>
                                    <span>Report Builder</span>
                                </a>
                            </li>
                            <li>
                                <a href="report-history.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'report-history.php' ? 'active' : ''; ?>">
                                    <i class="fas fa-history"></i>
                                    <span>Report History</span>
                                </a>
                            </li>
                            <li>
                                <a href="scheduled-reports.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'scheduled-reports.php' ? 'active' : ''; ?>">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>Scheduled Reports</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Automated Alerts -->
                    <li class="sidebar-menu-item">
                        <a href="#" class="sidebar-link sidebar-submenu-toggle">
                            <i class="fas fa-bell"></i>
                            <span>Alerts</span>
                            <i class="fas fa-chevron-down submenu-icon"></i>
                        </a>
                        <ul class="sidebar-submenu">
                            <li>
                                <a href="crime-alerts.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'crime-alerts.php' ? 'active' : ''; ?>">
                                    <i class="fas fa-bullhorn"></i>
                                    <span>Crime Cluster Alerts</span>
                                </a>
                            </li>
                            <li>
                                <a href="risk-notifications.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'risk-notifications.php' ? 'active' : ''; ?>">
                                    <i class="fas fa-shield-alt"></i>
                                    <span>High-Risk Notifications</span>
                                </a>
                            </li>
                            <li>
                                <a href="alert-settings.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'alert-settings.php' ? 'active' : ''; ?>">
                                    <i class="fas fa-cog"></i>
                                    <span>Alert Settings</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>

            <!-- Account Section -->
            <div class="sidebar-section">
                <h3 class="sidebar-section-title">Account</h3>
                <ul class="sidebar-menu">
                    <li class="sidebar-menu-item">
                        <a href="profile.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>">
                            <i class="fas fa-user"></i>
                            <span>Profile</span>
                            <?php if ($sidebar_has_incomplete_profile): ?>
                                <span class="notification-dot" title="Profile incomplete"></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="settings.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">
                            <i class="fas fa-cog"></i>
                            <span>Settings</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="api/action/logout.php" class="sidebar-link">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</aside>

<!-- Sidebar Overlay for mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sidebar functionality
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    // Toggle sidebar
    function toggleSidebar() {
        sidebar.classList.toggle('sidebar-open');
        sidebarOverlay.classList.toggle('sidebar-overlay-open');
        document.body.classList.toggle('sidebar-open');
    }

    // Close sidebar
    function closeSidebar() {
        sidebar.classList.remove('sidebar-open');
        sidebarOverlay.classList.remove('sidebar-overlay-open');
        document.body.classList.remove('sidebar-open');
    }

    // Expose functions globally so other scripts
    // can trigger the sidebar without duplicating logic.
    window.sidebarToggle = toggleSidebar;
    window.sidebarClose = closeSidebar;

    // Close sidebar when clicking overlay
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', closeSidebar);
    }

    // Close sidebar on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar.classList.contains('sidebar-open')) {
            closeSidebar();
        }
    });

    // Handle submenu toggles
    const submenuToggles = document.querySelectorAll('.sidebar-submenu-toggle');
    submenuToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();

            // Close other open submenus (optional - for accordion behavior)
            const parentSection = this.closest('.sidebar-section');
            const allToggles = parentSection.querySelectorAll('.sidebar-submenu-toggle');
            allToggles.forEach(otherToggle => {
                if (otherToggle !== this) {
                    const otherSubmenu = otherToggle.nextElementSibling;
                    if (otherSubmenu && otherSubmenu.classList.contains('sidebar-submenu-open')) {
                        otherSubmenu.classList.remove('sidebar-submenu-open');
                        otherToggle.classList.remove('active');
                    }
                }
            });

            const submenu = this.nextElementSibling;

            if (submenu) {
                const isOpen = submenu.classList.contains('sidebar-submenu-open');
                submenu.classList.toggle('sidebar-submenu-open');
                this.classList.toggle('active', !isOpen);
            }
        });
    });

    // Auto-open submenu if it contains active item
    const activeLinks = document.querySelectorAll('.sidebar-submenu .sidebar-link.active');
    activeLinks.forEach(activeLink => {
        const submenu = activeLink.closest('.sidebar-submenu');
        const toggle = submenu ? submenu.previousElementSibling : null;

        if (submenu && toggle && toggle.classList.contains('sidebar-submenu-toggle')) {
            submenu.classList.add('sidebar-submenu-open');
            toggle.classList.add('active');
        }
    });
});
</script>