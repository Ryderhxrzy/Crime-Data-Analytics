<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Overview - Crime Data Analytics</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/admin-header.css">
    <link rel="stylesheet" href="../css/hero.css">
    <link rel="stylesheet" href="../css/sidebar-footer.css">
    <link rel="stylesheet" href="../css/system-overview.css">
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
                        <li class="breadcrumb-item active" aria-current="page">
                            <span>System Overview</span>
                        </li>
                    </ol>
                </nav>
                <h1>System Overview</h1>
                <p>Real-time monitoring and comprehensive analytics of crime data across all districts. Track incidents, manage alerts, and gain insights into crime patterns and trends.</p>
            </div>
            
            <div class="sub-container">
                <div class="page-content">
                    <!-- Main Statistics Dashboard -->
                    <div class="dashboard-grid">
                        <!-- Total Crimes Card -->
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-card-icon primary">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <div class="stat-card-info">
                                    <div class="stat-card-label">Total Crimes</div>
                                    <div class="stat-card-value">12,847</div>
                                </div>
                            </div>
                            <div class="stat-card-footer">
                                <span class="stat-trend up">
                                    <i class="fas fa-arrow-up"></i> 5.2%
                                </span>
                                <span style="margin-left: 0.5rem;">vs last month</span>
                            </div>
                        </div>

                        <!-- Today's Crimes Card -->
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-card-icon danger">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <div class="stat-card-info">
                                    <div class="stat-card-label">Crimes Today</div>
                                    <div class="stat-card-value">47</div>
                                </div>
                            </div>
                            <div class="stat-card-footer">
                                <span class="stat-trend down">
                                    <i class="fas fa-arrow-down"></i> 2.1%
                                </span>
                                <span style="margin-left: 0.5rem;">vs yesterday</span>
                            </div>
                        </div>

                        <!-- Active Alerts Card -->
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-card-icon warning">
                                    <i class="fas fa-bell"></i>
                                </div>
                                <div class="stat-card-info">
                                    <div class="stat-card-label">Active Alerts</div>
                                    <div class="stat-card-value">23</div>
                                </div>
                            </div>
                            <div class="stat-card-footer">
                                <span style="color: var(--text-secondary-1);">
                                    8 Critical • 10 High • 5 Medium
                                </span>
                            </div>
                        </div>

                        <!-- Resolved Cases Card -->
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-card-icon success">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="stat-card-info">
                                    <div class="stat-card-label">Resolved Cases</div>
                                    <div class="stat-card-value">8,392</div>
                                </div>
                            </div>
                            <div class="stat-card-footer">
                                <span style="color: var(--success-color); font-weight: 600;">65.3%</span>
                                <span style="margin-left: 0.5rem;">resolution rate</span>
                            </div>
                        </div>
                    </div>

                    <!-- Time Period Statistics -->
                    <div class="incidents-section">
                        <div class="section-header">
                            <h2 class="section-title">Crime Statistics by Period</h2>
                        </div>

                        <div class="time-period-tabs">
                            <button class="time-tab" onclick="showPeriod('today')">Today</button>
                            <button class="time-tab active" onclick="showPeriod('week')">This Week</button>
                            <button class="time-tab" onclick="showPeriod('month')">This Month</button>
                            <button class="time-tab" onclick="showPeriod('year')">This Year</button>
                        </div>

                        <div class="quick-stats-grid" id="period-stats">
                            <div class="quick-stat-item">
                                <div class="quick-stat-value">324</div>
                                <div class="quick-stat-label">Total Incidents</div>
                            </div>
                            <div class="quick-stat-item">
                                <div class="quick-stat-value">89</div>
                                <div class="quick-stat-label">Thefts</div>
                            </div>
                            <div class="quick-stat-item">
                                <div class="quick-stat-value">56</div>
                                <div class="quick-stat-label">Assaults</div>
                            </div>
                            <div class="quick-stat-item">
                                <div class="quick-stat-value">42</div>
                                <div class="quick-stat-label">Vandalism</div>
                            </div>
                            <div class="quick-stat-item">
                                <div class="quick-stat-value">67</div>
                                <div class="quick-stat-label">Burglaries</div>
                            </div>
                            <div class="quick-stat-item">
                                <div class="quick-stat-value">70</div>
                                <div class="quick-stat-label">Other Crimes</div>
                            </div>
                        </div>
                    </div>

                    <!-- Active Alerts Section -->
                    <div class="incidents-section">
                        <div class="section-header">
                            <h2 class="section-title">Active Alerts</h2>
                            <a href="#" class="view-all-btn">
                                View All Alerts <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>

                        <div class="alerts-grid">
                            <div class="alert-card critical">
                                <div class="alert-title">
                                    <i class="fas fa-exclamation-circle"></i> Armed Robbery in Progress
                                </div>
                                <div class="alert-message">
                                    Multiple reports of armed robbery at Downtown Shopping District. Units dispatched to the scene.
                                </div>
                                <div class="alert-footer">
                                    <span class="alert-priority critical">CRITICAL</span>
                                    <span class="alert-time">5 min ago</span>
                                </div>
                            </div>

                            <div class="alert-card critical">
                                <div class="alert-title">
                                    <i class="fas fa-exclamation-circle"></i> Multiple Break-ins Reported
                                </div>
                                <div class="alert-message">
                                    Pattern detected: 5 residential break-ins in North District within 2 hours.
                                </div>
                                <div class="alert-footer">
                                    <span class="alert-priority critical">CRITICAL</span>
                                    <span class="alert-time">12 min ago</span>
                                </div>
                            </div>

                            <div class="alert-card high">
                                <div class="alert-title">
                                    <i class="fas fa-exclamation-triangle"></i> Vehicle Theft Spike
                                </div>
                                <div class="alert-message">
                                    Unusual increase in vehicle theft reports in East District parking areas.
                                </div>
                                <div class="alert-footer">
                                    <span class="alert-priority high">HIGH</span>
                                    <span class="alert-time">28 min ago</span>
                                </div>
                            </div>

                            <div class="alert-card high">
                                <div class="alert-title">
                                    <i class="fas fa-exclamation-triangle"></i> Suspicious Activity Pattern
                                </div>
                                <div class="alert-message">
                                    Repeated suspicious activity reports near Central Park area after dark.
                                </div>
                                <div class="alert-footer">
                                    <span class="alert-priority high">HIGH</span>
                                    <span class="alert-time">1 hour ago</span>
                                </div>
                            </div>

                            <div class="alert-card medium">
                                <div class="alert-title">
                                    <i class="fas fa-info-circle"></i> Weekend Crime Forecast
                                </div>
                                <div class="alert-message">
                                    Based on historical data, increased patrols recommended for South District this weekend.
                                </div>
                                <div class="alert-footer">
                                    <span class="alert-priority medium">MEDIUM</span>
                                    <span class="alert-time">2 hours ago</span>
                                </div>
                            </div>

                            <div class="alert-card medium">
                                <div class="alert-title">
                                    <i class="fas fa-info-circle"></i> System Maintenance Scheduled
                                </div>
                                <div class="alert-message">
                                    Database optimization scheduled for tonight at 2:00 AM. Expected downtime: 30 minutes.
                                </div>
                                <div class="alert-footer">
                                    <span class="alert-priority medium">MEDIUM</span>
                                    <span class="alert-time">3 hours ago</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Latest Incidents Section -->
                    <div class="incidents-section">
                        <div class="section-header">
                            <h2 class="section-title">Latest Recorded Incidents</h2>
                            <a href="#" class="view-all-btn">
                                View All Incidents <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>

                        <div class="incident-card">
                            <div class="incident-header">
                                <span class="incident-type theft">
                                    <i class="fas fa-user-secret"></i> Armed Robbery
                                </span>
                                <span class="incident-time">15 minutes ago</span>
                            </div>
                            <div class="incident-description">
                                Armed robbery reported at First National Bank, Downtown Branch. Two suspects fled the scene in a dark sedan. No injuries reported.
                            </div>
                            <div class="incident-meta">
                                <span class="incident-meta-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    Downtown District
                                </span>
                                <span class="incident-meta-item">
                                    <i class="fas fa-fingerprint"></i>
                                    Case #2024-12847
                                </span>
                                <span class="incident-meta-item">
                                    <i class="fas fa-user-shield"></i>
                                    Officers dispatched
                                </span>
                            </div>
                        </div>

                        <div class="incident-card">
                            <div class="incident-header">
                                <span class="incident-type assault">
                                    <i class="fas fa-fist-raised"></i> Assault
                                </span>
                                <span class="incident-time">1 hour ago</span>
                            </div>
                            <div class="incident-description">
                                Physical altercation between two individuals at Main Street Bar. One victim transported to hospital with minor injuries. Suspect in custody.
                            </div>
                            <div class="incident-meta">
                                <span class="incident-meta-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    Central District
                                </span>
                                <span class="incident-meta-item">
                                    <i class="fas fa-fingerprint"></i>
                                    Case #2024-12846
                                </span>
                                <span class="incident-meta-item">
                                    <i class="fas fa-check-circle"></i>
                                    Suspect arrested
                                </span>
                            </div>
                        </div>

                        <div class="incident-card">
                            <div class="incident-header">
                                <span class="incident-type theft">
                                    <i class="fas fa-car"></i> Vehicle Theft
                                </span>
                                <span class="incident-time">2 hours ago</span>
                            </div>
                            <div class="incident-description">
                                Blue Honda Civic reported stolen from residential parking lot on Oak Avenue. Security footage being reviewed.
                            </div>
                            <div class="incident-meta">
                                <span class="incident-meta-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    East District
                                </span>
                                <span class="incident-meta-item">
                                    <i class="fas fa-fingerprint"></i>
                                    Case #2024-12845
                                </span>
                                <span class="incident-meta-item">
                                    <i class="fas fa-search"></i>
                                    Under investigation
                                </span>
                            </div>
                        </div>

                        <div class="incident-card">
                            <div class="incident-header">
                                <span class="incident-type vandalism">
                                    <i class="fas fa-spray-can"></i> Vandalism
                                </span>
                                <span class="incident-time">3 hours ago</span>
                            </div>
                            <div class="incident-description">
                                Graffiti reported on public property at City Park. Parks department notified for cleanup. Investigation ongoing.
                            </div>
                            <div class="incident-meta">
                                <span class="incident-meta-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    South District
                                </span>
                                <span class="incident-meta-item">
                                    <i class="fas fa-fingerprint"></i>
                                    Case #2024-12844
                                </span>
                                <span class="incident-meta-item">
                                    <i class="fas fa-search"></i>
                                    Under investigation
                                </span>
                            </div>
                        </div>

                        <div class="incident-card">
                            <div class="incident-header">
                                <span class="incident-type fraud">
                                    <i class="fas fa-credit-card"></i> Fraud
                                </span>
                                <span class="incident-time">5 hours ago</span>
                            </div>
                            <div class="incident-description">
                                Credit card fraud reported by local resident. Multiple unauthorized transactions detected. Financial crimes unit investigating.
                            </div>
                            <div class="incident-meta">
                                <span class="incident-meta-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    West District
                                </span>
                                <span class="incident-meta-item">
                                    <i class="fas fa-fingerprint"></i>
                                    Case #2024-12843
                                </span>
                                <span class="incident-meta-item">
                                    <i class="fas fa-search"></i>
                                    Under investigation
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Statistics Section -->
                    <div class="incidents-section">
                        <div class="section-header">
                            <h2 class="section-title">District Performance Metrics</h2>
                        </div>

                        <div class="quick-stats-grid">
                            <div class="quick-stat-item">
                                <div class="quick-stat-value">4.2 min</div>
                                <div class="quick-stat-label">Avg Response Time</div>
                            </div>
                            <div class="quick-stat-item">
                                <div class="quick-stat-value">92%</div>
                                <div class="quick-stat-label">Report Accuracy</div>
                            </div>
                            <div class="quick-stat-item">
                                <div class="quick-stat-value">156</div>
                                <div class="quick-stat-label">Active Officers</div>
                            </div>
                            <div class="quick-stat-item">
                                <div class="quick-stat-value">28</div>
                                <div class="quick-stat-label">Active Patrols</div>
                            </div>
                            <div class="quick-stat-item">
                                <div class="quick-stat-value">7,842</div>
                                <div class="quick-stat-label">Citizens Assisted</div>
                            </div>
                            <div class="quick-stat-item">
                                <div class="quick-stat-value">15</div>
                                <div class="quick-stat-label">Ongoing Operations</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include('../includes/admin-footer.php') ?>
    </div>

    <script>
        // Time period tab functionality
        function showPeriod(period) {
            // Update active tab
            const tabs = document.querySelectorAll('.time-tab');
            tabs.forEach(tab => {
                tab.classList.remove('active');
                if (tab.textContent.toLowerCase().includes(period)) {
                    tab.classList.add('active');
                }
            });

            // Update stats based on period (static data)
            const statsData = {
                today: {
                    total: 47,
                    thefts: 12,
                    assaults: 8,
                    vandalism: 5,
                    burglaries: 11,
                    other: 11
                },
                week: {
                    total: 324,
                    thefts: 89,
                    assaults: 56,
                    vandalism: 42,
                    burglaries: 67,
                    other: 70
                },
                month: {
                    total: 1428,
                    thefts: 387,
                    assaults: 234,
                    vandalism: 189,
                    burglaries: 298,
                    other: 320
                },
                year: {
                    total: 12847,
                    thefts: 3542,
                    assaults: 2156,
                    vandalism: 1876,
                    burglaries: 2584,
                    other: 2689
                }
            };

            const data = statsData[period];
            const statsGrid = document.getElementById('period-stats');
            statsGrid.innerHTML = `
                <div class="quick-stat-item">
                    <div class="quick-stat-value">${data.total}</div>
                    <div class="quick-stat-label">Total Incidents</div>
                </div>
                <div class="quick-stat-item">
                    <div class="quick-stat-value">${data.thefts}</div>
                    <div class="quick-stat-label">Thefts</div>
                </div>
                <div class="quick-stat-item">
                    <div class="quick-stat-value">${data.assaults}</div>
                    <div class="quick-stat-label">Assaults</div>
                </div>
                <div class="quick-stat-item">
                    <div class="quick-stat-value">${data.vandalism}</div>
                    <div class="quick-stat-label">Vandalism</div>
                </div>
                <div class="quick-stat-item">
                    <div class="quick-stat-value">${data.burglaries}</div>
                    <div class="quick-stat-label">Burglaries</div>
                </div>
                <div class="quick-stat-item">
                    <div class="quick-stat-value">${data.other}</div>
                    <div class="quick-stat-label">Other Crimes</div>
                </div>
            `;
        }
    </script>
</body>
</html>