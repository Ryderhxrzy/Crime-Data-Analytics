<?php
require_once '../../api/middleware/auth.php';
require_once '../../api/config.php';

// Get total incidents
$totalQuery = "SELECT COUNT(*) as total FROM crime_department_crime_incidents";
$totalResult = $mysqli->query($totalQuery);
$totalIncidents = $totalResult->fetch_assoc()['total'];

// Get this month's incidents
$thisMonthQuery = "SELECT COUNT(*) as total FROM crime_department_crime_incidents WHERE incident_date >= DATE_FORMAT(CURDATE(), '%Y-%m-01')";
$thisMonthResult = $mysqli->query($thisMonthQuery);
$thisMonthIncidents = $thisMonthResult->fetch_assoc()['total'];

// Get last month's incidents
$lastMonthQuery = "SELECT COUNT(*) as total FROM crime_department_crime_incidents WHERE incident_date BETWEEN DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-01') AND LAST_DAY(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))";
$lastMonthResult = $mysqli->query($lastMonthQuery);
$lastMonthIncidents = $lastMonthResult->fetch_assoc()['total'];

// Calculate trend
$monthlyTrend = $lastMonthIncidents > 0 ? round((($thisMonthIncidents - $lastMonthIncidents) / $lastMonthIncidents) * 100, 1) : 0;

// Get crime rate by barangay (per 1000 population)
$barangayRatesQuery = "
    SELECT b.barangay_name, b.district, b.population, COUNT(ci.id) as incidents,
           ROUND(COUNT(ci.id) * 1000 / NULLIF(b.population, 0), 2) as crime_rate
    FROM crime_department_barangays b
    LEFT JOIN crime_department_crime_incidents ci ON b.id = ci.barangay_id
    WHERE b.is_active = 1 AND b.population > 0
    GROUP BY b.id
    HAVING incidents > 0
    ORDER BY crime_rate DESC
    LIMIT 15
";
$barangayRatesResult = $mysqli->query($barangayRatesQuery);
$barangayRates = [];
while ($row = $barangayRatesResult->fetch_assoc()) {
    $barangayRates[] = $row;
}

// Get monthly crime rates for chart
$monthlyRatesQuery = "
    SELECT DATE_FORMAT(incident_date, '%b %Y') as month_label,
           DATE_FORMAT(incident_date, '%Y-%m') as month,
           COUNT(*) as count
    FROM crime_department_crime_incidents
    WHERE incident_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
    GROUP BY DATE_FORMAT(incident_date, '%Y-%m')
    ORDER BY month
";
$monthlyRatesResult = $mysqli->query($monthlyRatesQuery);
$monthlyRates = [];
while ($row = $monthlyRatesResult->fetch_assoc()) {
    $monthlyRates[] = $row;
}

// Get crime rates by category
$categoryRatesQuery = "
    SELECT cc.category_name, cc.color, COUNT(ci.id) as count
    FROM crime_department_crime_categories cc
    LEFT JOIN crime_department_crime_incidents ci ON cc.id = ci.crime_category_id
    WHERE cc.is_active = 1
    GROUP BY cc.id
    HAVING count > 0
    ORDER BY count DESC
    LIMIT 8
";
$categoryRatesResult = $mysqli->query($categoryRatesQuery);
$categoryRates = [];
while ($row = $categoryRatesResult->fetch_assoc()) {
    $categoryRates[] = $row;
}

