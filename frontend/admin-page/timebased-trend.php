<?php
// Authentication check - must be at the top of every admin page
require_once '../../api/middleware/auth.php';
require_once '../../api/config.php';

// Get hourly distribution
$hourlyQuery = "
    SELECT HOUR(incident_time) as hour, COUNT(*) as count
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

// Get daily distribution
$dailyQuery = "
    SELECT DAYOFWEEK(incident_date) as day_num, DAYNAME(incident_date) as day_name, COUNT(*) as count
    FROM crime_department_crime_incidents
    WHERE incident_date IS NOT NULL
    GROUP BY DAYOFWEEK(incident_date), DAYNAME(incident_date)
    ORDER BY day_num
";
$dailyResult = $mysqli->query($dailyQuery);
$dailyData = [];
while ($row = $dailyResult->fetch_assoc()) {
    $dailyData[] = $row;
}

// Get monthly distribution
$monthlyQuery = "
    SELECT DATE_FORMAT(incident_date, '%Y-%m') as month,
           DATE_FORMAT(incident_date, '%b %Y') as month_label,
           COUNT(*) as count
    FROM crime_department_crime_incidents
    WHERE incident_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
    GROUP BY DATE_FORMAT(incident_date, '%Y-%m')
    ORDER BY month
";
$monthlyResult = $mysqli->query($monthlyQuery);
$monthlyData = [];
while ($row = $monthlyResult->fetch_assoc()) {
    $monthlyData[] = $row;
}

// Calculate insights
$peakHour = array_search(max($hourlyData), $hourlyData);
$lowestHour = array_search(min(array_filter($hourlyData, function($v) { return $v > 0; }) ?: [0]), $hourlyData);
$peakDay = !empty($dailyData) ? array_reduce($dailyData, function($carry, $item) {
    return ($item['count'] > ($carry['count'] ?? 0)) ? $item : $carry;
}, ['count' => 0, 'day_name' => 'N/A']) : ['day_name' => 'N/A', 'count' => 0];
$lowestDay = !empty($dailyData) ? array_reduce($dailyData, function($carry, $item) {
    return ($item['count'] < ($carry['count'] ?? PHP_INT_MAX)) ? $item : $carry;
}, ['count' => PHP_INT_MAX, 'day_name' => 'N/A']) : ['day_name' => 'N/A', 'count' => 0];

