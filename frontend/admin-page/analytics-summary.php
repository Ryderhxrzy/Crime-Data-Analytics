<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Summary - Crime Data Analytics</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/admin-header.css">
    <link rel="stylesheet" href="../css/hero.css">
    <link rel="stylesheet" href="../css/sidebar-footer.css">
    <link rel="stylesheet" href="../css/analytics-summary.css">
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
                                Peak in June with 1,892 incidents • Overall upward trend of 51.8%
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
                                Total of 12,847 crimes recorded this year
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
                                Current week • Friday has the highest crime rate (67 incidents)
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
                                Peak hours: 6PM - 9PM • Lowest activity: 3AM - 6AM
                            </div>
                        </div>
                    </div>

                    <!-- Crime Trends Section -->
                    <div class="trend-section">
                        <div class="chart-header">
                            <h3 class="chart-title">Crime Trend Indicators</h3>
                            <div class="chart-icon">
                                <i class="fas fa-chart-area"></i>
                            </div>
                        </div>
                        <div class="trend-indicators">
                            <div class="trend-item">
                                <div class="trend-icon-wrapper up">
                                    <i class="fas fa-arrow-up"></i>
                                </div>
                                <div class="trend-content">
                                    <div class="trend-label">Theft Incidents</div>
                                    <div class="trend-value">
                                        3,542
                                        <span class="trend-change up">
                                            <i class="fas fa-arrow-up"></i> 8.3%
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="trend-item">
                                <div class="trend-icon-wrapper down">
                                    <i class="fas fa-arrow-down"></i>
                                </div>
                                <div class="trend-content">
                                    <div class="trend-label">Assault Cases</div>
                                    <div class="trend-value">
                                        2,156
                                        <span class="trend-change down">
                                            <i class="fas fa-arrow-down"></i> 3.7%
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="trend-item">
                                <div class="trend-icon-wrapper up">
                                    <i class="fas fa-arrow-up"></i>
                                </div>
                                <div class="trend-content">
                                    <div class="trend-label">Vehicle Crimes</div>
                                    <div class="trend-value">
                                        1,234
                                        <span class="trend-change up">
                                            <i class="fas fa-arrow-up"></i> 12.5%
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="trend-item">
                                <div class="trend-icon-wrapper stable">
                                    <i class="fas fa-minus"></i>
                                </div>
                                <div class="trend-content">
                                    <div class="trend-label">Drug-Related</div>
                                    <div class="trend-value">
                                        892
                                        <span class="trend-change stable">
                                            <i class="fas fa-minus"></i> 0.2%
                                        </span>
                                    </div>
                                </div>
                            </div>
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
                            <div class="crime-type-item">
                                <div class="crime-rank rank-1">
                                    <i class="fas fa-medal"></i>
                                </div>
                                <div class="crime-type-info">
                                    <div class="crime-type-name">Theft & Robbery</div>
                                    <div class="crime-type-description">
                                        Includes burglary, shoplifting, pickpocketing, and armed robbery
                                    </div>
                                </div>
                                <div class="crime-type-stats">
                                    <div class="crime-count">3,542</div>
                                    <div class="crime-percentage">27.6% of total</div>
                                </div>
                            </div>

                            <div class="crime-type-item">
                                <div class="crime-rank rank-2">
                                    <i class="fas fa-medal"></i>
                                </div>
                                <div class="crime-type-info">
                                    <div class="crime-type-name">Burglary & Breaking & Entering</div>
                                    <div class="crime-type-description">
                                        Residential and commercial break-ins, property intrusion
                                    </div>
                                </div>
                                <div class="crime-type-stats">
                                    <div class="crime-count">2,584</div>
                                    <div class="crime-percentage">20.1% of total</div>
                                </div>
                            </div>

                            <div class="crime-type-item">
                                <div class="crime-rank rank-3">
                                    <i class="fas fa-medal"></i>
                                </div>
                                <div class="crime-type-info">
                                    <div class="crime-type-name">Assault & Battery</div>
                                    <div class="crime-type-description">
                                        Physical altercations, domestic violence, aggravated assault
                                    </div>
                                </div>
                                <div class="crime-type-stats">
                                    <div class="crime-count">2,156</div>
                                    <div class="crime-percentage">16.8% of total</div>
                                </div>
                            </div>
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
                            <div class="barangay-item">
                                <div class="barangay-position">1</div>
                                <div class="barangay-details">
                                    <div class="barangay-name">Barangay San Isidro</div>
                                    <div class="barangay-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        Central District
                                    </div>
                                </div>
                                <div class="barangay-count">1,847</div>
                            </div>

                            <div class="barangay-item">
                                <div class="barangay-position">2</div>
                                <div class="barangay-details">
                                    <div class="barangay-name">Barangay Poblacion</div>
                                    <div class="barangay-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        Downtown District
                                    </div>
                                </div>
                                <div class="barangay-count">1,652</div>
                            </div>

                            <div class="barangay-item">
                                <div class="barangay-position">3</div>
                                <div class="barangay-details">
                                    <div class="barangay-name">Barangay Santa Cruz</div>
                                    <div class="barangay-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        East District
                                    </div>
                                </div>
                                <div class="barangay-count">1,423</div>
                            </div>

                            <div class="barangay-item">
                                <div class="barangay-position">4</div>
                                <div class="barangay-details">
                                    <div class="barangay-name">Barangay Santo Niño</div>
                                    <div class="barangay-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        North District
                                    </div>
                                </div>
                                <div class="barangay-count">1,289</div>
                            </div>

                            <div class="barangay-item">
                                <div class="barangay-position">5</div>
                                <div class="barangay-details">
                                    <div class="barangay-name">Barangay San Roque</div>
                                    <div class="barangay-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        West District
                                    </div>
                                </div>
                                <div class="barangay-count">1,156</div>
                            </div>

                            <div class="barangay-item">
                                <div class="barangay-position">6</div>
                                <div class="barangay-details">
                                    <div class="barangay-name">Barangay San Antonio</div>
                                    <div class="barangay-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        South District
                                    </div>
                                </div>
                                <div class="barangay-count">1,042</div>
                            </div>

                            <div class="barangay-item">
                                <div class="barangay-position">7</div>
                                <div class="barangay-details">
                                    <div class="barangay-name">Barangay San Vicente</div>
                                    <div class="barangay-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        Central District
                                    </div>
                                </div>
                                <div class="barangay-count">987</div>
                            </div>

                            <div class="barangay-item">
                                <div class="barangay-position">8</div>
                                <div class="barangay-details">
                                    <div class="barangay-name">Barangay San Jose</div>
                                    <div class="barangay-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        East District
                                    </div>
                                </div>
                                <div class="barangay-count">892</div>
                            </div>

                            <div class="barangay-item">
                                <div class="barangay-position">9</div>
                                <div class="barangay-details">
                                    <div class="barangay-name">Barangay San Pedro</div>
                                    <div class="barangay-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        North District
                                    </div>
                                </div>
                                <div class="barangay-count">834</div>
                            </div>

                            <div class="barangay-item">
                                <div class="barangay-position">10</div>
                                <div class="barangay-details">
                                    <div class="barangay-name">Barangay Santa Maria</div>
                                    <div class="barangay-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        West District
                                    </div>
                                </div>
                                <div class="barangay-count">767</div>
                            </div>
                        </div>
                    </div>

                    <!-- Current High-Risk Areas -->
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3 class="chart-title">Current High-Risk Areas</h3>
                            <div class="chart-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                        </div>
                        <div class="risk-areas-grid">
                            <div class="risk-area-card high-risk">
                                <div class="risk-area-header">
                                    <div class="risk-area-name">Downtown Shopping District</div>
                                    <span class="risk-badge high">High</span>
                                </div>
                                <div class="risk-area-stats">
                                    <div class="risk-stat">
                                        <div class="risk-stat-value">156</div>
                                        <div class="risk-stat-label">Incidents</div>
                                    </div>
                                    <div class="risk-stat">
                                        <div class="risk-stat-value">18%</div>
                                        <div class="risk-stat-label">Increase</div>
                                    </div>
                                </div>
                                <div class="risk-area-factors">
                                    <i class="fas fa-info-circle"></i>
                                    High theft & pickpocketing activity
                                </div>
                            </div>

                            <div class="risk-area-card high-risk">
                                <div class="risk-area-header">
                                    <div class="risk-area-name">North Residential Area</div>
                                    <span class="risk-badge high">High</span>
                                </div>
                                <div class="risk-area-stats">
                                    <div class="risk-stat">
                                        <div class="risk-stat-value">142</div>
                                        <div class="risk-stat-label">Incidents</div>
                                    </div>
                                    <div class="risk-stat">
                                        <div class="risk-stat-value">22%</div>
                                        <div class="risk-stat-label">Increase</div>
                                    </div>
                                </div>
                                <div class="risk-area-factors">
                                    <i class="fas fa-info-circle"></i>
                                    Multiple burglary reports
                                </div>
                            </div>

                            <div class="risk-area-card high-risk">
                                <div class="risk-area-header">
                                    <div class="risk-area-name">East Market Complex</div>
                                    <span class="risk-badge high">High</span>
                                </div>
                                <div class="risk-area-stats">
                                    <div class="risk-stat">
                                        <div class="risk-stat-value">134</div>
                                        <div class="risk-stat-label">Incidents</div>
                                    </div>
                                    <div class="risk-stat">
                                        <div class="risk-stat-value">15%</div>
                                        <div class="risk-stat-label">Increase</div>
                                    </div>
                                </div>
                                <div class="risk-area-factors">
                                    <i class="fas fa-info-circle"></i>
                                    Vehicle theft hotspot
                                </div>
                            </div>

                            <div class="risk-area-card medium-risk">
                                <div class="risk-area-header">
                                    <div class="risk-area-name">Central Park Area</div>
                                    <span class="risk-badge medium">Medium</span>
                                </div>
                                <div class="risk-area-stats">
                                    <div class="risk-stat">
                                        <div class="risk-stat-value">98</div>
                                        <div class="risk-stat-label">Incidents</div>
                                    </div>
                                    <div class="risk-stat">
                                        <div class="risk-stat-value">8%</div>
                                        <div class="risk-stat-label">Increase</div>
                                    </div>
                                </div>
                                <div class="risk-area-factors">
                                    <i class="fas fa-info-circle"></i>
                                    Suspicious activity after dark
                                </div>
                            </div>

                            <div class="risk-area-card medium-risk">
                                <div class="risk-area-header">
                                    <div class="risk-area-name">West Commercial Zone</div>
                                    <span class="risk-badge medium">Medium</span>
                                </div>
                                <div class="risk-area-stats">
                                    <div class="risk-stat">
                                        <div class="risk-stat-value">87</div>
                                        <div class="risk-stat-label">Incidents</div>
                                    </div>
                                    <div class="risk-stat">
                                        <div class="risk-stat-value">5%</div>
                                        <div class="risk-stat-label">Increase</div>
                                    </div>
                                </div>
                                <div class="risk-area-factors">
                                    <i class="fas fa-info-circle"></i>
                                    Vandalism and property damage
                                </div>
                            </div>

                            <div class="risk-area-card low-risk">
                                <div class="risk-area-header">
                                    <div class="risk-area-name">South Suburban District</div>
                                    <span class="risk-badge low">Low</span>
                                </div>
                                <div class="risk-area-stats">
                                    <div class="risk-stat">
                                        <div class="risk-stat-value">42</div>
                                        <div class="risk-stat-label">Incidents</div>
                                    </div>
                                    <div class="risk-stat">
                                        <div class="risk-stat-value">2%</div>
                                        <div class="risk-stat-label">Decrease</div>
                                    </div>
                                </div>
                                <div class="risk-area-factors">
                                    <i class="fas fa-info-circle"></i>
                                    Well-patrolled residential area
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
            const r = parseInt(hex.slice(1, 3), 16);
            const g = parseInt(hex.slice(3, 5), 16);
            const b = parseInt(hex.slice(5, 7), 16);
            return `rgba(${r}, ${g}, ${b}, ${alpha})`;
        }

        // Function to initialize/update all charts
        function initializeCharts() {
            const colors = getThemeColors();

            // Chart.js default configuration with theme-aware colors
            Chart.defaults.color = colors.textColor;
            Chart.defaults.borderColor = colors.gridColor;
            Chart.defaults.font.family = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';

            // Destroy existing charts if they exist
            if (monthlyTrendChart) monthlyTrendChart.destroy();
            if (crimeTypePieChart) crimeTypePieChart.destroy();
            if (weeklyBarChart) weeklyBarChart.destroy();
            if (hourlyLineChart) hourlyLineChart.destroy();

            // 1. Monthly Trend Line Chart
            const monthlyCtx = document.getElementById('monthlyTrendChart').getContext('2d');
            monthlyTrendChart = new Chart(monthlyCtx, {
                type: 'line',
                data: {
                    labels: ['January', 'February', 'March', 'April', 'May', 'June'],
                    datasets: [{
                        label: 'Crime Incidents',
                        data: [1247, 1089, 1378, 1034, 1489, 1892],
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
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                color: colors.textColor,
                                font: {
                                    size: 14,
                                    weight: '600'
                                },
                                padding: 15
                            }
                        },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        callbacks: {
                            label: function(context) {
                                return 'Incidents: ' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: colors.textColor,
                            font: {
                                size: 12
                            },
                            callback: function(value) {
                                return value.toLocaleString();
                            }
                        },
                        grid: {
                            color: colors.gridColor
                        }
                    },
                    x: {
                        ticks: {
                            color: colors.textColor,
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // 2. Crime Type Pie Chart
        const pieCtx = document.getElementById('crimeTypePieChart').getContext('2d');
        crimeTypePieChart = new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: ['Theft & Robbery', 'Burglary', 'Assault', 'Vandalism', 'Vehicle Crimes', 'Drug-Related', 'Others'],
                datasets: [{
                    data: [3542, 2584, 2156, 1876, 1234, 892, 563],
                    backgroundColor: [
                        colors.errorColor,
                        colors.warningColor,
                        '#8b5cf6',
                        '#3b82f6',
                        colors.successColor,
                        '#6366f1',
                        colors.textSecondary
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
                            font: {
                                size: 13
                            },
                            padding: 15,
                            generateLabels: function(chart) {
                                const data = chart.data;
                                const currentColors = getThemeColors();
                                return data.labels.map((label, i) => {
                                    const value = data.datasets[0].data[i];
                                    const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return {
                                        text: `${label}: ${value.toLocaleString()} (${percentage}%)`,
                                        fillStyle: data.datasets[0].backgroundColor[i],
                                        fontColor: currentColors.textColor,
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

        // 3. Weekly Bar Chart
        const weeklyCtx = document.getElementById('weeklyBarChart').getContext('2d');
        weeklyBarChart = new Chart(weeklyCtx, {
            type: 'bar',
            data: {
                labels: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                datasets: [{
                    label: 'Incidents',
                    data: [42, 38, 48, 52, 67, 59, 36],
                    backgroundColor: [
                        hexToRgba(colors.primaryColor, 0.8),
                        hexToRgba(colors.primaryColor, 0.8),
                        hexToRgba(colors.primaryColor, 0.8),
                        hexToRgba(colors.primaryColor, 0.8),
                        hexToRgba(colors.errorColor, 0.8), // Friday - highlighted in red
                        hexToRgba(colors.primaryColor, 0.8),
                        hexToRgba(colors.primaryColor, 0.8)
                    ],
                    borderColor: [
                        colors.primaryColor,
                        colors.primaryColor,
                        colors.primaryColor,
                        colors.primaryColor,
                        colors.errorColor,
                        colors.primaryColor,
                        colors.primaryColor
                    ],
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                return 'Incidents: ' + context.parsed.y;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: colors.textColor,
                            font: {
                                size: 12
                            },
                            stepSize: 10
                        },
                        grid: {
                            color: colors.gridColor
                        }
                    },
                    x: {
                        ticks: {
                            color: colors.textColor,
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // 4. Hourly Line Chart (24 hours)
        const hourlyCtx = document.getElementById('hourlyLineChart').getContext('2d');
        hourlyLineChart = new Chart(hourlyCtx, {
            type: 'line',
            data: {
                labels: ['12AM', '3AM', '6AM', '9AM', '12PM', '3PM', '6PM', '9PM'],
                datasets: [{
                    label: 'Average Incidents',
                    data: [8, 5, 12, 18, 28, 35, 47, 42],
                    borderColor: colors.errorColor,
                    backgroundColor: hexToRgba(colors.errorColor, 0.1),
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    pointBackgroundColor: colors.errorColor,
                    pointBorderColor: colors.cardBg,
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            color: colors.textColor,
                            font: {
                                size: 14,
                                weight: '600'
                            },
                            padding: 15
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                return 'Incidents: ' + context.parsed.y;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: colors.textColor,
                            font: {
                                size: 12
                            },
                            stepSize: 10
                        },
                        grid: {
                            color: colors.gridColor
                        }
                    },
                    x: {
                        ticks: {
                            color: colors.textColor,
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
        }

        // Initialize charts on page load
        document.addEventListener('DOMContentLoaded', function() {
            initializeCharts();
        });

        // Listen for theme changes and reinitialize charts
        // This observer watches for class changes on the html or body element
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'class' || mutation.attributeName === 'data-theme') {
                    // Small delay to ensure CSS variables are updated
                    setTimeout(() => {
                        initializeCharts();
                    }, 50);
                }
            });
        });

        // Observe both html and body for theme changes
        observer.observe(document.documentElement, { attributes: true });
        if (document.body) {
            observer.observe(document.body, { attributes: true });
        }
    </script>
</body>
</html>