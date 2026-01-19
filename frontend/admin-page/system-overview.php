<?php
// Authentication check - must be at the top of every admin page
require_once '../../api/middleware/auth.php';
require_once '../../api/config.php';

// Fetch statistics from database
$today = date('Y-m-d');
$yesterday = date('Y-m-d', strtotime('-1 day'));
$monthStart = date('Y-m-01');
$lastMonthStart = date('Y-m-01', strtotime('-1 month'));
$lastMonthEnd = date('Y-m-t', strtotime('-1 month'));
$weekAgo = date('Y-m-d', strtotime('-7 days'));

// Total crimes
$totalQuery = "SELECT COUNT(*) as total FROM crime_department_crime_incidents";
$totalResult = $mysqli->query($totalQuery);
$totalCrimes = $totalResult->fetch_assoc()['total'];

// Today's crimes
$todayQuery = "SELECT COUNT(*) as total FROM crime_department_crime_incidents WHERE incident_date = '$today'";
$todayResult = $mysqli->query($todayQuery);
$todayCrimes = $todayResult->fetch_assoc()['total'];

// Yesterday's crimes
$yesterdayQuery = "SELECT COUNT(*) as total FROM crime_department_crime_incidents WHERE incident_date = '$yesterday'";
$yesterdayResult = $mysqli->query($yesterdayQuery);
$yesterdayCrimes = $yesterdayResult->fetch_assoc()['total'];

// This month's crimes
$thisMonthQuery = "SELECT COUNT(*) as total FROM crime_department_crime_incidents WHERE incident_date >= '$monthStart'";
$thisMonthResult = $mysqli->query($thisMonthQuery);
$thisMonthCrimes = $thisMonthResult->fetch_assoc()['total'];

// Last month's crimes
$lastMonthQuery = "SELECT COUNT(*) as total FROM crime_department_crime_incidents WHERE incident_date BETWEEN '$lastMonthStart' AND '$lastMonthEnd'";
$lastMonthResult = $mysqli->query($lastMonthQuery);
$lastMonthCrimes = $lastMonthResult->fetch_assoc()['total'];

// Resolved cases
$resolvedQuery = "SELECT COUNT(*) as total FROM crime_department_crime_incidents WHERE clearance_status = 'cleared'";
$resolvedResult = $mysqli->query($resolvedQuery);
$resolvedCases = $resolvedResult->fetch_assoc()['total'];

// Calculate trends
$dailyTrend = $yesterdayCrimes > 0 ? round((($todayCrimes - $yesterdayCrimes) / $yesterdayCrimes) * 100, 1) : 0;
$monthlyTrend = $lastMonthCrimes > 0 ? round((($thisMonthCrimes - $lastMonthCrimes) / $lastMonthCrimes) * 100, 1) : 0;
$resolutionRate = $totalCrimes > 0 ? round(($resolvedCases / $totalCrimes) * 100, 1) : 0;

// Get crime counts by status
$statusQuery = "SELECT status, COUNT(*) as count FROM crime_department_crime_incidents GROUP BY status";
$statusResult = $mysqli->query($statusQuery);
$statusCounts = ['reported' => 0, 'under_investigation' => 0, 'resolved' => 0, 'closed' => 0];
while ($row = $statusResult->fetch_assoc()) {
    $statusCounts[$row['status']] = (int)$row['count'];
}

// Get latest incidents
$latestQuery = "
    SELECT
        ci.id,
        ci.incident_code,
        ci.incident_title,
        ci.incident_description,
        ci.incident_date,
        ci.incident_time,
        ci.status,
        ci.clearance_status,
        cc.category_name,
        cc.icon as category_icon,
        cc.color_code as category_color,
        b.barangay_name,
        b.city_municipality as district
    FROM crime_department_crime_incidents ci
    LEFT JOIN crime_department_crime_categories cc ON ci.crime_category_id = cc.id
    LEFT JOIN crime_department_barangays b ON ci.barangay_id = b.id
    ORDER BY ci.incident_date DESC, ci.incident_time DESC
    LIMIT 5
";
$latestResult = $mysqli->query($latestQuery);
$latestIncidents = [];
while ($row = $latestResult->fetch_assoc()) {
    $latestIncidents[] = $row;
}

// Get crime statistics by category for period tabs
$periodStatsQuery = "
    SELECT
        cc.category_name,
        COUNT(*) as count
    FROM crime_department_crime_incidents ci
    LEFT JOIN crime_department_crime_categories cc ON ci.crime_category_id = cc.id
    WHERE ci.incident_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY cc.id
    ORDER BY count DESC
    LIMIT 6
