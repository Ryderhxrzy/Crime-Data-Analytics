<?php
require_once '../../api/middleware/auth.php';
require_once '../../api/config.php';

// Get total incidents
$totalQuery = "SELECT COUNT(*) as total FROM crime_department_crime_incidents";
$totalResult = $mysqli->query($totalQuery);
$totalIncidents = $totalResult->fetch_assoc()['total'];

// Get clearance counts
$clearedQuery = "SELECT COUNT(*) as total FROM crime_department_crime_incidents WHERE clearance_status = 'cleared'";
$clearedResult = $mysqli->query($clearedQuery);
$clearedCases = $clearedResult->fetch_assoc()['total'];

$unclearedQuery = "SELECT COUNT(*) as total FROM crime_department_crime_incidents WHERE clearance_status = 'uncleared'";
$unclearedResult = $mysqli->query($unclearedQuery);
$unclearedCases = $unclearedResult->fetch_assoc()['total'];

// Calculate clearance rate
$clearanceRate = $totalIncidents > 0 ? round(($clearedCases / $totalIncidents) * 100, 1) : 0;

// Get clearance by category
$categoryQuery = "
    SELECT cc.category_name, cc.color_code as color,
           COUNT(ci.id) as total,
           SUM(CASE WHEN ci.clearance_status = 'cleared' THEN 1 ELSE 0 END) as cleared,
           ROUND(SUM(CASE WHEN ci.clearance_status = 'cleared' THEN 1 ELSE 0 END) * 100.0 / NULLIF(COUNT(ci.id), 0), 1) as rate
    FROM crime_department_crime_categories cc
    LEFT JOIN crime_department_crime_incidents ci ON cc.id = ci.crime_category_id
    WHERE cc.is_active = 1
    GROUP BY cc.id
    HAVING total > 0
    ORDER BY rate DESC
";
$categoryResult = $mysqli->query($categoryQuery);
$categoryData = [];
while ($row = $categoryResult->fetch_assoc()) {
    $categoryData[] = $row;
}

// Get clearance by barangay (top 15)
$barangayQuery = "
    SELECT b.barangay_name, b.city_municipality as district,
           COUNT(ci.id) as total,
           SUM(CASE WHEN ci.clearance_status = 'cleared' THEN 1 ELSE 0 END) as cleared,
           ROUND(SUM(CASE WHEN ci.clearance_status = 'cleared' THEN 1 ELSE 0 END) * 100.0 / NULLIF(COUNT(ci.id), 0), 1) as rate
    FROM crime_department_barangays b
    LEFT JOIN crime_department_crime_incidents ci ON b.id = ci.barangay_id
    WHERE b.is_active = 1
    GROUP BY b.id
    HAVING total > 0
    ORDER BY rate DESC
    LIMIT 15
";
$barangayResult = $mysqli->query($barangayQuery);
$barangayData = [];
while ($row = $barangayResult->fetch_assoc()) {
    $barangayData[] = $row;
}

// Get monthly clearance trend
$monthlyQuery = "
    SELECT DATE_FORMAT(incident_date, '%b %Y') as month_label,
           DATE_FORMAT(incident_date, '%Y-%m') as month,
           COUNT(*) as total,
           SUM(CASE WHEN clearance_status = 'cleared' THEN 1 ELSE 0 END) as cleared,
           ROUND(SUM(CASE WHEN clearance_status = 'cleared' THEN 1 ELSE 0 END) * 100.0 / NULLIF(COUNT(*), 0), 1) as rate
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

// Get status distribution
$statusQuery = "
    SELECT status, COUNT(*) as count
    FROM crime_department_crime_incidents
    GROUP BY status