// Average crime rate
$avgRate = count($barangayRates) > 0 ? round(array_sum(array_column($barangayRates, 'crime_rate')) / count($barangayRates), 2) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crime Rate Metrics | Crime Dep.</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/admin-header.css">
    <link rel="stylesheet" href="../css/hero.css">
    <link rel="stylesheet" href="../css/sidebar-footer.css">
    <link rel="stylesheet" href="../css/crime-mapping.css">
    <link rel="icon" type="image/x-icon" href="../image/favicon.ico">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                        <li class="breadcrumb-item"><a href="/metrics" class="breadcrumb-link"><span>Key Metrics</span></a></li>
                        <li class="breadcrumb-item active" aria-current="page"><span>Crime Rates</span></li>
                    </ol>
                </nav>
                <h1>Crime Rate Metrics</h1>
                <p>Analyze crime rates per barangay and across different time periods. Track trends and identify areas with high crime concentration.</p>
            </div>

            <div class="sub-container">
                <div class="page-content">
                    <!-- Stats Cards -->
                    <div class="dashboard-grid">
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-card-icon primary"><i class="fas fa-chart-line"></i></div>
                                <div class="stat-card-info">
                                    <div class="stat-card-label">Total Incidents</div>
                                    <div class="stat-card-value"><?php echo number_format($totalIncidents); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-card-icon <?php echo $monthlyTrend >= 0 ? 'danger' : 'success'; ?>">
                                    <i class="fas fa-arrow-<?php echo $monthlyTrend >= 0 ? 'up' : 'down'; ?>"></i>
                                </div>
                                <div class="stat-card-info">
                                    <div class="stat-card-label">This Month</div>
                                    <div class="stat-card-value"><?php echo number_format($thisMonthIncidents); ?></div>
                                </div>
                            </div>
                            <div class="stat-card-footer">
                                <span class="stat-trend <?php echo $monthlyTrend >= 0 ? 'up' : 'down'; ?>">
                                    <?php echo $monthlyTrend >= 0 ? '+' : ''; ?><?php echo $monthlyTrend; ?>%
                                </span> vs last month
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-card-icon warning"><i class="fas fa-users"></i></div>
                                <div class="stat-card-info">
                                    <div class="stat-card-label">Avg. Rate/1000</div>
                                    <div class="stat-card-value"><?php echo $avgRate; ?></div>
                                </div>
                            </div>
                            <div class="stat-card-footer">Per population</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-card-icon success"><i class="fas fa-map-marker-alt"></i></div>
                                <div class="stat-card-info">
                                    <div class="stat-card-label">Barangays</div>
                                    <div class="stat-card-value"><?php echo count($barangayRates); ?></div>
                                </div>
                            </div>
                            <div class="stat-card-footer">With recorded incidents</div>
                        </div>
                    </div>

                    <!-- Charts -->
                    <div class="chart-grid-single">
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title">Monthly Crime Rate Trend</h3>
                                <div class="chart-icon"><i class="fas fa-chart-area"></i></div>
                            </div>
                            <div class="chart-canvas-container">
                                <canvas id="monthlyRateChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="chart-grid-double">
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title">Crime Rates by Barangay (per 1000)</h3>
                                <div class="chart-icon"><i class="fas fa-chart-bar"></i></div>
                            </div>
                            <div class="chart-canvas-container" style="height: 350px;">
                                <canvas id="barangayRateChart"></canvas>
                            </div>
                        </div>
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title">Crime by Category</h3>
                                <div class="chart-icon"><i class="fas fa-chart-pie"></i></div>
                            </div>
                            <div class="chart-canvas-container medium">
                                <canvas id="categoryChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Barangay Rates Table -->
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3 class="chart-title">Barangay Crime Rate Rankings</h3>
                            <div class="chart-icon"><i class="fas fa-list-ol"></i></div>
                        </div>
                        <div class="barangay-list">
                            <?php foreach ($barangayRates as $index => $brgy): ?>
                            <div class="barangay-item">
                                <div class="barangay-position"><?php echo $index + 1; ?></div>
                                <div class="barangay-details">
                                    <div class="barangay-name"><?php echo htmlspecialchars($brgy['barangay_name']); ?></div>
                                    <div class="barangay-location">
                                        <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($brgy['district'] ?? 'QC'); ?>
                                        | Pop: <?php echo number_format($brgy['population']); ?>
                                    </div>
                                </div>
                                <div style="text-align: right;">
                                    <div class="barangay-count"><?php echo $brgy['crime_rate']; ?></div>
                                    <small style="color: var(--text-secondary-1);">per 1000</small>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include('../includes/admin-footer.php') ?>
    </div>

    <script>
        const monthlyLabels = <?php echo json_encode(array_column($monthlyRates, 'month_label')); ?>;
        const monthlyValues = <?php echo json_encode(array_map('intval', array_column($monthlyRates, 'count'))); ?>;
        const barangayLabels = <?php echo json_encode(array_slice(array_column($barangayRates, 'barangay_name'), 0, 10)); ?>;
        const barangayValues = <?php echo json_encode(array_slice(array_map('floatval', array_column($barangayRates, 'crime_rate')), 0, 10)); ?>;
        const categoryLabels = <?php echo json_encode(array_column($categoryRates, 'category_name')); ?>;
        const categoryValues = <?php echo json_encode(array_map('intval', array_column($categoryRates, 'count'))); ?>;
        const categoryColors = <?php echo json_encode(array_map(fn($c) => $c['color'] ?? '#6b7280', $categoryRates)); ?>;

        let monthlyChart, barangayChart, categoryChart;

        function getThemeColors() {
            const getColor = (v) => getComputedStyle(document.documentElement).getPropertyValue(v).trim() || null;
            return {
                primaryColor: getColor('--primary-color-1') || '#4c8a89',
                textColor: getColor('--text-color-1') || '#171717',
                gridColor: getColor('--border-color-1') || '#e5e5e5',
                errorColor: getColor('--error-color') || '#dc2626'
            };
        }

        function hexToRgba(hex, alpha) {
            if (!hex) return `rgba(0,0,0,${alpha})`;
            hex = hex.replace('#', '');
            return `rgba(${parseInt(hex.slice(0,2),16)}, ${parseInt(hex.slice(2,4),16)}, ${parseInt(hex.slice(4,6),16)}, ${alpha})`;
        }

        function initializeCharts() {
            const colors = getThemeColors();

            if (monthlyChart) monthlyChart.destroy();
            if (barangayChart) barangayChart.destroy();
            if (categoryChart) categoryChart.destroy();

            monthlyChart = new Chart(document.getElementById('monthlyRateChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: monthlyLabels,
                    datasets: [{
                        label: 'Monthly Incidents',
                        data: monthlyValues,
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

            barangayChart = new Chart(document.getElementById('barangayRateChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: barangayLabels,
                    datasets: [{
                        label: 'Rate per 1000',
                        data: barangayValues,
                        backgroundColor: hexToRgba(colors.errorColor, 0.8),
                        borderRadius: 4
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { beginAtZero: true, grid: { color: colors.gridColor } },
                        y: { grid: { display: false }, ticks: { color: colors.textColor } }
                    }
                }
            });

            categoryChart = new Chart(document.getElementById('categoryChart').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: categoryLabels,
                    datasets: [{ data: categoryValues, backgroundColor: categoryColors, borderWidth: 2, borderColor: '#fff' }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { position: 'right', labels: { color: colors.textColor } } }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', initializeCharts);
        const observer = new MutationObserver(() => setTimeout(initializeCharts, 50));
        observer.observe(document.documentElement, { attributes: true });
    </script>
</body>
</html>