";
$periodStatsResult = $mysqli->query($periodStatsQuery);
$weekStats = [];
while ($row = $periodStatsResult->fetch_assoc()) {
    $weekStats[] = $row;
}

// Calculate time ago
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;

    if ($diff < 60) return 'just now';
    if ($diff < 3600) return floor($diff / 60) . ' min ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
    if ($diff < 604800) return floor($diff / 86400) . ' days ago';
    return date('M d, Y', $timestamp);
}

// Get status class for incidents
function getStatusClass($status) {
    switch ($status) {
        case 'reported': return 'theft';
        case 'under_investigation': return 'assault';
        case 'resolved': return 'fraud';
        case 'closed': return 'vandalism';
        default: return 'theft';
    }
}

function getStatusIcon($status) {
    switch ($status) {
        case 'reported': return 'fa-file-alt';
        case 'under_investigation': return 'fa-search';
        case 'resolved': return 'fa-check-circle';
        case 'closed': return 'fa-archive';
        default: return 'fa-file-alt';
    }
}

function getStatusLabel($status) {
    switch ($status) {
        case 'reported': return 'Reported';
        case 'under_investigation': return 'Under Investigation';
        case 'resolved': return 'Resolved';
        case 'closed': return 'Closed';
        default: return ucfirst($status);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Overview | Crime Dep.</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/admin-header.css">
    <link rel="stylesheet" href="../css/hero.css">
    <link rel="stylesheet" href="../css/sidebar-footer.css">
    <link rel="stylesheet" href="../css/system-overview.css">
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
                                    <div class="stat-card-value"><?php echo number_format($totalCrimes); ?></div>
                                </div>
                            </div>
                            <div class="stat-card-footer">
                                <span class="stat-trend <?php echo $monthlyTrend >= 0 ? 'up' : 'down'; ?>">
                                    <i class="fas fa-arrow-<?php echo $monthlyTrend >= 0 ? 'up' : 'down'; ?>"></i> <?php echo abs($monthlyTrend); ?>%
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
                                    <div class="stat-card-value"><?php echo number_format($todayCrimes); ?></div>
                                </div>
                            </div>
                            <div class="stat-card-footer">
                                <span class="stat-trend <?php echo $dailyTrend >= 0 ? 'up' : 'down'; ?>">
                                    <i class="fas fa-arrow-<?php echo $dailyTrend >= 0 ? 'up' : 'down'; ?>"></i> <?php echo abs($dailyTrend); ?>%
                                </span>
                                <span style="margin-left: 0.5rem;">vs yesterday</span>
                            </div>
                        </div>

                        <!-- Active Cases Card -->
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-card-icon warning">
                                    <i class="fas fa-search"></i>
                                </div>
                                <div class="stat-card-info">
                                    <div class="stat-card-label">Under Investigation</div>
                                    <div class="stat-card-value"><?php echo number_format($statusCounts['under_investigation']); ?></div>
                                </div>
                            </div>
                            <div class="stat-card-footer">
                                <span style="color: var(--text-secondary-1);">
                                    <?php echo number_format($statusCounts['reported']); ?> Reported
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
                                    <div class="stat-card-value"><?php echo number_format($resolvedCases); ?></div>
                                </div>
                            </div>
                            <div class="stat-card-footer">
                                <span style="color: var(--success-color); font-weight: 600;"><?php echo $resolutionRate; ?>%</span>
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
                            <div class="loading-spinner">
                                <i class="fas fa-spinner fa-spin"></i> Loading statistics...
                            </div>
                        </div>
                    </div>

                    <!-- Latest Incidents Section -->
                    <div class="incidents-section">
                        <div class="section-header">
                            <h2 class="section-title">Latest Recorded Incidents</h2>
                            <a href="crime-mapping.php" class="view-all-btn">
                                View All Incidents <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>

                        <?php if (empty($latestIncidents)): ?>
                            <div class="no-data-message">
                                <i class="fas fa-inbox"></i>
                                <p>No incidents recorded yet.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($latestIncidents as $incident): ?>
                                <div class="incident-card">
                                    <div class="incident-header">
                                        <span class="incident-type <?php echo getStatusClass($incident['status']); ?>">
                                            <i class="fas <?php echo $incident['category_icon'] ?? 'fa-exclamation-circle'; ?>"></i>
                                            <?php echo htmlspecialchars($incident['category_name'] ?? 'Unknown'); ?>
                                        </span>
                                        <span class="incident-time"><?php echo timeAgo($incident['incident_date'] . ' ' . $incident['incident_time']); ?></span>
                                    </div>
                                    <div class="incident-description">
                                        <strong><?php echo htmlspecialchars($incident['incident_title']); ?></strong><br>
                                        <?php echo htmlspecialchars(substr($incident['incident_description'], 0, 200)); ?>
                                        <?php echo strlen($incident['incident_description']) > 200 ? '...' : ''; ?>
                                    </div>
                                    <div class="incident-meta">
                                        <span class="incident-meta-item">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <?php echo htmlspecialchars($incident['barangay_name'] ?? 'Unknown'); ?>, <?php echo htmlspecialchars($incident['district'] ?? ''); ?>
                                        </span>
                                        <span class="incident-meta-item">
                                            <i class="fas fa-fingerprint"></i>
                                            <?php echo htmlspecialchars($incident['incident_code']); ?>
                                        </span>
                                        <span class="incident-meta-item">
                                            <i class="fas <?php echo getStatusIcon($incident['status']); ?>"></i>
                                            <?php echo getStatusLabel($incident['status']); ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- District Performance Metrics -->
                    <div class="incidents-section">
                        <div class="section-header">
                            <h2 class="section-title">Crime Distribution by District</h2>
                        </div>

                        <div class="quick-stats-grid" id="district-stats">
                            <div class="loading-spinner">
                                <i class="fas fa-spinner fa-spin"></i> Loading district data...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include('../includes/admin-footer.php') ?>
    </div>

    <script>
        // Current period state
        let currentPeriod = 'week';

        // Time period tab functionality
        function showPeriod(period) {
            currentPeriod = period;

            // Update active tab
            const tabs = document.querySelectorAll('.time-tab');
            tabs.forEach(tab => {
                tab.classList.remove('active');
                if (tab.textContent.toLowerCase().includes(period)) {
                    tab.classList.add('active');
                }
            });

            // Fetch stats from API
            loadPeriodStats(period);
        }

        // Load statistics for selected period
        async function loadPeriodStats(period) {
            const statsGrid = document.getElementById('period-stats');
            statsGrid.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i> Loading statistics...</div>';

            try {
                const response = await fetch(`../../api/retrieve/crime-statistics.php?period=${period}`);
                const data = await response.json();

                if (data.success) {
                    const stats = data.data;
                    const categories = stats.by_category.slice(0, 6);

                    let html = `
                        <div class="quick-stat-item">
                            <div class="quick-stat-value">${stats.overview.total_crimes.toLocaleString()}</div>
                            <div class="quick-stat-label">Total Incidents</div>
                        </div>
                    `;

                    categories.forEach(cat => {
                        html += `
                            <div class="quick-stat-item">
                                <div class="quick-stat-value">${parseInt(cat.count).toLocaleString()}</div>
                                <div class="quick-stat-label">${cat.category_name || 'Other'}</div>
                            </div>
                        `;
                    });

                    statsGrid.innerHTML = html;
                } else {
                    statsGrid.innerHTML = '<div class="error-message">Failed to load statistics</div>';
                }
            } catch (error) {
                console.error('Error fetching statistics:', error);
                statsGrid.innerHTML = '<div class="error-message">Error loading statistics</div>';
            }
        }

        // Load district statistics
        async function loadDistrictStats() {
            const districtGrid = document.getElementById('district-stats');

            try {
                const response = await fetch('../../api/retrieve/barangays.php?include_stats=true&period=month');
                const data = await response.json();

                if (data.success && data.district_summary) {
                    let html = '';
                    data.district_summary.forEach(district => {
                        html += `
                            <div class="quick-stat-item">
                                <div class="quick-stat-value">${parseInt(district.total_incidents).toLocaleString()}</div>
                                <div class="quick-stat-label">${district.district || 'Unknown'}</div>
                            </div>
                        `;
                    });

                    districtGrid.innerHTML = html || '<div class="no-data-message">No district data available</div>';
                } else {
                    districtGrid.innerHTML = '<div class="error-message">Failed to load district data</div>';
                }
            } catch (error) {
                console.error('Error fetching district stats:', error);
                districtGrid.innerHTML = '<div class="error-message">Error loading district data</div>';
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            showPeriod('week');
            loadDistrictStats();
        });
    </script>

    <style>
        .loading-spinner {
            text-align: center;
            padding: 2rem;
            color: var(--text-secondary-1);
            grid-column: 1 / -1;
        }

        .loading-spinner i {
            margin-right: 0.5rem;
        }

        .error-message, .no-data-message {
            text-align: center;
            padding: 2rem;
            color: var(--text-secondary-1);
            grid-column: 1 / -1;
        }

        .no-data-message i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            display: block;
        }
    </style>
</body>
</html>