";
$statusResult = $mysqli->query($statusQuery);
$statusData = [];
while ($row = $statusResult->fetch_assoc()) {
    $statusData[$row['status']] = (int)$row['count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clearance Metrics | Crime Dep.</title>
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
                        <li class="breadcrumb-item active" aria-current="page"><span>Clearance Rates</span></li>
                    </ol>
                </nav>
                <h1>Clearance Metrics</h1>
                <p>Track case resolution rates across different crime categories and locations. Monitor clearance trends over time.</p>
            </div>

            <div class="sub-container">
                <div class="page-content">
                    <!-- Stats Cards -->
                    <div class="dashboard-grid">
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-card-icon primary"><i class="fas fa-folder-open"></i></div>
                                <div class="stat-card-info">
                                    <div class="stat-card-label">Total Cases</div>
                                    <div class="stat-card-value"><?php echo number_format($totalIncidents); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-card-icon success"><i class="fas fa-check-circle"></i></div>
                                <div class="stat-card-info">
                                    <div class="stat-card-label">Cleared</div>
                                    <div class="stat-card-value"><?php echo number_format($clearedCases); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-card-icon danger"><i class="fas fa-times-circle"></i></div>
                                <div class="stat-card-info">
                                    <div class="stat-card-label">Uncleared</div>
                                    <div class="stat-card-value"><?php echo number_format($unclearedCases); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-card-icon warning"><i class="fas fa-percentage"></i></div>
                                <div class="stat-card-info">
                                    <div class="stat-card-label">Clearance Rate</div>
                                    <div class="stat-card-value"><?php echo $clearanceRate; ?>%</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts -->
                    <div class="chart-grid-single">
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title">Monthly Clearance Rate Trend</h3>
                                <div class="chart-icon"><i class="fas fa-chart-line"></i></div>
                            </div>
                            <div class="chart-canvas-container">
                                <canvas id="monthlyChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="chart-grid-double">
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title">Case Status Distribution</h3>
                                <div class="chart-icon"><i class="fas fa-chart-pie"></i></div>
                            </div>
                            <div class="chart-canvas-container medium">
                                <canvas id="statusChart"></canvas>
                            </div>
                        </div>
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title">Clearance by Category</h3>
                                <div class="chart-icon"><i class="fas fa-chart-bar"></i></div>
                            </div>
                            <div class="chart-canvas-container medium">
                                <canvas id="categoryChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Barangay Clearance Rankings -->
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3 class="chart-title">Barangay Clearance Rankings</h3>
                            <div class="chart-icon"><i class="fas fa-list-ol"></i></div>
                        </div>
                        <div class="barangay-list">
                            <?php foreach ($barangayData as $index => $brgy): ?>
                            <div class="barangay-item">
                                <div class="barangay-position"><?php echo $index + 1; ?></div>
                                <div class="barangay-details">
                                    <div class="barangay-name"><?php echo htmlspecialchars($brgy['barangay_name']); ?></div>
                                    <div class="barangay-location">
                                        <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($brgy['district'] ?? 'QC'); ?>
                                        | <?php echo $brgy['cleared']; ?>/<?php echo $brgy['total']; ?> cleared
                                    </div>
                                </div>
                                <div style="text-align: right;">
                                    <div class="barangay-count" style="color: <?php echo ($brgy['rate'] ?? 0) >= 50 ? 'var(--success-color)' : 'var(--error-color)'; ?>">
                                        <?php echo $brgy['rate'] ?? 0; ?>%
                                    </div>
                                    <small style="color: var(--text-secondary-1);">clearance rate</small>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Category Clearance Table -->
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3 class="chart-title">Clearance by Crime Category</h3>
                            <div class="chart-icon"><i class="fas fa-tags"></i></div>
                        </div>
                        <div class="barangay-list">
                            <?php foreach ($categoryData as $index => $cat): ?>
                            <div class="barangay-item">
                                <div class="barangay-position" style="background: <?php echo $cat['color'] ?? '#6b7280'; ?>20; color: <?php echo $cat['color'] ?? '#6b7280'; ?>;">
                                    <?php echo $index + 1; ?>
                                </div>
                                <div class="barangay-details">
                                    <div class="barangay-name"><?php echo htmlspecialchars($cat['category_name']); ?></div>
                                    <div class="barangay-location">
                                        <?php echo $cat['cleared']; ?> cleared out of <?php echo $cat['total']; ?> total cases
                                    </div>
                                </div>
                                <div style="text-align: right;">
                                    <div class="barangay-count" style="color: <?php echo ($cat['rate'] ?? 0) >= 50 ? 'var(--success-color)' : 'var(--error-color)'; ?>">
                                        <?php echo $cat['rate'] ?? 0; ?>%
                                    </div>
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
        const monthlyLabels = <?php echo json_encode(array_column($monthlyData, 'month_label')); ?>;
        const monthlyRates = <?php echo json_encode(array_map('floatval', array_column($monthlyData, 'rate'))); ?>;
        const statusLabels = <?php echo json_encode(array_keys($statusData)); ?>;
        const statusValues = <?php echo json_encode(array_values($statusData)); ?>;
        const categoryLabels = <?php echo json_encode(array_slice(array_column($categoryData, 'category_name'), 0, 8)); ?>;
        const categoryRates = <?php echo json_encode(array_slice(array_map('floatval', array_column($categoryData, 'rate')), 0, 8)); ?>;

        let monthlyChart, statusChart, categoryChart;

        function getThemeColors() {
            const getColor = (v) => getComputedStyle(document.documentElement).getPropertyValue(v).trim() || null;
            return {
                primaryColor: getColor('--primary-color-1') || '#4c8a89',
                textColor: getColor('--text-color-1') || '#171717',
                gridColor: getColor('--border-color-1') || '#e5e5e5',
                successColor: getColor('--success-color') || '#10b981',
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
            const statusColors = { reported: '#f59e0b', under_investigation: '#3b82f6', resolved: '#10b981', closed: '#6b7280' };

            if (monthlyChart) monthlyChart.destroy();
            if (statusChart) statusChart.destroy();
            if (categoryChart) categoryChart.destroy();

            monthlyChart = new Chart(document.getElementById('monthlyChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: monthlyLabels,
                    datasets: [{
                        label: 'Clearance Rate (%)',
                        data: monthlyRates,
                        borderColor: colors.successColor,
                        backgroundColor: hexToRgba(colors.successColor, 0.1),
                        borderWidth: 3, fill: true, tension: 0.4,
                        pointRadius: 5, pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: true, position: 'top' } },
                    scales: {
                        y: { beginAtZero: true, max: 100, grid: { color: colors.gridColor }, ticks: { callback: v => v + '%' } },
                        x: { grid: { display: false } }
                    }
                }
            });

            statusChart = new Chart(document.getElementById('statusChart').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: statusLabels.map(s => s.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())),
                    datasets: [{
                        data: statusValues,
                        backgroundColor: statusLabels.map(s => statusColors[s] || '#6b7280'),
                        borderWidth: 2, borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { position: 'right', labels: { color: colors.textColor } } }
                }
            });

            categoryChart = new Chart(document.getElementById('categoryChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: categoryLabels,
                    datasets: [{
                        label: 'Clearance Rate (%)',
                        data: categoryRates,
                        backgroundColor: categoryRates.map(r => r >= 50 ? hexToRgba(colors.successColor, 0.8) : hexToRgba(colors.errorColor, 0.8)),
                        borderRadius: 4
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { beginAtZero: true, max: 100, grid: { color: colors.gridColor }, ticks: { callback: v => v + '%' } },
                        y: { grid: { display: false }, ticks: { color: colors.textColor } }
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
