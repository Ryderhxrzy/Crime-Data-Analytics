<?php
// Authentication check - must be at the top of every admin page
require_once '../../api/middleware/auth.php';
require_once '../../api/config.php';

// Get total crimes
$totalQuery = "SELECT COUNT(*) as total FROM crime_department_crime_incidents";
$totalResult = $mysqli->query($totalQuery);
$totalCrimes = $totalResult->fetch_assoc()['total'];

// Get crime categories with counts
$categoriesQuery = "
    SELECT
        cc.id,
        cc.category_name,
        cc.color_code as color,
        cc.icon,
        COUNT(ci.id) as count
    FROM crime_department_crime_categories cc
    LEFT JOIN crime_department_crime_incidents ci ON cc.id = ci.crime_category_id
    WHERE cc.is_active = 1
    GROUP BY cc.id
    ORDER BY count DESC
";
$categoriesResult = $mysqli->query($categoriesQuery);
$categories = [];
while ($row = $categoriesResult->fetch_assoc()) {
    $categories[] = $row;
}

// Get top 10 barangays with highest crime counts
$barangaysQuery = "
    SELECT
        b.id,
        b.barangay_name,
        b.city_municipality as district,
        COUNT(ci.id) as count
    FROM crime_department_barangays b
    LEFT JOIN crime_department_crime_incidents ci ON b.id = ci.barangay_id
    WHERE b.is_active = 1
    GROUP BY b.id
    HAVING count > 0
    ORDER BY count DESC
    LIMIT 10
";
$barangaysResult = $mysqli->query($barangaysQuery);
$topBarangays = [];
while ($row = $barangaysResult->fetch_assoc()) {
    $topBarangays[] = $row;
}

// Get monthly trend data (last 6 months)
$monthlyQuery = "
    SELECT
        DATE_FORMAT(incident_date, '%Y-%m') as month,
        DATE_FORMAT(incident_date, '%M') as month_name,
        COUNT(*) as count
    FROM crime_department_crime_incidents
    WHERE incident_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(incident_date, '%Y-%m')
    ORDER BY month ASC
";
$monthlyResult = $mysqli->query($monthlyQuery);
$monthlyData = [];
while ($row = $monthlyResult->fetch_assoc()) {
    $monthlyData[] = $row;
}

// Get daily distribution (day of week)
$dailyQuery = "
    SELECT
        DAYOFWEEK(incident_date) as day_num,
        DAYNAME(incident_date) as day_name,
        COUNT(*) as count
    FROM crime_department_crime_incidents
    WHERE incident_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    GROUP BY DAYOFWEEK(incident_date), DAYNAME(incident_date)
    ORDER BY day_num
";
$dailyResult = $mysqli->query($dailyQuery);
$dailyData = [];
while ($row = $dailyResult->fetch_assoc()) {
    $dailyData[] = $row;
}

// Get hourly distribution
$hourlyQuery = "
    SELECT
        HOUR(incident_time) as hour,
        COUNT(*) as count
    FROM crime_department_crime_incidents
    WHERE incident_time IS NOT NULL
    GROUP BY HOUR(incident_time)
    ORDER BY hour
";
$hourlyResult = $mysqli->query($hourlyQuery);
$hourlyData = array_fill(0, 24, 0);
while ($row = $hourlyResult->fetch_assoc()) {
    $hourlyData[(int)$row['hour']] = (int)$row['count'];
}

// Get top 3 crime categories
$top3Categories = array_slice($categories, 0, 3);

// Get district summary for risk areas
$districtQuery = "
    SELECT
        b.city_municipality as district,
        COUNT(ci.id) as count,
        COUNT(CASE WHEN ci.incident_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN 1 END) as recent_count
    FROM crime_department_barangays b
    LEFT JOIN crime_department_crime_incidents ci ON b.id = ci.barangay_id
    WHERE b.is_active = 1 AND b.city_municipality IS NOT NULL
    GROUP BY b.city_municipality
    HAVING count > 0
    ORDER BY count DESC
    LIMIT 6
";
$districtResult = $mysqli->query($districtQuery);
$districtData = [];
while ($row = $districtResult->fetch_assoc()) {
    $districtData[] = $row;
}

