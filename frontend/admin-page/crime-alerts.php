<?php
// Authentication check - must be at the top of every admin page
require_once '../../api/middleware/auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crime Alerts | Crime Dep.</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/admin-header.css">
    <link rel="stylesheet" href="../css/hero.css">
    <link rel="stylesheet" href="../css/sidebar-footer.css">
    <link rel="stylesheet" href="../css/crime-alerts.css">
    <link rel="icon" type="image/x-icon" href="../image/favicon.ico">
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
                                <span>Admin Dashboard</span>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="/alerts" class="breadcrumb-link">
                                <span>Reports & Alerts</span>
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <span>Crime Alerts</span>
                        </li>
                    </ol>
                </nav>
                <h1>Crime Cluster Alerts</h1>
                <p>Real-time detection of crime clusters and patterns. Monitor active alerts, view detailed cluster information, and respond to emerging crime hotspots.</p>
            </div>
            
            <div class="sub-container">
                <div class="page-content">
                    <!-- Alert Statistics -->
                    <div class="alert-stats-grid">
                        <div class="stat-card critical">
                            <div class="stat-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="stat-content">
                                <h3>3</h3>
                                <p>Active Critical Alerts</p>
                            </div>
                        </div>
                        <div class="stat-card high">
                            <div class="stat-icon">
                                <i class="fas fa-exclamation-circle"></i>
                            </div>
                            <div class="stat-content">
                                <h3>7</h3>
                                <p>High Priority Alerts</p>
                            </div>
                        </div>
                        <div class="stat-card medium">
                            <div class="stat-icon">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <div class="stat-content">
                                <h3>12</h3>
                                <p>Medium Priority</p>
                            </div>
                        </div>
                        <div class="stat-card total">
                            <div class="stat-icon">
                                <i class="fas fa-bell"></i>
                            </div>
                            <div class="stat-content">
                                <h3>22</h3>
                                <p>Total Active Alerts</p>
                            </div>
                        </div>
                    </div>

                    <!-- Active Alerts List -->
                    <div class="alerts-section">
                        <div class="section-header">
                            <h3><i class="fas fa-bell"></i> Active Alerts</h3>
                            <div class="filter-controls">
                                <select class="form-control" id="severityFilter">
                                    <option value="all">All Severities</option>
                                    <option value="critical">Critical</option>
                                    <option value="high">High</option>
                                    <option value="medium">Medium</option>
                                    <option value="low">Low</option>
                                </select>
                                <select class="form-control" id="timeFilter">
                                    <option value="all">All Time</option>
                                    <option value="today">Today</option>
                                    <option value="week">This Week</option>
                                    <option value="month">This Month</option>
                                </select>
                            </div>
                        </div>

                        <div class="alerts-list">
                            <!-- Critical Alert -->
                            <div class="alert-item critical">
                                <div class="alert-header">
                                    <div class="alert-title">
                                        <h4>Downtown Theft Cluster</h4>
                                        <span class="alert-badge critical">CRITICAL</span>
                                    </div>
                                    <div class="alert-time">
                                        <i class="fas fa-clock"></i> 15 minutes ago
                                    </div>
                                </div>
                                <div class="alert-details">
                                    <div class="alert-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>Central Business District, 5-block radius</span>
                                    </div>
                                    <div class="alert-crimes">
                                        <p><strong>5 incidents detected:</strong> 3 shoplifting, 2 pickpocketing within 2 hours</p>
                                    </div>
                                    <div class="alert-pattern">
                                        <i class="fas fa-chart-line"></i>
                                        <span>Pattern: Peak activity during lunch hours (12:00-14:00)</span>
                                    </div>
                                </div>
                                <div class="alert-actions">
                                    <button class="btn btn-primary btn-sm">
                                        <i class="fas fa-map"></i> View Map
                                    </button>
                                    <button class="btn btn-secondary btn-sm">
                                        <i class="fas fa-list"></i> View Details
                                    </button>
                                    <button class="btn btn-outline btn-sm">
                                        <i class="fas fa-check"></i> Dismiss
                                    </button>
                                </div>
                            </div>

                            <!-- High Alert -->
                            <div class="alert-item high">
                                <div class="alert-header">
                                    <div class="alert-title">
                                        <h4>Residential Burglary Pattern</h4>
                                        <span class="alert-badge high">HIGH</span>
                                    </div>
                                    <div class="alert-time">
                                        <i class="fas fa-clock"></i> 2 hours ago
                                    </div>
                                </div>
                                <div class="alert-details">
                                    <div class="alert-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>Greenwood Suburbs, 3-block area</span>
                                    </div>
                                    <div class="alert-crimes">
                                        <p><strong>4 incidents detected:</strong> Residential break-ins over 3 days</p>
                                    </div>
                                    <div class="alert-pattern">
                                        <i class="fas fa-chart-line"></i>
                                        <span>Pattern: Weekday afternoons (14:00-17:00) when residents are at work</span>
                                    </div>
                                </div>
                                <div class="alert-actions">
                                    <button class="btn btn-primary btn-sm">
                                        <i class="fas fa-map"></i> View Map
                                    </button>
                                    <button class="btn btn-secondary btn-sm">
                                        <i class="fas fa-list"></i> View Details
                                    </button>
                                    <button class="btn btn-outline btn-sm">
                                        <i class="fas fa-check"></i> Dismiss
                                    </button>
                                </div>
                            </div>

                            <!-- Medium Alert -->
                            <div class="alert-item medium">
                                <div class="alert-header">
                                    <div class="alert-title">
                                        <h4>Vehicle Theft Hotspot</h4>
                                        <span class="alert-badge medium">MEDIUM</span>
                                    </div>
                                    <div class="alert-time">
                                        <i class="fas fa-clock"></i> 4 hours ago
                                    </div>
                                </div>
                                <div class="alert-details">
                                    <div class="alert-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>Shopping Mall Parking Area</span>
                                    </div>
                                    <div class="alert-crimes">
                                        <p><strong>3 incidents detected:</strong> Vehicle thefts over 24 hours</p>
                                    </div>
                                    <div class="alert-pattern">
                                        <i class="fas fa-chart-line"></i>
                                        <span>Pattern: Evening hours (19:00-22:00) in poorly lit areas</span>
                                    </div>
                                </div>
                                <div class="alert-actions">
                                    <button class="btn btn-primary btn-sm">
                                        <i class="fas fa-map"></i> View Map
                                    </button>
                                    <button class="btn btn-secondary btn-sm">
                                        <i class="fas fa-list"></i> View Details
                                    </button>
                                    <button class="btn btn-outline btn-sm">
                                        <i class="fas fa-check"></i> Dismiss
                                    </button>
                                </div>
                            </div>

                            <!-- Low Alert -->
                            <div class="alert-item low">
                                <div class="alert-header">
                                    <div class="alert-title">
                                        <h4>Vandalism Cluster</h4>
                                        <span class="alert-badge low">LOW</span>
                                    </div>
                                    <div class="alert-time">
                                        <i class="fas fa-clock"></i> 6 hours ago
                                    </div>
                                </div>
                                <div class="alert-details">
                                    <div class="alert-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>City Park Area</span>
                                    </div>
                                    <div class="alert-crimes">
                                        <p><strong>3 incidents detected:</strong> Property vandalism over 2 days</p>
                                    </div>
                                    <div class="alert-pattern">
                                        <i class="fas fa-chart-line"></i>
                                        <span>Pattern: Late night activity (23:00-02:00)</span>
                                    </div>
                                </div>
                                <div class="alert-actions">
                                    <button class="btn btn-primary btn-sm">
                                        <i class="fas fa-map"></i> View Map
                                    </button>
                                    <button class="btn btn-secondary btn-sm">
                                        <i class="fas fa-list"></i> View Details
                                    </button>
                                    <button class="btn btn-outline btn-sm">
                                        <i class="fas fa-check"></i> Dismiss
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- High Risk Notifications -->
                    <div class="risk-notifications-section">
                        <div class="section-header">
                            <h3><i class="fas fa-exclamation-triangle"></i> High Risk Notifications</h3>
                            <div class="notification-toggle">
                                <label class="switch">
                                    <input type="checkbox" id="notificationToggle" checked>
                                    <span class="slider"></span>
                                </label>
                                <span class="toggle-label">Enable Notifications</span>
                            </div>
                        </div>

                        <div class="notifications-list">
                            <!-- High Risk Notification -->
                            <div class="notification-item high-risk">
                                <div class="notification-icon">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <div class="notification-content">
                                    <div class="notification-header">
                                        <h4>Elevated Risk Level Detected</h4>
                                        <span class="notification-time">5 minutes ago</span>
                                    </div>
                                    <div class="notification-details">
                                        <p><strong>Area:</strong> Industrial District - Zone B</p>
                                        <p><strong>Risk Factor:</strong> 85% probability of criminal activity</p>
                                        <p><strong>Contributing Factors:</strong> Poor lighting, abandoned buildings, recent break-ins</p>
                                        <div class="risk-indicators">
                                            <div class="risk-item">
                                                <span class="risk-label">Lighting:</span>
                                                <span class="risk-value poor">Poor</span>
                                            </div>
                                            <div class="risk-item">
                                                <span class="risk-label">Activity:</span>
                                                <span class="risk-value high">High</span>
                                            </div>
                                            <div class="risk-item">
                                                <span class="risk-label">History:</span>
                                                <span class="risk-value elevated">Elevated</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="notification-actions">
                                        <button class="btn btn-primary btn-sm">
                                            <i class="fas fa-map-marked-alt"></i> Deploy Resources
                                        </button>
                                        <button class="btn btn-secondary btn-sm">
                                            <i class="fas fa-bullhorn"></i> Issue Warning
                                        </button>
                                        <button class="btn btn-outline btn-sm">
                                            <i class="fas fa-times"></i> Dismiss
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Critical Risk Notification -->
                            <div class="notification-item critical-risk">
                                <div class="notification-icon">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <div class="notification-content">
                                    <div class="notification-header">
                                        <h4>Critical Risk Alert</h4>
                                        <span class="notification-time">1 minute ago</span>
                                    </div>
                                    <div class="notification-details">
                                        <p><strong>Area:</strong> Entertainment District - Nightlife Zone</p>
                                        <p><strong>Risk Factor:</strong> 92% probability of violent incidents</p>
                                        <p><strong>Trigger:</strong> Multiple assault patterns detected + alcohol establishment density</p>
                                        <div class="risk-indicators">
                                            <div class="risk-item">
                                                <span class="risk-label">Crowd:</span>
                                                <span class="risk-value critical">Dense</span>
                                            </div>
                                            <div class="risk-item">
                                                <span class="risk-label">Time:</span>
                                                <span class="risk-value critical">Peak Hours</span>
                                            </div>
                                            <div class="risk-item">
                                                <span class="risk-label">Threat:</span>
                                                <span class="risk-value critical">Violent</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="notification-actions">
                                        <button class="btn btn-danger btn-sm">
                                            <i class="fas fa-shield-alt"></i> Emergency Response
                                        </button>
                                        <button class="btn btn-primary btn-sm">
                                            <i class="fas fa-users"></i> Increase Patrol
                                        </button>
                                        <button class="btn btn-outline btn-sm">
                                            <i class="fas fa-times"></i> Dismiss
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Medium Risk Notification -->
                            <div class="notification-item medium-risk">
                                <div class="notification-icon">
                                    <i class="fas fa-exclamation-circle"></i>
                                </div>
                                <div class="notification-content">
                                    <div class="notification-header">
                                        <h4>Moderate Risk Increase</h4>
                                        <span class="notification-time">30 minutes ago</span>
                                    </div>
                                    <div class="notification-details">
                                        <p><strong>Area:</strong> Residential Area - Greenwood</p>
                                        <p><strong>Risk Factor:</strong> 67% probability of property crimes</p>
                                        <p><strong>Reason:</strong> Recent series of vehicle break-ins</p>
                                        <div class="risk-indicators">
                                            <div class="risk-item">
                                                <span class="risk-label">Security:</span>
                                                <span class="risk-value moderate">Moderate</span>
                                            </div>
                                            <div class="risk-item">
                                                <span class="risk-label">Coverage:</span>
                                                <span class="risk-value moderate">Limited</span>
                                            </div>
                                            <div class="risk-item">
                                                <span class="risk-label">Trend:</span>
                                                <span class="risk-value increasing">Increasing</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="notification-actions">
                                        <button class="btn btn-primary btn-sm">
                                            <i class="fas fa-search"></i> Increase Surveillance
                                        </button>
                                        <button class="btn btn-secondary btn-sm">
                                            <i class="fas fa-envelope"></i> Community Alert
                                        </button>
                                        <button class="btn btn-outline btn-sm">
                                            <i class="fas fa-times"></i> Dismiss
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Alert History -->
                    <div class="history-section">
                        <div class="section-header">
                            <h3><i class="fas fa-history"></i> Recent Alert History</h3>
                            <button class="btn btn-outline btn-sm">
                                <i class="fas fa-download"></i> Export Report
                            </button>
                        </div>
                        <div class="history-table-container">
                            <table class="history-table">
                                <thead>
                                    <tr>
                                        <th>Alert ID</th>
                                        <th>Type</th>
                                        <th>Location</th>
                                        <th>Severity</th>
                                        <th>Incidents</th>
                                        <th>Detected</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>#ALT-001</td>
                                        <td>Theft Cluster</td>
                                        <td>Downtown CBD</td>
                                        <td><span class="severity-badge critical">Critical</span></td>
                                        <td>5</td>
                                        <td>Jan 15, 2024 14:30</td>
                                        <td><span class="status-badge active">Active</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary">View</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>#ALT-002</td>
                                        <td>Burglary Pattern</td>
                                        <td>Greenwood</td>
                                        <td><span class="severity-badge high">High</span></td>
                                        <td>4</td>
                                        <td>Jan 15, 2024 12:15</td>
                                        <td><span class="status-badge active">Active</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary">View</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>#ALT-003</td>
                                        <td>Vehicle Theft</td>
                                        <td>Mall Parking</td>
                                        <td><span class="severity-badge medium">Medium</span></td>
                                        <td>3</td>
                                        <td>Jan 15, 2024 10:45</td>
                                        <td><span class="status-badge active">Active</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary">View</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>#ALT-004</td>
                                        <td>Assault Pattern</td>
                                        <td>Entertainment District</td>
                                        <td><span class="severity-badge high">High</span></td>
                                        <td>6</td>
                                        <td>Jan 14, 2024 22:30</td>
                                        <td><span class="status-badge resolved">Resolved</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-secondary">View</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>#ALT-005</td>
                                        <td>Vandalism</td>
                                        <td>City Park</td>
                                        <td><span class="severity-badge low">Low</span></td>
                                        <td>3</td>
                                        <td>Jan 14, 2024 18:20</td>
                                        <td><span class="status-badge resolved">Resolved</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-secondary">View</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include('../includes/admin-footer.php') ?>
    </div>

    <script>
        // Crime Alerts JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            initializeAlerts();
        });

        function initializeAlerts() {
            // Filter handlers
            const severityFilter = document.getElementById('severityFilter');
            const timeFilter = document.getElementById('timeFilter');
            
            severityFilter.addEventListener('change', filterAlerts);
            timeFilter.addEventListener('change', filterAlerts);
            
            // Alert action handlers
            initializeAlertActions();
            
            // Notification handlers
            initializeNotifications();
        }

        function filterAlerts() {
            const severity = document.getElementById('severityFilter').value;
            const time = document.getElementById('timeFilter').value;
            
            console.log(`Filtering alerts: severity=${severity}, time=${time}`);
            // Here you would typically filter the alerts display
            // For now, this is just a placeholder
        }

        function initializeAlertActions() {
            // Dismiss alert buttons
            document.querySelectorAll('.alert-actions .btn-outline').forEach(btn => {
                btn.addEventListener('click', function() {
                    const alertItem = this.closest('.alert-item');
                    if (confirm('Are you sure you want to dismiss this alert?')) {
                        alertItem.style.opacity = '0.5';
                        this.textContent = 'Dismissed';
                        this.disabled = true;
                    }
                });
            });

            // View map buttons
            document.querySelectorAll('.alert-actions .btn-primary').forEach(btn => {
                btn.addEventListener('click', function() {
                    if (this.textContent.includes('Map')) {
                        alert('Map view would open here showing the alert location');
                    }
                });
            });

            // View details buttons
            document.querySelectorAll('.alert-actions .btn-secondary').forEach(btn => {
                btn.addEventListener('click', function() {
                    if (this.textContent.includes('Details')) {
                        alert('Detailed alert information would open here');
                    }
                });
            });
        }

        function initializeNotifications() {
            // Notification toggle
            const notificationToggle = document.getElementById('notificationToggle');
            notificationToggle.addEventListener('change', function() {
                const notificationsList = document.querySelector('.notifications-list');
                if (this.checked) {
                    notificationsList.style.display = 'flex';
                } else {
                    notificationsList.style.display = 'none';
                }
            });

            // Notification action buttons
            document.querySelectorAll('.notification-actions .btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const notificationItem = this.closest('.notification-item');
                    
                    if (this.textContent.includes('Emergency Response')) {
                        alert('Emergency response team would be dispatched immediately');
                        notificationItem.style.opacity = '0.5';
                        this.disabled = true;
                    } else if (this.textContent.includes('Deploy Resources')) {
                        alert('Resources would be deployed to Industrial District - Zone B');
                    } else if (this.textContent.includes('Increase Patrol')) {
                        alert('Patrol units would be increased in Entertainment District');
                    } else if (this.textContent.includes('Increase Surveillance')) {
                        alert('Surveillance cameras would be activated in Greenwood area');
                    } else if (this.textContent.includes('Issue Warning')) {
                        alert('Community warning would be issued for Industrial District');
                    } else if (this.textContent.includes('Community Alert')) {
                        alert('Community alert notification would be sent to residents');
                    } else if (this.textContent.includes('Dismiss')) {
                        if (confirm('Are you sure you want to dismiss this notification?')) {
                            notificationItem.style.opacity = '0.5';
                            this.textContent = 'Dismissed';
                            this.disabled = true;
                        }
                    }
                });
            });
        }
    </script>
</body>
</html>