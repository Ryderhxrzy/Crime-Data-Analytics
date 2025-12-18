<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Location-Based Trends - Crime Data Analytics</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/admin-header.css">
    <link rel="stylesheet" href="../css/hero.css">
    <link rel="stylesheet" href="../css/sidebar-footer.css">
    <link rel="stylesheet" href="../css/location-trend.css">
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
                        <li class="breadcrumb-item">
                            <a href="/" class="breadcrumb-link">
                                <span>Home</span>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="/analytics" class="breadcrumb-link">
                                <span>Analytics</span>
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <span>Location-Based Trends</span>
                        </li>
                    </ol>
                </nav>
                <h1>Location-Based Trends</h1>
                <p>Compare crime statistics across different barangays and identify areas with rising or declining crime rates. Analyze location-based patterns to prioritize resource allocation and intervention strategies.</p>
            </div>

            <div class="sub-container">
                <div class="page-content">
                    <!-- Crime Comparison by Barangay -->
                    <div class="chart-grid-single">
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title">Crime Incidents by Barangay (Top 15)</h3>
                                <div class="chart-icon">
                                    <i class="fas fa-map-marked-alt"></i>
                                </div>
                            </div>
                            <div class="chart-canvas-container large">
                                <canvas id="barangayCrimeChart"></canvas>
                            </div>
                            <div class="chart-footer-text">
                                Barangay San Isidro leads with 1,847 incidents • Overall 15 barangays account for 68% of total crimes
                            </div>
                        </div>
                    </div>

                    <!-- Barangay Trend Comparison -->
                    <div class="chart-grid-double">
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title">Top 5 Barangays - Trend Analysis</h3>
                                <div class="chart-icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                            </div>
                            <div class="chart-canvas-container medium">
                                <canvas id="topBarangayTrendChart"></canvas>
                            </div>
                            <div class="chart-footer-text">
                                6-month trend comparison for highest crime barangays
                            </div>
                        </div>

                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title">Crime Distribution by District</h3>
                                <div class="chart-icon">
                                    <i class="fas fa-chart-pie"></i>
                                </div>
                            </div>
                            <div class="chart-canvas-container medium">
                                <canvas id="districtPieChart"></canvas>
                            </div>
                            <div class="chart-footer-text">
                                Central District shows highest crime concentration
                            </div>
                        </div>
                    </div>

                    <!-- Barangay Rankings with Change Indicators -->
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3 class="chart-title">Barangay Crime Rankings</h3>
                            <div class="chart-icon">
                                <i class="fas fa-ranking-star"></i>
                            </div>
                        </div>
                        <div class="barangay-rankings">
                            <div class="ranking-item rising">
                                <div class="ranking-position">1</div>
                                <div class="ranking-details">
                                    <div class="ranking-name">Barangay San Isidro</div>
                                    <div class="ranking-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        Central District
                                    </div>
                                </div>
                                <div class="ranking-stats">
                                    <div class="ranking-count">1,847</div>
                                    <div class="ranking-change rising">
                                        <i class="fas fa-arrow-up"></i> +18.2%
                                    </div>
                                </div>
                            </div>

                            <div class="ranking-item rising">
                                <div class="ranking-position">2</div>
                                <div class="ranking-details">
                                    <div class="ranking-name">Barangay Poblacion</div>
                                    <div class="ranking-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        Downtown District
                                    </div>
                                </div>
                                <div class="ranking-stats">
                                    <div class="ranking-count">1,652</div>
                                    <div class="ranking-change rising">
                                        <i class="fas fa-arrow-up"></i> +12.5%
                                    </div>
                                </div>
                            </div>

                            <div class="ranking-item declining">
                                <div class="ranking-position">3</div>
                                <div class="ranking-details">
                                    <div class="ranking-name">Barangay Santa Cruz</div>
                                    <div class="ranking-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        East District
                                    </div>
                                </div>
                                <div class="ranking-stats">
                                    <div class="ranking-count">1,423</div>
                                    <div class="ranking-change declining">
                                        <i class="fas fa-arrow-down"></i> -5.3%
                                    </div>
                                </div>
                            </div>

                            <div class="ranking-item stable">
                                <div class="ranking-position">4</div>
                                <div class="ranking-details">
                                    <div class="ranking-name">Barangay Santo Niño</div>
                                    <div class="ranking-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        North District
                                    </div>
                                </div>
                                <div class="ranking-stats">
                                    <div class="ranking-count">1,289</div>
                                    <div class="ranking-change stable">
                                        <i class="fas fa-minus"></i> +1.2%
                                    </div>
                                </div>
                            </div>

                            <div class="ranking-item declining">
                                <div class="ranking-position">5</div>
                                <div class="ranking-details">
                                    <div class="ranking-name">Barangay San Roque</div>
                                    <div class="ranking-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        West District
                                    </div>
                                </div>
                                <div class="ranking-stats">
                                    <div class="ranking-count">1,156</div>
                                    <div class="ranking-change declining">
                                        <i class="fas fa-arrow-down"></i> -8.7%
                                    </div>
                                </div>
                            </div>

                            <div class="ranking-item rising">
                                <div class="ranking-position">6</div>
                                <div class="ranking-details">
                                    <div class="ranking-name">Barangay San Antonio</div>
                                    <div class="ranking-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        South District
                                    </div>
                                </div>
                                <div class="ranking-stats">
                                    <div class="ranking-count">1,042</div>
                                    <div class="ranking-change rising">
                                        <i class="fas fa-arrow-up"></i> +15.4%
                                    </div>
                                </div>
                            </div>

                            <div class="ranking-item declining">
                                <div class="ranking-position">7</div>
                                <div class="ranking-details">
                                    <div class="ranking-name">Barangay San Vicente</div>
                                    <div class="ranking-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        Central District
                                    </div>
                                </div>
                                <div class="ranking-stats">
                                    <div class="ranking-count">987</div>
                                    <div class="ranking-change declining">
                                        <i class="fas fa-arrow-down"></i> -3.2%
                                    </div>
                                </div>
                            </div>

                            <div class="ranking-item stable">
                                <div class="ranking-position">8</div>
                                <div class="ranking-details">
                                    <div class="ranking-name">Barangay San Jose</div>
                                    <div class="ranking-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        East District
                                    </div>
                                </div>
                                <div class="ranking-stats">
                                    <div class="ranking-count">892</div>
                                    <div class="ranking-change stable">
                                        <i class="fas fa-minus"></i> +0.8%
                                    </div>
                                </div>
                            </div>

                            <div class="ranking-item rising">
                                <div class="ranking-position">9</div>
                                <div class="ranking-details">
                                    <div class="ranking-name">Barangay San Pedro</div>
                                    <div class="ranking-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        North District
                                    </div>
                                </div>
                                <div class="ranking-stats">
                                    <div class="ranking-count">834</div>
                                    <div class="ranking-change rising">
                                        <i class="fas fa-arrow-up"></i> +9.3%
                                    </div>
                                </div>
                            </div>

                            <div class="ranking-item declining">
                                <div class="ranking-position">10</div>
                                <div class="ranking-details">
                                    <div class="ranking-name">Barangay Santa Maria</div>
                                    <div class="ranking-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        West District
                                    </div>
                                </div>
                                <div class="ranking-stats">
                                    <div class="ranking-count">767</div>
                                    <div class="ranking-change declining">
                                        <i class="fas fa-arrow-down"></i> -11.2%
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Location-Based Insights -->
                    <div class="location-insights">
                        <div class="chart-header">
                            <h3 class="chart-title">Location-Based Insights</h3>
                            <div class="chart-icon">
                                <i class="fas fa-lightbulb"></i>
                            </div>
                        </div>
                        <div class="insights-grid">
                            <div class="insight-card alert">
                                <div class="insight-icon alert">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <div class="insight-content">
                                    <h4>Highest Crime Area</h4>
                                    <p class="insight-value">Barangay San Isidro</p>
                                    <p class="insight-description">Central District shows 18.2% increase with 1,847 total incidents.</p>
                                </div>
                            </div>

                            <div class="insight-card success">
                                <div class="insight-icon success">
                                    <i class="fas fa-arrow-down"></i>
                                </div>
                                <div class="insight-content">
                                    <h4>Most Improved Area</h4>
                                    <p class="insight-value">Barangay Santa Maria</p>
                                    <p class="insight-description">West District shows 11.2% decrease in crime incidents.</p>
                                </div>
                            </div>

                            <div class="insight-card warning">
                                <div class="insight-icon warning">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div class="insight-content">
                                    <h4>Rising Concern</h4>
                                    <p class="insight-value">3 Barangays</p>
                                    <p class="insight-description">San Isidro, Poblacion, and San Antonio show significant upward trends.</p>
                                </div>
                            </div>

                            <div class="insight-card info">
                                <div class="insight-icon info">
                                    <i class="fas fa-map-marked"></i>
                                </div>
                                <div class="insight-content">
                                    <h4>District Concentration</h4>
                                    <p class="insight-value">Central District</p>
                                    <p class="insight-description">32% of all crimes occur in the Central District area.</p>
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
        let barangayCrimeChart, topBarangayTrendChart, districtPieChart;

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

        function hexToRgba(hex, alpha) {
            if (!hex) return `rgba(0,0,0,${alpha})`;
            const r = parseInt(hex.slice(1, 3), 16);
            const g = parseInt(hex.slice(3, 5), 16);
            const b = parseInt(hex.slice(5, 7), 16);
            return `rgba(${r}, ${g}, ${b}, ${alpha})`;
        }

        function initializeCharts() {
            const colors = getThemeColors();

            Chart.defaults.color = colors.textColor;
            Chart.defaults.borderColor = colors.gridColor;
            Chart.defaults.font.family = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';

            if (barangayCrimeChart) barangayCrimeChart.destroy();
            if (topBarangayTrendChart) topBarangayTrendChart.destroy();
            if (districtPieChart) districtPieChart.destroy();

            // Barangay Crime Comparison Chart
            const barangayCtx = document.getElementById('barangayCrimeChart').getContext('2d');
            barangayCrimeChart = new Chart(barangayCtx, {
                type: 'bar',
                data: {
                    labels: ['San Isidro', 'Poblacion', 'Santa Cruz', 'Santo Niño', 'San Roque',
                             'San Antonio', 'San Vicente', 'San Jose', 'San Pedro', 'Santa Maria',
                             'San Miguel', 'San Rafael', 'San Gabriel', 'San Carlos', 'San Juan'],
                    datasets: [{
                        label: 'Crime Incidents',
                        data: [1847, 1652, 1423, 1289, 1156, 1042, 987, 892, 834, 767, 698, 623, 567, 489, 421],
                        backgroundColor: hexToRgba(colors.primaryColor, 0.8),
                        borderColor: colors.primaryColor,
                        borderWidth: 2,
                        borderRadius: 6,
                        borderSkipped: false
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            callbacks: { label: (context) => 'Incidents: ' + context.parsed.x.toLocaleString() }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: { color: colors.textColor, font: { size: 12 } },
                            grid: { color: colors.gridColor }
                        },
                        y: {
                            ticks: { color: colors.textColor, font: { size: 12 } },
                            grid: { display: false }
                        }
                    }
                }
            });

            // Top 5 Barangays Trend Chart
            const trendCtx = document.getElementById('topBarangayTrendChart').getContext('2d');
            topBarangayTrendChart = new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: ['January', 'February', 'March', 'April', 'May', 'June'],
                    datasets: [
                        {
                            label: 'San Isidro',
                            data: [289, 298, 312, 305, 318, 325],
                            borderColor: colors.errorColor,
                            backgroundColor: hexToRgba(colors.errorColor, 0.1),
                            borderWidth: 2,
                            tension: 0.4
                        },
                        {
                            label: 'Poblacion',
                            data: [267, 272, 278, 281, 286, 292],
                            borderColor: colors.warningColor,
                            backgroundColor: hexToRgba(colors.warningColor, 0.1),
                            borderWidth: 2,
                            tension: 0.4
                        },
                        {
                            label: 'Santa Cruz',
                            data: [245, 241, 238, 236, 235, 231],
                            borderColor: colors.successColor,
                            backgroundColor: hexToRgba(colors.successColor, 0.1),
                            borderWidth: 2,
                            tension: 0.4
                        },
                        {
                            label: 'Santo Niño',
                            data: [214, 215, 216, 215, 217, 218],
                            borderColor: colors.primaryColor,
                            backgroundColor: hexToRgba(colors.primaryColor, 0.1),
                            borderWidth: 2,
                            tension: 0.4
                        },
                        {
                            label: 'San Roque',
                            data: [208, 201, 197, 194, 191, 187],
                            borderColor: '#8b5cf6',
                            backgroundColor: 'rgba(139, 92, 246, 0.1)',
                            borderWidth: 2,
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: { color: colors.textColor, font: { size: 12 }, padding: 10 }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { color: colors.textColor, font: { size: 12 } },
                            grid: { color: colors.gridColor }
                        },
                        x: {
                            ticks: { color: colors.textColor, font: { size: 11 } },
                            grid: { display: false }
                        }
                    }
                }
            });

            // District Pie Chart
            const districtCtx = document.getElementById('districtPieChart').getContext('2d');
            districtPieChart = new Chart(districtCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Central District', 'Downtown District', 'East District', 'North District', 'West District', 'South District'],
                    datasets: [{
                        data: [3542, 2856, 2315, 2123, 1923, 1084],
                        backgroundColor: [
                            colors.errorColor,
                            colors.warningColor,
                            '#8b5cf6',
                            colors.primaryColor,
                            colors.successColor,
                            '#3b82f6'
                        ],
                        borderWidth: 2,
                        borderColor: colors.cardBg,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                color: colors.textColor,
                                font: { size: 12 },
                                padding: 12,
                                generateLabels: function(chart) {
                                    const data = chart.data;
                                    return data.labels.map((label, i) => {
                                        const value = data.datasets[0].data[i];
                                        const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return {
                                            text: `${label}: ${percentage}%`,
                                            fillStyle: data.datasets[0].backgroundColor[i],
                                            fontColor: colors.textColor,
                                            hidden: false,
                                            index: i
                                        };
                                    });
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return `${label}: ${value.toLocaleString()} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            initializeCharts();
        });

        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'class' || mutation.attributeName === 'data-theme') {
                    setTimeout(() => { initializeCharts(); }, 50);
                }
            });
        });

        observer.observe(document.documentElement, { attributes: true });
        if (document.body) {
            observer.observe(document.body, { attributes: true });
        }
    </script>
</body>
</html>