// Calculate peak month
$peakMonth = !empty($monthlyData) ? max(array_column($monthlyData, 'count')) : 0;
$peakMonthName = '';
foreach ($monthlyData as $m) {
    if ((int)$m['count'] === $peakMonth) {
        $peakMonthName = $m['month_name'];
        break;
    }
}

// Calculate trend percentage
$firstMonth = !empty($monthlyData) ? (int)$monthlyData[0]['count'] : 0;
$lastMonth = !empty($monthlyData) ? (int)$monthlyData[count($monthlyData)-1]['count'] : 0;
$trendPercent = $firstMonth > 0 ? round((($lastMonth - $firstMonth) / $firstMonth) * 100, 1) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Summary | Crime Dep.</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/admin-header.css">
    <link rel="stylesheet" href="../css/hero.css">
    <link rel="stylesheet" href="../css/sidebar-footer.css">
    <link rel="stylesheet" href="../css/analytics-summary.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                            <span>Analytics Summary</span>
                        </li>
                    </ol>
                </nav>
                <h1>Analytics Summary</h1>
                <p>Comprehensive crime analytics and statistical insights. Track trends, identify patterns, and analyze crime distribution across districts and barangays.</p>
            </div>

            <div class="sub-container">
                <div class="page-content">
                    <!-- Crime Trend Charts -->
                    <div class="chart-grid-single">
                        <!-- Monthly Crime Trend -->
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title">Monthly Crime Trend (Last 6 Months)</h3>
                                <div class="chart-icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                            </div>
                            <div class="chart-canvas-container">
                                <canvas id="monthlyTrendChart"></canvas>
                            </div>
                            <div class="chart-footer-text">
                                <?php if ($peakMonthName): ?>
                                    Peak in <?php echo $peakMonthName; ?> with <?php echo number_format($peakMonth); ?> incidents
                                    <?php if ($trendPercent != 0): ?>
                                        • Overall <?php echo $trendPercent > 0 ? 'upward' : 'downward'; ?> trend of <?php echo abs($trendPercent); ?>%
                                    <?php endif; ?>
                                <?php else: ?>
                                    No data available for the selected period
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="chart-grid-double">
                        <!-- Crime Type Distribution Pie Chart -->
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title">Crime Type Distribution</h3>
                                <div class="chart-icon">
                                    <i class="fas fa-chart-pie"></i>
                                </div>
                            </div>
                            <div class="chart-canvas-container medium">
                                <canvas id="crimeTypePieChart"></canvas>
                            </div>
                            <div class="chart-footer-text">
                                Total of <?php echo number_format($totalCrimes); ?> crimes recorded
                            </div>
                        </div>

                        <!-- Weekly Crime Distribution -->
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title">Weekly Distribution</h3>
                                <div class="chart-icon">
                                    <i class="fas fa-chart-bar"></i>
                                </div>
                            </div>
                            <div class="chart-canvas-container medium">
                                <canvas id="weeklyBarChart"></canvas>
                            </div>
                            <div class="chart-footer-text">
                                Last 30 days crime distribution by day of week
                            </div>
                        </div>
                    </div>

                    <div class="chart-grid-single">
                        <!-- Hourly Crime Pattern -->
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title">Peak Crime Hours (24-Hour Analysis)</h3>
                                <div class="chart-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                            <div class="chart-canvas-container medium">
                                <canvas id="hourlyLineChart"></canvas>
                            </div>
                            <div class="chart-footer-text">
                                Crime distribution by hour of day
                            </div>
                        </div>
                    </div>

                    <!-- Crime Trends Section -->
                    <div class="trend-section">
                        <div class="chart-header">
                            <h3 class="chart-title">Top Crime Categories</h3>
                            <div class="chart-icon">
                                <i class="fas fa-chart-area"></i>
                            </div>
                        </div>
                        <div class="trend-indicators">
                            <?php foreach (array_slice($categories, 0, 4) as $cat): ?>
                            <div class="trend-item">
                                <div class="trend-icon-wrapper" style="background-color: <?php echo $cat['color'] ?? '#4c8a89'; ?>20; color: <?php echo $cat['color'] ?? '#4c8a89'; ?>;">
                                    <i class="fas <?php echo $cat['icon'] ?? 'fa-exclamation-circle'; ?>"></i>
                                </div>
                                <div class="trend-content">
                                    <div class="trend-label"><?php echo htmlspecialchars($cat['category_name']); ?></div>
                                    <div class="trend-value">
                                        <?php echo number_format($cat['count']); ?>
                                        <span class="trend-change">
                                            <?php
                                            $percentage = $totalCrimes > 0 ? round(($cat['count'] / $totalCrimes) * 100, 1) : 0;
                                            echo $percentage . '%';
                                            ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Top 3 Crime Types -->
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3 class="chart-title">Top 3 Most Common Crime Types</h3>
                            <div class="chart-icon">
                                <i class="fas fa-trophy"></i>
                            </div>
                        </div>
                        <div class="crime-types-list">
                            <?php foreach ($top3Categories as $index => $cat): ?>
                            <div class="crime-type-item">
                                <div class="crime-rank rank-<?php echo $index + 1; ?>">
                                    <i class="fas fa-medal"></i>
                                </div>
                                <div class="crime-type-info">
                                    <div class="crime-type-name"><?php echo htmlspecialchars($cat['category_name']); ?></div>
                                    <div class="crime-type-description">
                                        <i class="fas <?php echo $cat['icon'] ?? 'fa-exclamation-circle'; ?>"></i>
                                        Crime category from integrated systems
                                    </div>
                                </div>
                                <div class="crime-type-stats">
                                    <div class="crime-count"><?php echo number_format($cat['count']); ?></div>
                                    <div class="crime-percentage">
                                        <?php echo $totalCrimes > 0 ? round(($cat['count'] / $totalCrimes) * 100, 1) : 0; ?>% of total
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Barangays with Highest Crime Count -->
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3 class="chart-title">Barangays with Highest Crime Count</h3>
                            <div class="chart-icon">
                                <i class="fas fa-map-marked-alt"></i>
                            </div>
                        </div>
                        <div class="barangay-list">
                            <?php foreach ($topBarangays as $index => $brgy): ?>
                            <div class="barangay-item">
                                <div class="barangay-position"><?php echo $index + 1; ?></div>
                                <div class="barangay-details">
                                    <div class="barangay-name"><?php echo htmlspecialchars($brgy['barangay_name']); ?></div>
                                    <div class="barangay-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <?php echo htmlspecialchars($brgy['district'] ?? 'Quezon City'); ?>
                                    </div>
                                </div>
                                <div class="barangay-count"><?php echo number_format($brgy['count']); ?></div>
                            </div>
                            <?php endforeach; ?>

                            <?php if (empty($topBarangays)): ?>
                            <div class="no-data-message">
                                <i class="fas fa-inbox"></i>
                                <p>No barangay crime data available</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Current High-Risk Areas by District -->
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3 class="chart-title">Crime Distribution by District</h3>
                            <div class="chart-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                        </div>
                        <div class="risk-areas-grid">
                            <?php foreach ($districtData as $index => $district):
                                $riskLevel = 'low';
                                $riskClass = 'low-risk';
                                if ($district['count'] > 20) {
                                    $riskLevel = 'high';
                                    $riskClass = 'high-risk';
                                } elseif ($district['count'] > 10) {
                                    $riskLevel = 'medium';
                                    $riskClass = 'medium-risk';
                                }
                            ?>
                            <div class="risk-area-card <?php echo $riskClass; ?>">
                                <div class="risk-area-header">
                                    <div class="risk-area-name"><?php echo htmlspecialchars($district['district']); ?></div>
                                    <span class="risk-badge <?php echo $riskLevel; ?>"><?php echo ucfirst($riskLevel); ?></span>
                                </div>
                                <div class="risk-area-stats">
                                    <div class="risk-stat">
                                        <div class="risk-stat-value"><?php echo number_format($district['count']); ?></div>
                                        <div class="risk-stat-label">Total Incidents</div>
                                    </div>
                                    <div class="risk-stat">
                                        <div class="risk-stat-value"><?php echo number_format($district['recent_count']); ?></div>
                                        <div class="risk-stat-label">Last 7 Days</div>
                                    </div>
                                </div>
                                <div class="risk-area-factors">
                                    <i class="fas fa-info-circle"></i>
                                    <?php echo $totalCrimes > 0 ? round(($district['count'] / $totalCrimes) * 100, 1) : 0; ?>% of total incidents
                                </div>
                            </div>
                            <?php endforeach; ?>

                            <?php if (empty($districtData)): ?>
                            <div class="no-data-message" style="grid-column: 1 / -1;">
                                <i class="fas fa-inbox"></i>
                                <p>No district data available</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include('../includes/admin-footer.php') ?>
    </div>

    <script>
        // Chart data from PHP
        const monthlyLabels = <?php echo json_encode(array_column($monthlyData, 'month_name')); ?>;
        const monthlyValues = <?php echo json_encode(array_map('intval', array_column($monthlyData, 'count'))); ?>;

        const categoryLabels = <?php echo json_encode(array_slice(array_column($categories, 'category_name'), 0, 7)); ?>;
        const categoryValues = <?php echo json_encode(array_map('intval', array_slice(array_column($categories, 'count'), 0, 7))); ?>;
        const categoryColors = <?php echo json_encode(array_slice(array_map(function($c) { return $c['color'] ?? '#6b7280'; }, $categories), 0, 7)); ?>;

        const dailyLabels = <?php echo json_encode(array_column($dailyData, 'day_name')); ?>;
        const dailyValues = <?php echo json_encode(array_map('intval', array_column($dailyData, 'count'))); ?>;

        const hourlyValues = <?php echo json_encode($hourlyData); ?>;

        // Store chart instances globally
        let monthlyTrendChart, crimeTypePieChart, weeklyBarChart, hourlyLineChart;

        // Function to get current theme colors
        function getThemeColors() {
            const getComputedColor = (variable) => {
                const value = getComputedStyle(document.documentElement).getPropertyValue(variable).trim();
                return value || null;
            };

            return {
                primaryColor: getComputedColor('--primary-color-1') || '#4c8a89',
                textColor: getComputedColor('--text-color-1') || '#171717',
                textSecondary: getComputedColor('--text-secondary-1') || '#575757',
                gridColor: getComputedColor('--border-color-1') || '#e5e5e5',
                cardBg: getComputedColor('--card-bg-1') || '#ffffff',
                errorColor: getComputedColor('--error-color') || '#dc2626',
                successColor: getComputedColor('--success-color') || '#10b981',
                warningColor: getComputedColor('--warning-color') || '#f59e0b'
            };
        }

        // Convert hex to rgba
        function hexToRgba(hex, alpha) {
            if (!hex) return `rgba(0,0,0,${alpha})`;
            hex = hex.replace('#', '');
            const r = parseInt(hex.slice(0, 2), 16);
            const g = parseInt(hex.slice(2, 4), 16);
            const b = parseInt(hex.slice(4, 6), 16);
            return `rgba(${r}, ${g}, ${b}, ${alpha})`;
        }

        // Function to initialize/update all charts
        function initializeCharts() {
            const colors = getThemeColors();

            // Chart.js default configuration
            Chart.defaults.color = colors.textColor;
            Chart.defaults.borderColor = colors.gridColor;
            Chart.defaults.font.family = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';

            // Destroy existing charts
            if (monthlyTrendChart) monthlyTrendChart.destroy();
            if (crimeTypePieChart) crimeTypePieChart.destroy();
            if (weeklyBarChart) weeklyBarChart.destroy();
            if (hourlyLineChart) hourlyLineChart.destroy();

            // 1. Monthly Trend Line Chart
            const monthlyCtx = document.getElementById('monthlyTrendChart').getContext('2d');
            monthlyTrendChart = new Chart(monthlyCtx, {
                type: 'line',
                data: {
                    labels: monthlyLabels.length > 0 ? monthlyLabels : ['No Data'],
                    datasets: [{
                        label: 'Crime Incidents',
                        data: monthlyValues.length > 0 ? monthlyValues : [0],
                        borderColor: colors.primaryColor,
                        backgroundColor: hexToRgba(colors.primaryColor, 0.1),
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 6,
                        pointHoverRadius: 8,
                        pointBackgroundColor: colors.primaryColor,
                        pointBorderColor: colors.cardBg,
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: true, position: 'top', labels: { color: colors.textColor, font: { size: 14, weight: '600' }, padding: 15 } },
                        tooltip: { backgroundColor: 'rgba(0, 0, 0, 0.8)', padding: 12 }
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { color: colors.textColor }, grid: { color: colors.gridColor } },
                        x: { ticks: { color: colors.textColor }, grid: { display: false } }
                    }
                }
            });

            // 2. Crime Type Pie Chart
            const pieCtx = document.getElementById('crimeTypePieChart').getContext('2d');
            crimeTypePieChart = new Chart(pieCtx, {
                type: 'doughnut',
                data: {
                    labels: categoryLabels.length > 0 ? categoryLabels : ['No Data'],
                    datasets: [{
                        data: categoryValues.length > 0 ? categoryValues : [1],
                        backgroundColor: categoryColors.length > 0 ? categoryColors : ['#6b7280'],
                        borderWidth: 2,
                        borderColor: colors.cardBg,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'right', labels: { color: colors.textColor, font: { size: 12 }, padding: 10 } },
                        tooltip: { backgroundColor: 'rgba(0, 0, 0, 0.8)', padding: 12 }
                    }
                }
            });

            // 3. Weekly Bar Chart
            const weeklyCtx = document.getElementById('weeklyBarChart').getContext('2d');
            const maxDailyValue = Math.max(...dailyValues, 1);
            const dailyBackgroundColors = dailyValues.map(v => v === maxDailyValue ? hexToRgba(colors.errorColor, 0.8) : hexToRgba(colors.primaryColor, 0.8));
            const dailyBorderColors = dailyValues.map(v => v === maxDailyValue ? colors.errorColor : colors.primaryColor);

            weeklyBarChart = new Chart(weeklyCtx, {
                type: 'bar',
                data: {
                    labels: dailyLabels.length > 0 ? dailyLabels : ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    datasets: [{
                        label: 'Incidents',
                        data: dailyValues.length > 0 ? dailyValues : [0, 0, 0, 0, 0, 0, 0],
                        backgroundColor: dailyBackgroundColors,
                        borderColor: dailyBorderColors,
                        borderWidth: 2,
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false }, tooltip: { backgroundColor: 'rgba(0, 0, 0, 0.8)', padding: 12 } },
                    scales: {
                        y: { beginAtZero: true, ticks: { color: colors.textColor }, grid: { color: colors.gridColor } },
                        x: { ticks: { color: colors.textColor }, grid: { display: false } }
                    }
                }
            });

            // 4. Hourly Line Chart
            const hourlyCtx = document.getElementById('hourlyLineChart').getContext('2d');
            const hourlyLabels = ['12AM', '1AM', '2AM', '3AM', '4AM', '5AM', '6AM', '7AM', '8AM', '9AM', '10AM', '11AM',
                                  '12PM', '1PM', '2PM', '3PM', '4PM', '5PM', '6PM', '7PM', '8PM', '9PM', '10PM', '11PM'];

            hourlyLineChart = new Chart(hourlyCtx, {
                type: 'line',
                data: {
                    labels: hourlyLabels,
                    datasets: [{
                        label: 'Incidents',
                        data: hourlyValues,
                        borderColor: colors.errorColor,
                        backgroundColor: hexToRgba(colors.errorColor, 0.1),
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 3,
                        pointHoverRadius: 6,
                        pointBackgroundColor: colors.errorColor
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: true, position: 'top', labels: { color: colors.textColor } }, tooltip: { backgroundColor: 'rgba(0, 0, 0, 0.8)', padding: 12 } },
                    scales: {
                        y: { beginAtZero: true, ticks: { color: colors.textColor }, grid: { color: colors.gridColor } },
                        x: { ticks: { color: colors.textColor, maxRotation: 45 }, grid: { display: false } }
                    }
                }
            });
        }

        // Initialize charts on page load
        document.addEventListener('DOMContentLoaded', initializeCharts);

        // Theme change observer
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'class' || mutation.attributeName === 'data-theme') {
                    setTimeout(initializeCharts, 50);
                }
            });
        });
        observer.observe(document.documentElement, { attributes: true });
    </script>

    <style>
        .no-data-message {
            text-align: center;
            padding: 2rem;
            color: var(--text-secondary-1);
        }
        .no-data-message i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            display: block;
        }
        .trend-icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }
    </style>
</body>
</html>