// Calculate year-over-year if data exists
$totalThisYear = array_sum(array_column($monthlyData, 'count'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Time-Based Trends | Crime Dep.</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/admin-header.css">
    <link rel="stylesheet" href="../css/hero.css">
    <link rel="stylesheet" href="../css/sidebar-footer.css">
    <link rel="stylesheet" href="../css/timebased-trend.css">
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
                        <li class="breadcrumb-item"><a href="/" class="breadcrumb-link"><span>Home</span></a></li>
                        <li class="breadcrumb-item"><a href="/analytics" class="breadcrumb-link"><span>Analytics</span></a></li>
                        <li class="breadcrumb-item active" aria-current="page"><span>Time-Based Trends</span></li>
                    </ol>
                </nav>
                <h1>Time-Based Trends</h1>
                <p>Analyze crime patterns across different time periods. Track hourly, daily, and monthly trends to identify when crimes usually occur.</p>
            </div>

            <div class="sub-container">
                <div class="page-content">
                    <!-- Hourly Crime Distribution -->
                    <div class="chart-grid-single">
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title">Crime Distribution by Hour (24-Hour Analysis)</h3>
                                <div class="chart-icon"><i class="fas fa-clock"></i></div>
                            </div>
                            <div class="chart-canvas-container">
                                <canvas id="hourlyTrendChart"></canvas>
                            </div>
                            <div class="chart-footer-text">
                                Peak hour: <?php echo sprintf('%02d:00', $peakHour); ?> (<?php echo $hourlyData[$peakHour]; ?> incidents) • Lowest: <?php echo sprintf('%02d:00', $lowestHour); ?> (<?php echo $hourlyData[$lowestHour]; ?> incidents)
                            </div>
                        </div>
                    </div>

                    <!-- Daily Crime Distribution -->
                    <div class="chart-grid-single">
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title">Crime Distribution by Day of Week</h3>
                                <div class="chart-icon"><i class="fas fa-calendar-week"></i></div>
                            </div>
                            <div class="chart-canvas-container">
                                <canvas id="dailyTrendChart"></canvas>
                            </div>
                            <div class="chart-footer-text">
                                Highest crime day: <?php echo $peakDay['day_name']; ?> (<?php echo $peakDay['count']; ?> incidents) • Lowest: <?php echo $lowestDay['day_name']; ?> (<?php echo $lowestDay['count']; ?> incidents)
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Crime Trend -->
                    <div class="chart-grid-single">
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title">Monthly Crime Trend (Last 12 Months)</h3>
                                <div class="chart-icon"><i class="fas fa-chart-line"></i></div>
                            </div>
                            <div class="chart-canvas-container">
                                <canvas id="monthlyTrendChart"></canvas>
                            </div>
                            <div class="chart-footer-text">
                                Total incidents in period: <?php echo number_format($totalThisYear); ?>
                            </div>
                        </div>
                    </div>

                    <!-- Time-Based Insights -->
                    <div class="insights-section">
                        <div class="chart-header">
                            <h3 class="chart-title">Time-Based Insights</h3>
                            <div class="chart-icon"><i class="fas fa-lightbulb"></i></div>
                        </div>
                        <div class="insights-grid">
                            <div class="insight-card">
                                <div class="insight-icon"><i class="fas fa-sun"></i></div>
                                <div class="insight-content">
                                    <h4>Peak Crime Time</h4>
                                    <p class="insight-value"><?php echo sprintf('%02d:00 - %02d:00', $peakHour, ($peakHour + 1) % 24); ?></p>
                                    <p class="insight-description"><?php echo $hourlyData[$peakHour]; ?> incidents recorded during this hour.</p>
                                </div>
                            </div>

                            <div class="insight-card">
                                <div class="insight-icon"><i class="fas fa-moon"></i></div>
                                <div class="insight-content">
                                    <h4>Safest Time</h4>
                                    <p class="insight-value"><?php echo sprintf('%02d:00 - %02d:00', $lowestHour, ($lowestHour + 1) % 24); ?></p>
                                    <p class="insight-description">Lowest crime activity with <?php echo $hourlyData[$lowestHour]; ?> incidents.</p>
                                </div>
                            </div>

                            <div class="insight-card">
                                <div class="insight-icon"><i class="fas fa-calendar-alt"></i></div>
                                <div class="insight-content">
                                    <h4>Most Active Day</h4>
                                    <p class="insight-value"><?php echo $peakDay['day_name']; ?></p>
                                    <p class="insight-description"><?php echo $peakDay['count']; ?> incidents - highest among all days.</p>
                                </div>
                            </div>

                            <div class="insight-card">
                                <div class="insight-icon"><i class="fas fa-chart-line"></i></div>
                                <div class="insight-content">
                                    <h4>Total Recorded</h4>
                                    <p class="insight-value"><?php echo number_format($totalThisYear); ?></p>
                                    <p class="insight-description">Incidents in the last 12 months.</p>
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
        const hourlyData = <?php echo json_encode($hourlyData); ?>;
        const dailyLabels = <?php echo json_encode(array_column($dailyData, 'day_name')); ?>;
        const dailyValues = <?php echo json_encode(array_map('intval', array_column($dailyData, 'count'))); ?>;
        const monthlyLabels = <?php echo json_encode(array_column($monthlyData, 'month_label')); ?>;
        const monthlyValues = <?php echo json_encode(array_map('intval', array_column($monthlyData, 'count'))); ?>;

        let hourlyTrendChart, dailyTrendChart, monthlyTrendChart;

        function getThemeColors() {
            const getComputedColor = (variable) => getComputedStyle(document.documentElement).getPropertyValue(variable).trim() || null;
            return {
                primaryColor: getComputedColor('--primary-color-1') || '#4c8a89',
                textColor: getComputedColor('--text-color-1') || '#171717',
                gridColor: getComputedColor('--border-color-1') || '#e5e5e5',
                cardBg: getComputedColor('--card-bg-1') || '#ffffff',
                errorColor: getComputedColor('--error-color') || '#dc2626',
                successColor: getComputedColor('--success-color') || '#10b981'
            };
        }

        function hexToRgba(hex, alpha) {
            if (!hex) return `rgba(0,0,0,${alpha})`;
            hex = hex.replace('#', '');
            return `rgba(${parseInt(hex.slice(0,2),16)}, ${parseInt(hex.slice(2,4),16)}, ${parseInt(hex.slice(4,6),16)}, ${alpha})`;
        }

        function initializeCharts() {
            const colors = getThemeColors();
            Chart.defaults.color = colors.textColor;
            Chart.defaults.borderColor = colors.gridColor;

            if (hourlyTrendChart) hourlyTrendChart.destroy();
            if (dailyTrendChart) dailyTrendChart.destroy();
            if (monthlyTrendChart) monthlyTrendChart.destroy();

            const hourLabels = ['12AM','1AM','2AM','3AM','4AM','5AM','6AM','7AM','8AM','9AM','10AM','11AM',
                               '12PM','1PM','2PM','3PM','4PM','5PM','6PM','7PM','8PM','9PM','10PM','11PM'];

            hourlyTrendChart = new Chart(document.getElementById('hourlyTrendChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: hourLabels,
                    datasets: [{
                        label: 'Crime Incidents',
                        data: hourlyData,
                        borderColor: colors.errorColor,
                        backgroundColor: hexToRgba(colors.errorColor, 0.1),
                        borderWidth: 3, fill: true, tension: 0.4,
                        pointRadius: 3, pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: true, position: 'top' } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: colors.gridColor } },
                        x: { grid: { display: false } }
                    }
                }
            });

            const maxDaily = Math.max(...dailyValues);
            const dailyColors = dailyValues.map(v => v === maxDaily ? hexToRgba(colors.errorColor, 0.8) : hexToRgba(colors.primaryColor, 0.8));

            dailyTrendChart = new Chart(document.getElementById('dailyTrendChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: dailyLabels.length > 0 ? dailyLabels : ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
                    datasets: [{
                        label: 'Incidents',
                        data: dailyValues.length > 0 ? dailyValues : [0,0,0,0,0,0,0],
                        backgroundColor: dailyColors,
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: colors.gridColor } },
                        x: { grid: { display: false } }
                    }
                }
            });

            monthlyTrendChart = new Chart(document.getElementById('monthlyTrendChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: monthlyLabels.length > 0 ? monthlyLabels : ['No Data'],
                    datasets: [{
                        label: 'Monthly Incidents',
                        data: monthlyValues.length > 0 ? monthlyValues : [0],
                        borderColor: colors.primaryColor,
                        backgroundColor: hexToRgba(colors.primaryColor, 0.1),
                        borderWidth: 3, fill: true, tension: 0.4,
                        pointRadius: 5, pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: true, position: 'top' } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: colors.gridColor } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', initializeCharts);

        const observer = new MutationObserver(() => setTimeout(initializeCharts, 50));
        observer.observe(document.documentElement, { attributes: true });
    </script>
</body>
</html>
