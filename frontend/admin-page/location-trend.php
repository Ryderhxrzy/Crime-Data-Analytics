<?php
require_once '../../api/middleware/auth.php';
require_once '../../api/config.php';

// Get top barangays by crime count
$barangaysQuery = "
    SELECT b.id, b.barangay_name, b.city_municipality as district, COUNT(ci.id) as count
    FROM crime_department_barangays b
    LEFT JOIN crime_department_crime_incidents ci ON b.id = ci.barangay_id
    WHERE b.is_active = 1
    GROUP BY b.id
    HAVING count > 0
    ORDER BY count DESC
    LIMIT 15
";
$barangaysResult = $mysqli->query($barangaysQuery);
$topBarangays = [];
while ($row = $barangaysResult->fetch_assoc()) {
    $topBarangays[] = $row;
}

// Get district summary
$districtQuery = "
    SELECT b.city_municipality as district, COUNT(ci.id) as count
    FROM crime_department_barangays b
    LEFT JOIN crime_department_crime_incidents ci ON b.id = ci.barangay_id
    WHERE b.is_active = 1 AND b.city_municipality IS NOT NULL
    GROUP BY b.city_municipality
    ORDER BY count DESC
";
$districtResult = $mysqli->query($districtQuery);
$districtData = [];
while ($row = $districtResult->fetch_assoc()) {
    $districtData[] = $row;
}

// Get total incidents
$totalQuery = "SELECT COUNT(*) as total FROM crime_department_crime_incidents";
$totalResult = $mysqli->query($totalQuery);
$totalIncidents = $totalResult->fetch_assoc()['total'];

// Get unique barangays with incidents
$uniqueBarangaysQuery = "SELECT COUNT(DISTINCT barangay_id) as count FROM crime_department_crime_incidents WHERE barangay_id IS NOT NULL";
$uniqueBarangaysResult = $mysqli->query($uniqueBarangaysQuery);
$affectedBarangays = $uniqueBarangaysResult->fetch_assoc()['count'];

$peakBarangay = !empty($topBarangays) ? $topBarangays[0] : ['barangay_name' => 'N/A', 'count' => 0];
$peakDistrict = !empty($districtData) ? $districtData[0] : ['district' => 'N/A', 'count' => 0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Location Trends | Crime Dep.</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/admin-header.css">
    <link rel="stylesheet" href="../css/hero.css">
    <link rel="stylesheet" href="../css/sidebar-footer.css">
    <link rel="stylesheet" href="../css/location-trend.css">
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
                        <li class="breadcrumb-item active" aria-current="page"><span>Location Trends</span></li>
                    </ol>
                </nav>
                <h1>Location-Based Trends</h1>
                <p>Analyze crime distribution across different barangays and districts in Quezon City.</p>
            </div>

            <div class="sub-container">
                <div class="page-content">
                    <!-- Statistics Cards -->
                    <div class="dashboard-grid">
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-card-icon primary"><i class="fas fa-map-marker-alt"></i></div>
                                <div class="stat-card-info">
                                    <div class="stat-card-label">Total Incidents</div>
                                    <div class="stat-card-value"><?php echo number_format($totalIncidents); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-card-icon danger"><i class="fas fa-building"></i></div>
                                <div class="stat-card-info">
                                    <div class="stat-card-label">Affected Barangays</div>
                                    <div class="stat-card-value"><?php echo number_format($affectedBarangays); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-card-icon warning"><i class="fas fa-exclamation-triangle"></i></div>
                                <div class="stat-card-info">
                                    <div class="stat-card-label">Peak Barangay</div>
                                    <div class="stat-card-value" style="font-size: 1rem;"><?php echo htmlspecialchars($peakBarangay['barangay_name']); ?></div>
                                </div>
                            </div>
                            <div class="stat-card-footer"><span><?php echo $peakBarangay['count']; ?> incidents</span></div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-card-icon success"><i class="fas fa-city"></i></div>
                                <div class="stat-card-info">
                                    <div class="stat-card-label">Peak District</div>
                                    <div class="stat-card-value" style="font-size: 1rem;"><?php echo htmlspecialchars($peakDistrict['district']); ?></div>
                                </div>
                            </div>
                            <div class="stat-card-footer"><span><?php echo $peakDistrict['count']; ?> incidents</span></div>
                        </div>
                    </div>

                    <!-- District Chart -->
                    <div class="chart-grid-single">
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title">Crime Distribution by District</h3>
                                <div class="chart-icon"><i class="fas fa-chart-pie"></i></div>
                            </div>
                            <div class="chart-canvas-container medium">
                                <canvas id="districtChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Top Barangays Chart -->
                    <div class="chart-grid-single">
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title">Top 15 Barangays by Crime Count</h3>
                                <div class="chart-icon"><i class="fas fa-chart-bar"></i></div>
                            </div>
                            <div class="chart-canvas-container" style="height: 400px;">
                                <canvas id="barangayChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Barangays List -->
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3 class="chart-title">Barangay Crime Rankings</h3>
                            <div class="chart-icon"><i class="fas fa-list-ol"></i></div>
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
                            <div class="no-data">No barangay data available</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include('../includes/admin-footer.php') ?>
    </div>

    <script>
        const districtLabels = <?php echo json_encode(array_column($districtData, 'district')); ?>;
        const districtValues = <?php echo json_encode(array_map('intval', array_column($districtData, 'count'))); ?>;
        const barangayLabels = <?php echo json_encode(array_column($topBarangays, 'barangay_name')); ?>;
        const barangayValues = <?php echo json_encode(array_map('intval', array_column($topBarangays, 'count'))); ?>;

        let districtChart, barangayChart;

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
            const chartColors = ['#ef4444', '#f59e0b', '#10b981', '#3b82f6', '#8b5cf6', '#ec4899'];

            if (districtChart) districtChart.destroy();
            if (barangayChart) barangayChart.destroy();

            districtChart = new Chart(document.getElementById('districtChart').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: districtLabels,
                    datasets: [{
                        data: districtValues,
                        backgroundColor: chartColors.slice(0, districtLabels.length),
                        borderWidth: 2, borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { position: 'right', labels: { color: colors.textColor } } }
                }
            });

            barangayChart = new Chart(document.getElementById('barangayChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: barangayLabels,
                    datasets: [{
                        label: 'Incidents',
                        data: barangayValues,
                        backgroundColor: hexToRgba(colors.primaryColor, 0.8),
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
        }

        document.addEventListener('DOMContentLoaded', initializeCharts);
        const observer = new MutationObserver(() => setTimeout(initializeCharts, 50));
        observer.observe(document.documentElement, { attributes: true });
    </script>

    <style>
        .no-data { text-align: center; padding: 2rem; color: var(--text-secondary-1); }
    </style>
</body>
</html>
