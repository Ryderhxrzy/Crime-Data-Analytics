<?php
// Authentication check - must be at the top of every admin page
require_once '../../api/middleware/auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Risk Notifications | Crime Dep.</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/admin-header.css">
    <link rel="stylesheet" href="../css/hero.css">
    <link rel="stylesheet" href="../css/sidebar-footer.css">
    <link rel="stylesheet" href="../css/risk-notifications.css">
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
                            <span>Risk Notifications</span>
                        </li>
                    </ol>
                </nav>
                <h1>High-Risk Notifications</h1>
                <p>Advanced risk assessment and notification system. Monitor high-risk areas, receive threshold breach alerts, and manage proactive safety measures.</p>
            </div>
            
            <div class="sub-container">
                <div class="page-content">
                    <!-- Risk Overview Dashboard -->
                    <div class="risk-overview-grid">
                        <div class="risk-card critical">
                            <div class="risk-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="risk-content">
                                <h3>2</h3>
                                <p>Critical Risk Areas</p>
                                <div class="risk-trend up">
                                    <i class="fas fa-arrow-up"></i> 25% from yesterday
                                </div>
                            </div>
                        </div>
                        <div class="risk-card high">
                            <div class="risk-icon">
                                <i class="fas fa-exclamation-circle"></i>
                            </div>
                            <div class="risk-content">
                                <h3>5</h3>
                                <p>High Risk Zones</p>
                                <div class="risk-trend stable">
                                    <i class="fas fa-minus"></i> No change
                                </div>
                            </div>
                        </div>
                        <div class="risk-card monitoring">
                            <div class="risk-icon">
                                <i class="fas fa-eye"></i>
                            </div>
                            <div class="risk-content">
                                <h3>12</h3>
                                <p>Under Monitoring</p>
                                <div class="risk-trend down">
                                    <i class="fas fa-arrow-down"></i> 8% decrease
                                </div>
                            </div>
                        </div>
                        <div class="risk-card threshold">
                            <div class="risk-icon">
                                <i class="fas fa-bell"></i>
                            </div>
                            <div class="risk-content">
                                <h3>3</h3>
                                <p>Threshold Breaches</p>
                                <div class="risk-trend up">
                                    <i class="fas fa-arrow-up"></i> New alerts
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Risk Criteria Definition -->
                    <div class="risk-criteria-section">
                        <div class="section-header">
                            <h3><i class="fas fa-cogs"></i> Risk Assessment Criteria</h3>
                            <button class="btn btn-outline btn-sm">
                                <i class="fas fa-edit"></i> Edit Criteria
                            </button>
                        </div>
                        <div class="criteria-grid">
                            <div class="criteria-card">
                                <div class="criteria-header">
                                    <h4>Criminal Activity Density</h4>
                                    <span class="criteria-weight">Weight: 30%</span>
                                </div>
                                <div class="criteria-levels">
                                    <div class="level-item critical">
                                        <span class="level-label">Critical</span>
                                        <span class="level-value">> 10 incidents/km²</span>
                                    </div>
                                    <div class="level-item high">
                                        <span class="level-label">High</span>
                                        <span class="level-value">5-10 incidents/km²</span>
                                    </div>
                                    <div class="level-item medium">
                                        <span class="level-label">Medium</span>
                                        <span class="level-value">2-5 incidents/km²</span>
                                    </div>
                                    <div class="level-item low">
                                        <span class="level-label">Low</span>
                                        <span class="level-value">< 2 incidents/km²</span>
                                    </div>
                                </div>
                            </div>
                            <div class="criteria-card">
                                <div class="criteria-header">
                                    <h4>Time-Based Risk Factors</h4>
                                    <span class="criteria-weight">Weight: 25%</span>
                                </div>
                                <div class="criteria-levels">
                                    <div class="level-item critical">
                                        <span class="level-label">Critical</span>
                                        <span class="level-value">Night hours (22:00-04:00)</span>
                                    </div>
                                    <div class="level-item high">
                                        <span class="level-label">High</span>
                                        <span class="level-value">Evening (18:00-22:00)</span>
                                    </div>
                                    <div class="level-item medium">
                                        <span class="level-label">Medium</span>
                                        <span class="level-value">Afternoon (14:00-18:00)</span>
                                    </div>
                                    <div class="level-item low">
                                        <span class="level-label">Low</span>
                                        <span class="level-value">Morning (06:00-14:00)</span>
                                    </div>
                                </div>
                            </div>
                            <div class="criteria-card">
                                <div class="criteria-header">
                                    <h4>Environmental Factors</h4>
                                    <span class="criteria-weight">Weight: 20%</span>
                                </div>
                                <div class="criteria-levels">
                                    <div class="level-item critical">
                                        <span class="level-label">Critical</span>
                                        <span class="level-value">Poor lighting + abandoned areas</span>
                                    </div>
                                    <div class="level-item high">
                                        <span class="level-label">High</span>
                                        <span class="level-value">Limited visibility + sparse population</span>
                                    </div>
                                    <div class="level-item medium">
                                        <span class="level-label">Medium</span>
                                        <span class="level-value">Moderate lighting + some activity</span>
                                    </div>
                                    <div class="level-item low">
                                        <span class="level-label">Low</span>
                                        <span class="level-value">Good lighting + high activity</span>
                                    </div>
                                </div>
                            </div>
                            <div class="criteria-card">
                                <div class="criteria-header">
                                    <h4>Historical Patterns</h4>
                                    <span class="criteria-weight">Weight: 25%</span>
                                </div>
                                <div class="criteria-levels">
                                    <div class="level-item critical">
                                        <span class="level-label">Critical</span>
                                        <span class="level-value">Recurring violent incidents</span>
                                    </div>
                                    <div class="level-item high">
                                        <span class="level-label">High</span>
                                        <span class="level-value">Increasing trend over 3 months</span>
                                    </div>
                                    <div class="level-item medium">
                                        <span class="level-label">Medium</span>
                                        <span class="level-value">Seasonal patterns detected</span>
                                    </div>
                                    <div class="level-item low">
                                        <span class="level-label">Low</span>
                                        <span class="level-value">Stable or decreasing trends</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Active Risk Notifications -->
                    <div class="notifications-section">
                        <div class="section-header">
                            <h3><i class="fas fa-bell"></i> Active Risk Notifications</h3>
                            <div class="notification-controls">
                                <select class="form-control" id="priorityFilter">
                                    <option value="all">All Priorities</option>
                                    <option value="critical">Critical</option>
                                    <option value="high">High</option>
                                    <option value="medium">Medium</option>
                                    <option value="low">Low</option>
                                </select>
                                <select class="form-control" id="statusFilter">
                                    <option value="all">All Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="acknowledged">Acknowledged</option>
                                    <option value="resolved">Resolved</option>
                                </select>
                            </div>
                        </div>

                        <div class="notifications-list">
                            <!-- Critical Priority Notification -->
                            <div class="notification-item critical" data-priority="critical" data-status="pending">
                                <div class="notification-header">
                                    <div class="notification-title">
                                        <h4>Threshold Breach: Critical Risk Level Exceeded</h4>
                                        <div class="notification-badges">
                                            <span class="priority-badge critical">CRITICAL</span>
                                            <span class="status-badge pending">PENDING</span>
                                        </div>
                                    </div>
                                    <div class="notification-time">
                                        <i class="fas fa-clock"></i> 2 minutes ago
                                    </div>
                                </div>
                                <div class="notification-details">
                                    <div class="risk-score">
                                        <div class="score-display">
                                            <span class="score-value">92</span>
                                            <span class="score-max">/100</span>
                                        </div>
                                        <div class="score-label">Risk Score</div>
                                    </div>
                                    <div class="risk-factors">
                                        <h5>Risk Assessment Details:</h5>
                                        <ul>
                                            <li><strong>Location:</strong> Entertainment District - Nightlife Zone</li>
                                            <li><strong>Trigger:</strong> Criminal activity density exceeded threshold (12.3 incidents/km²)</li>
                                            <li><strong>Time Factor:</strong> Peak nightlife hours (23:45)</li>
                                            <li><strong>Environmental:</strong> Poor lighting in alleyways + high crowd density</li>
                                            <li><strong>Historical:</strong> 45% increase in violent incidents over past month</li>
                                        </ul>
                                    </div>
                                    <div class="recommendations">
                                        <h5>Recommended Actions:</h5>
                                        <div class="recommendation-list">
                                            <div class="recommendation-item urgent">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                <span>Deploy additional patrol units immediately</span>
                                            </div>
                                            <div class="recommendation-item high">
                                                <i class="fas fa-shield-alt"></i>
                                                <span>Establish temporary security checkpoints</span>
                                            </div>
                                            <div class="recommendation-item medium">
                                                <i class="fas fa-bullhorn"></i>
                                                <span>Issue public safety announcements</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="notification-actions">
                                    <button class="btn btn-danger btn-sm" onclick="acknowledgeNotification(this, 'critical')">
                                        <i class="fas fa-check-circle"></i> Acknowledge
                                    </button>
                                    <button class="btn btn-primary btn-sm" onclick="deployResources(this)">
                                        <i class="fas fa-users"></i> Deploy Resources
                                    </button>
                                    <button class="btn btn-secondary btn-sm" onclick="viewDetails(this)">
                                        <i class="fas fa-info-circle"></i> Full Details
                                    </button>
                                    <button class="btn btn-outline btn-sm" onclick="dismissNotification(this)">
                                        <i class="fas fa-times"></i> Dismiss
                                    </button>
                                </div>
                            </div>

                            <!-- High Priority Notification -->
                            <div class="notification-item high" data-priority="high" data-status="pending">
                                <div class="notification-header">
                                    <div class="notification-title">
                                        <h4>Risk Escalation: Industrial Zone Security Concern</h4>
                                        <div class="notification-badges">
                                            <span class="priority-badge high">HIGH</span>
                                            <span class="status-badge pending">PENDING</span>
                                        </div>
                                    </div>
                                    <div class="notification-time">
                                        <i class="fas fa-clock"></i> 15 minutes ago
                                    </div>
                                </div>
                                <div class="notification-details">
                                    <div class="risk-score">
                                        <div class="score-display">
                                            <span class="score-value">78</span>
                                            <span class="score-max">/100</span>
                                        </div>
                                        <div class="score-label">Risk Score</div>
                                    </div>
                                    <div class="risk-factors">
                                        <h5>Risk Assessment Details:</h5>
                                        <ul>
                                            <li><strong>Location:</strong> Industrial District - Zone B</li>
                                            <li><strong>Trigger:</strong> Break-in pattern detected (3 incidents in 48 hours)</li>
                                            <li><strong>Time Factor:</strong> After business hours (20:30)</li>
                                            <li><strong>Environmental:</strong> Abandoned warehouses + poor surveillance coverage</li>
                                            <li><strong>Historical:</strong> Recurring property crime patterns</li>
                                        </ul>
                                    </div>
                                    <div class="recommendations">
                                        <h5>Recommended Actions:</h5>
                                        <div class="recommendation-list">
                                            <div class="recommendation-item high">
                                                <i class="fas fa-search"></i>
                                                <span>Increase surveillance camera monitoring</span>
                                            </div>
                                            <div class="recommendation-item medium">
                                                <i class="fas fa-car"></i>
                                                <span>Conduct regular security patrols</span>
                                            </div>
                                            <div class="recommendation-item low">
                                                <i class="fas fa-envelope"></i>
                                                <span>Notify property owners</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="notification-actions">
                                    <button class="btn btn-primary btn-sm" onclick="acknowledgeNotification(this, 'high')">
                                        <i class="fas fa-check-circle"></i> Acknowledge
                                    </button>
                                    <button class="btn btn-secondary btn-sm" onclick="viewDetails(this)">
                                        <i class="fas fa-info-circle"></i> Full Details
                                    </button>
                                    <button class="btn btn-outline btn-sm" onclick="dismissNotification(this)">
                                        <i class="fas fa-times"></i> Dismiss
                                    </button>
                                </div>
                            </div>

                            <!-- Medium Priority Notification -->
                            <div class="notification-item medium" data-priority="medium" data-status="acknowledged">
                                <div class="notification-header">
                                    <div class="notification-title">
                                        <h4>Risk Monitoring: Residential Area Alert</h4>
                                        <div class="notification-badges">
                                            <span class="priority-badge medium">MEDIUM</span>
                                            <span class="status-badge acknowledged">ACKNOWLEDGED</span>
                                        </div>
                                    </div>
                                    <div class="notification-time">
                                        <i class="fas fa-clock"></i> 1 hour ago
                                    </div>
                                </div>
                                <div class="notification-details">
                                    <div class="risk-score">
                                        <div class="score-display">
                                            <span class="score-value">65</span>
                                            <span class="score-max">/100</span>
                                        </div>
                                        <div class="score-label">Risk Score</div>
                                    </div>
                                    <div class="risk-factors">
                                        <h5>Risk Assessment Details:</h5>
                                        <ul>
                                            <li><strong>Location:</strong> Greenwood Residential Area</li>
                                            <li><strong>Trigger:</strong> Vehicle theft trend increase (67% probability)</li>
                                            <li><strong>Time Factor:</strong> Evening hours (19:00-22:00)</li>
                                            <li><strong>Environmental:</strong> Limited street lighting</li>
                                            <li><strong>Historical:</strong> Seasonal increase in property crimes</li>
                                        </ul>
                                    </div>
                                    <div class="recommendations">
                                        <h5>Recommended Actions:</h5>
                                        <div class="recommendation-list">
                                            <div class="recommendation-item medium">
                                                <i class="fas fa-lightbulb"></i>
                                                <span>Improve street lighting</span>
                                            </div>
                                            <div class="recommendation-item low">
                                                <i class="fas fa-users"></i>
                                                <span>Neighborhood watch program</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="notification-actions">
                                    <button class="btn btn-secondary btn-sm" onclick="viewDetails(this)">
                                        <i class="fas fa-info-circle"></i> Full Details
                                    </button>
                                    <button class="btn btn-success btn-sm" onclick="resolveNotification(this)">
                                        <i class="fas fa-check"></i> Mark Resolved
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notification Log -->
                    <div class="notification-log-section">
                        <div class="section-header">
                            <h3><i class="fas fa-history"></i> Notification Log</h3>
                            <div class="log-controls">
                                <button class="btn btn-outline btn-sm" onclick="exportNotificationLog()">
                                    <i class="fas fa-download"></i> Export Log
                                </button>
                                <button class="btn btn-outline btn-sm" onclick="clearResolvedNotifications()">
                                    <i class="fas fa-trash"></i> Clear Resolved
                                </button>
                            </div>
                        </div>
                        <div class="log-table-container">
                            <table class="log-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Priority</th>
                                        <th>Location</th>
                                        <th>Risk Score</th>
                                        <th>Trigger</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Acknowledged</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>#RISK-001</td>
                                        <td><span class="priority-badge critical">Critical</span></td>
                                        <td>Entertainment District</td>
                                        <td>92/100</td>
                                        <td>Threshold breach</td>
                                        <td><span class="status-badge pending">Pending</span></td>
                                        <td>Jan 15, 2024 23:45</td>
                                        <td>-</td>
                                        <td>
                                            <button class="btn btn-sm btn-primary">View</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>#RISK-002</td>
                                        <td><span class="priority-badge high">High</span></td>
                                        <td>Industrial District</td>
                                        <td>78/100</td>
                                        <td>Pattern escalation</td>
                                        <td><span class="status-badge pending">Pending</span></td>
                                        <td>Jan 15, 2024 23:30</td>
                                        <td>-</td>
                                        <td>
                                            <button class="btn btn-sm btn-primary">View</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>#RISK-003</td>
                                        <td><span class="priority-badge medium">Medium</span></td>
                                        <td>Greenwood</td>
                                        <td>65/100</td>
                                        <td>Trend increase</td>
                                        <td><span class="status-badge acknowledged">Acknowledged</span></td>
                                        <td>Jan 15, 2024 22:45</td>
                                        <td>Jan 15, 2024 23:15</td>
                                        <td>
                                            <button class="btn btn-sm btn-secondary">View</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>#RISK-004</td>
                                        <td><span class="priority-badge low">Low</span></td>
                                        <td>City Park</td>
                                        <td>45/100</td>
                                        <td>Monitoring alert</td>
                                        <td><span class="status-badge resolved">Resolved</span></td>
                                        <td>Jan 15, 2024 20:30</td>
                                        <td>Jan 15, 2024 21:00</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline">View</button>
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
        // Risk Notifications JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            initializeRiskNotifications();
        });

        function initializeRiskNotifications() {
            // Filter handlers
            const priorityFilter = document.getElementById('priorityFilter');
            const statusFilter = document.getElementById('statusFilter');
            
            priorityFilter.addEventListener('change', filterNotifications);
            statusFilter.addEventListener('change', filterNotifications);
            
            // Auto-refresh notifications
            setInterval(refreshNotifications, 30000); // Refresh every 30 seconds
        }

        function filterNotifications() {
            const priority = document.getElementById('priorityFilter').value;
            const status = document.getElementById('statusFilter').value;
            const notifications = document.querySelectorAll('.notification-item');
            
            notifications.forEach(notification => {
                const notificationPriority = notification.dataset.priority;
                const notificationStatus = notification.dataset.status;
                
                const priorityMatch = priority === 'all' || notificationPriority === priority;
                const statusMatch = status === 'all' || notificationStatus === status;
                
                notification.style.display = priorityMatch && statusMatch ? 'block' : 'none';
            });
        }

        function acknowledgeNotification(button, priority) {
            const notification = button.closest('.notification-item');
            const statusBadge = notification.querySelector('.status-badge');
            
            if (priority === 'critical') {
                if (confirm('This is a CRITICAL priority notification. Immediate action required. Continue?')) {
                    statusBadge.className = 'status-badge acknowledged';
                    statusBadge.textContent = 'ACKNOWLEDGED';
                    notification.dataset.status = 'acknowledged';
                    button.disabled = true;
                    button.textContent = 'Acknowledged';
                    
                    // Send notification to relevant departments
                    alert('Emergency services and patrol units have been notified. Response team deployed.');
                }
            } else {
                statusBadge.className = 'status-badge acknowledged';
                statusBadge.textContent = 'ACKNOWLEDGED';
                notification.dataset.status = 'acknowledged';
                button.disabled = true;
                button.textContent = 'Acknowledged';
                
                alert('Notification acknowledged. Response team has been notified.');
            }
        }

        function deployResources(button) {
            const notification = button.closest('.notification-item');
            const location = notification.querySelector('li strong').nextSibling.textContent;
            
            if (confirm(`Deploy resources to ${location}?`)) {
                alert(`Resources are being deployed to ${location}. ETA: 5-10 minutes.`);
                button.disabled = true;
                button.textContent = 'Deployed';
            }
        }

        function viewDetails(button) {
            const notification = button.closest('.notification-item');
            const title = notification.querySelector('h4').textContent;
            
            alert(`Detailed view for: ${title}\n\nThis would open a comprehensive dashboard with:\n- Real-time risk metrics\n- Historical data trends\n- Resource deployment status\n- Communication logs\n- Action item tracking`);
        }

        function dismissNotification(button) {
            const notification = button.closest('.notification-item');
            
            if (confirm('Are you sure you want to dismiss this notification?')) {
                notification.style.opacity = '0.5';
                button.disabled = true;
                button.textContent = 'Dismissed';
                
                // Update status
                const statusBadge = notification.querySelector('.status-badge');
                if (statusBadge.textContent === 'PENDING') {
                    statusBadge.className = 'status-badge resolved';
                    statusBadge.textContent = 'RESOLVED';
                    notification.dataset.status = 'resolved';
                }
            }
        }

        function resolveNotification(button) {
            const notification = button.closest('.notification-item');
            const statusBadge = notification.querySelector('.status-badge');
            
            statusBadge.className = 'status-badge resolved';
            statusBadge.textContent = 'RESOLVED';
            notification.dataset.status = 'resolved';
            
            button.disabled = true;
            button.textContent = 'Resolved';
            
            alert('Notification marked as resolved and logged.');
        }

        function exportNotificationLog() {
            alert('Notification log would be exported as CSV/PDF with all notification details, timestamps, and action taken.');
        }

        function clearResolvedNotifications() {
            if (confirm('Clear all resolved notifications from the log?')) {
                alert('All resolved notifications have been cleared from the display.');
                // In a real implementation, this would filter out resolved notifications
            }
        }

        function refreshNotifications() {
            console.log('Refreshing notifications...');
            // In a real implementation, this would fetch new notifications from the server
        }
    </script>
</body>
</html>