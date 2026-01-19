<?php
require_once '../../api/middleware/auth.php';
require_once '../../api/config.php';

// Get categories with counts
$categoriesQuery = "
    SELECT cc.id, cc.category_code, cc.category_name, cc.icon, cc.color_code as color, cc.severity_level,
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

// Get total
$totalQuery = "SELECT COUNT(*) as total FROM crime_department_crime_incidents";
$totalResult = $mysqli->query($totalQuery);
$totalIncidents = $totalResult->fetch_assoc()['total'];

// Get top category
$topCategory = !empty($categories) ? $categories[0] : ['category_name' => 'N/A', 'count' => 0];

// Get source system distribution
$sourceQuery = "
    SELECT cc.source_system, COUNT(ci.id) as count
    FROM crime_department_crime_categories cc
    LEFT JOIN crime_department_crime_incidents ci ON cc.id = ci.crime_category_id
    WHERE cc.is_active = 1
    GROUP BY cc.source_system
    ORDER BY count DESC
";
$sourceResult = $mysqli->query($sourceQuery);
$sourceData = [];
while ($row = $sourceResult->fetch_assoc()) {
    $sourceData[] = $row;
}

// Get severity distribution
$severityQuery = "
    SELECT cc.severity_level, COUNT(ci.id) as count
    FROM crime_department_crime_categories cc
    LEFT JOIN crime_department_crime_incidents ci ON cc.id = ci.crime_category_id
    WHERE cc.is_active = 1 AND cc.severity_level IS NOT NULL
    GROUP BY cc.severity_level
    ORDER BY FIELD(cc.severity_level, 'critical', 'high', 'medium', 'low')
";
$severityResult = $mysqli->query($severityQuery);
$severityData = [];
while ($row = $severityResult->fetch_assoc()) {
    $severityData[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crime Type Trends | Crime Dep.</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/admin-header.css">
    <link rel="stylesheet" href="../css/hero.css">
    <link rel="stylesheet" href="../css/sidebar-footer.css">
    <link rel="stylesheet" href="../css/crime-type-trend.css">
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
                        <li class="breadcrumb-item active" aria-current="page"><span>Crime Type Trends</span></li>
                    </ol>
                </nav>
                <h1>Crime Type Trends</h1>
                <p>Analyze trends for specific crime categories to identify patterns and emerging threats.</p>
            </div>

            <div class="sub-container">
                <div class="page-content">
                    <!-- Stats Cards -->
                    <div class="dashboard-grid">
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-card-icon primary"><i class="fas fa-tags"></i></div>
                                <div class="stat-card-info">
                                    <div class="stat-card-label">Total Categories</div>
                                    <div class="stat-card-value"><?php echo count($categories); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-card-icon danger"><i class="fas fa-chart-bar"></i></div>
                                <div class="stat-card-info">
                                    <div class="stat-card-label">Total Incidents</div>
                                    <div class="stat-card-value"><?php echo number_format($totalIncidents); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-card-icon warning"><i class="fas fa-exclamation-triangle"></i></div>
                                <div class="stat-card-info">
                                    <div class="stat-card-label">Top Crime Type</div>
                                    <div class="stat-card-value" style="font-size: 0.9rem;"><?php echo htmlspecialchars($topCategory['category_name']); ?></div>
                                </div>
                            </div>
                            <div class="stat-card-footer"><span><?php echo $topCategory['count']; ?> incidents</span></div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-card-icon success"><i class="fas fa-network-wired"></i></div>
                                <div class="stat-card-info">
                                    <div class="stat-card-label">Source Systems</div>
                                    <div class="stat-card-value"><?php echo count($sourceData); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Row -->
                    <div class="chart-grid-double">
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title">Crime Type Distribution</h3>
                                <div class="chart-icon"><i class="fas fa-chart-pie"></i></div>
                            </div>
                            <div class="chart-canvas-container medium">
                                <canvas id="categoryChart"></canvas>
                            </div>
                        </div>
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title">Severity Distribution</h3>
                                <div class="chart-icon"><i class="fas fa-chart-bar"></i></div>
                            </div>
                            <div class="chart-canvas-container medium">
                                <canvas id="severityChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Category List -->
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3 class="chart-title">All Crime Categories</h3>
                            <div class="chart-icon"><i class="fas fa-list"></i></div>
                        </div>
                        <div class="crime-types-list">
                            <?php foreach ($categories as $index => $cat): ?>
                            <div class="crime-type-item">
                                <div class="crime-rank rank-<?php echo min($index + 1, 3); ?>">
                                    <?php echo $index + 1; ?>
                                </div>
                                <div class="crime-type-info">
                                    <div class="crime-type-name">
                                        <i class="fas <?php echo $cat['icon'] ?? 'fa-exclamation-circle'; ?>" style="color: <?php echo $cat['color'] ?? '#6b7280'; ?>; margin-right: 0.5rem;"></i>
                                        <?php echo htmlspecialchars($cat['category_name']); ?>
                                    </div>
                                    <div class="crime-type-description">
                                        <span class="severity-badge severity-<?php echo $cat['severity_level'] ?? 'low'; ?>">
                                            <?php echo ucfirst($cat['severity_level'] ?? 'N/A'); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="crime-type-stats">
                                    <div class="crime-count"><?php echo number_format($cat['count']); ?></div>
                                    <div class="crime-percentage">
                                        <?php echo $totalIncidents > 0 ? round(($cat['count'] / $totalIncidents) * 100, 1) : 0; ?>%
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <?php if (empty($categories)): ?>
                            <div class="no-data">No categories available</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include('../includes/admin-footer.php') ?>
    </div>

    <script>
        const categoryLabels = <?php echo json_encode(array_slice(array_column($categories, 'category_name'), 0, 8)); ?>;
        const categoryValues = <?php echo json_encode(array_map('intval', array_slice(array_column($categories, 'count'), 0, 8))); ?>;
        const categoryColors = <?php echo json_encode(array_slice(array_map(fn($c) => $c['color'] ?? '#6b7280', $categories), 0, 8)); ?>;
        const severityLabels = <?php echo json_encode(array_column($severityData, 'severity_level')); ?>;
        const severityValues = <?php echo json_encode(array_map('intval', array_column($severityData, 'count'))); ?>;

        let categoryChart, severityChart;

        function getThemeColors() {
            const getColor = (v) => getComputedStyle(document.documentElement).getPropertyValue(v).trim() || null;
            return {
                textColor: getColor('--text-color-1') || '#171717',
                gridColor: getColor('--border-color-1') || '#e5e5e5'
            };
        }

        function initializeCharts() {
            const colors = getThemeColors();
            const severityColors = { critical: '#dc2626', high: '#f59e0b', medium: '#3b82f6', low: '#10b981' };

            if (categoryChart) categoryChart.destroy();
            if (severityChart) severityChart.destroy();

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

            severityChart = new Chart(document.getElementById('severityChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: severityLabels.map(s => s.charAt(0).toUpperCase() + s.slice(1)),
                    datasets: [{
                        label: 'Incidents',
                        data: severityValues,
                        backgroundColor: severityLabels.map(s => severityColors[s] || '#6b7280'),
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
        }

        document.addEventListener('DOMContentLoaded', initializeCharts);
        const observer = new MutationObserver(() => setTimeout(initializeCharts, 50));
        observer.observe(document.documentElement, { attributes: true });
    </script>

</body>
</html>
