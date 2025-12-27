<?php
// Authentication check - must be at the top of every admin page
require_once '../../api/middleware/auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crime Type Trends - Crime Data Analytics</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/admin-header.css">
    <link rel="stylesheet" href="../css/hero.css">
    <link rel="stylesheet" href="../css/sidebar-footer.css">
    <link rel="stylesheet" href="../css/crime-type-trend.css">
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
                            <span>Crime Type Trends</span>
                        </li>
                    </ol>
                </nav>
                <h1>Crime Type Trends</h1>
                <p>Analyze trends for specific crime categories to identify patterns and emerging threats. Track increase or decrease indicators to determine which crime types require immediate attention and resources.</p>
            </div>

            <div class="sub-container">
                <div class="page-content">
                    <!-- Overall Crime Type Distribution -->
                    <div class="chart-grid-single">
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title">Crime Type Distribution Overview</h3>
                                <div class="chart-icon">
                                    <i class="fas fa-chart-pie"></i>
                                </div>
                            </div>
                            <div class="chart-canvas-container medium">
                                <canvas id="crimeTypeOverviewChart"></canvas>
                            </div>
                            <div class="chart-footer-text">
                                Total of 16,605 crimes recorded this year across all categories
                            </div>
                        </div>
                    </div>

                    <!-- Crime Type Trends Over Time -->
                    <div class="chart-grid-single">
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title">Crime Type Trends (6-Month Comparison)</h3>
                                <div class="chart-icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                            </div>
                            <div class="chart-canvas-container large">
                                <canvas id="crimeTypeTrendChart"></canvas>
                            </div>
                            <div class="chart-footer-text">
                                Multi-category trend analysis showing increase/decrease patterns over time
                            </div>
                        </div>
                    </div>

                    <!-- Crime Type Change Indicators -->
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3 class="chart-title">Crime Type Change Indicators</h3>
                            <div class="chart-icon">
                                <i class="fas fa-arrows-alt-v"></i>
                            </div>
                        </div>
                        <div class="crime-type-list">
                            <div class="crime-type-card rising">
                                <div class="crime-type-header">
                                    <div class="crime-type-icon">
                                        <i class="fas fa-hand-holding-usd"></i>
                                    </div>
                                    <div class="crime-type-info">
                                        <h4>Theft & Robbery</h4>
                                        <p>Includes burglary, shoplifting, pickpocketing, and armed robbery</p>
                                    </div>
                                </div>
                                <div class="crime-type-stats">
                                    <div class="stat-item">
                                        <span class="stat-label">Total Incidents</span>
                                        <span class="stat-value">3,542</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Monthly Average</span>
                                        <span class="stat-value">295</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Trend</span>
                                        <span class="stat-value trend-up">
                                            <i class="fas fa-arrow-up"></i> +8.3%
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="crime-type-card rising">
                                <div class="crime-type-header">
                                    <div class="crime-type-icon">
                                        <i class="fas fa-door-open"></i>
                                    </div>
                                    <div class="crime-type-info">
                                        <h4>Burglary & Breaking & Entering</h4>
                                        <p>Residential and commercial break-ins, property intrusion</p>
                                    </div>
                                </div>
                                <div class="crime-type-stats">
                                    <div class="stat-item">
                                        <span class="stat-label">Total Incidents</span>
                                        <span class="stat-value">2,584</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Monthly Average</span>
                                        <span class="stat-value">215</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Trend</span>
                                        <span class="stat-value trend-up">
                                            <i class="fas fa-arrow-up"></i> +6.7%
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="crime-type-card declining">
                                <div class="crime-type-header">
                                    <div class="crime-type-icon">
                                        <i class="fas fa-user-injured"></i>
                                    </div>
                                    <div class="crime-type-info">
                                        <h4>Assault & Battery</h4>
                                        <p>Physical altercations, domestic violence, aggravated assault</p>
                                    </div>
                                </div>
                                <div class="crime-type-stats">
                                    <div class="stat-item">
                                        <span class="stat-label">Total Incidents</span>
                                        <span class="stat-value">2,156</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Monthly Average</span>
                                        <span class="stat-value">180</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Trend</span>
                                        <span class="stat-value trend-down">
                                            <i class="fas fa-arrow-down"></i> -3.7%
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="crime-type-card rising">
                                <div class="crime-type-header">
                                    <div class="crime-type-icon">
                                        <i class="fas fa-spray-can"></i>
                                    </div>
                                    <div class="crime-type-info">
                                        <h4>Vandalism & Property Damage</h4>
                                        <p>Graffiti, destruction of property, malicious damage</p>
                                    </div>
                                </div>
                                <div class="crime-type-stats">
                                    <div class="stat-item">
                                        <span class="stat-label">Total Incidents</span>
                                        <span class="stat-value">1,876</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Monthly Average</span>
                                        <span class="stat-value">156</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Trend</span>
                                        <span class="stat-value trend-up">
                                            <i class="fas fa-arrow-up"></i> +11.2%
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="crime-type-card rising">
                                <div class="crime-type-header">
                                    <div class="crime-type-icon">
                                        <i class="fas fa-car"></i>
                                    </div>
                                    <div class="crime-type-info">
                                        <h4>Vehicle Crimes</h4>
                                        <p>Auto theft, carjacking, vehicle break-ins</p>
                                    </div>
                                </div>
                                <div class="crime-type-stats">
                                    <div class="stat-item">
                                        <span class="stat-label">Total Incidents</span>
                                        <span class="stat-value">1,234</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Monthly Average</span>
                                        <span class="stat-value">103</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Trend</span>
                                        <span class="stat-value trend-up">
                                            <i class="fas fa-arrow-up"></i> +12.5%
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="crime-type-card stable">
                                <div class="crime-type-header">
                                    <div class="crime-type-icon">
                                        <i class="fas fa-pills"></i>
                                    </div>
                                    <div class="crime-type-info">
                                        <h4>Drug-Related Crimes</h4>
                                        <p>Drug possession, trafficking, drug-related offenses</p>
                                    </div>
                                </div>
                                <div class="crime-type-stats">
                                    <div class="stat-item">
                                        <span class="stat-label">Total Incidents</span>
                                        <span class="stat-value">892</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Monthly Average</span>
                                        <span class="stat-value">74</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Trend</span>
                                        <span class="stat-value trend-stable">
                                            <i class="fas fa-minus"></i> +0.2%
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="crime-type-card declining">
                                <div class="crime-type-header">
                                    <div class="crime-type-icon">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </div>
                                    <div class="crime-type-info">
                                        <h4>Other Crimes</h4>
                                        <p>Fraud, cybercrime, public disturbance, and miscellaneous offenses</p>
                                    </div>
                                </div>
                                <div class="crime-type-stats">
                                    <div class="stat-item">
                                        <span class="stat-label">Total Incidents</span>
                                        <span class="stat-value">563</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Monthly Average</span>
                                        <span class="stat-value">47</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Trend</span>
                                        <span class="stat-value trend-down">
                                            <i class="fas fa-arrow-down"></i> -4.8%
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Most Frequent Crimes -->
                    <div class="chart-grid-double">
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title">Most Frequent Crime Types (This Month)</h3>
                                <div class="chart-icon">
                                    <i class="fas fa-sort-amount-down"></i>
                                </div>
                            </div>
                            <div class="chart-canvas-container medium">
                                <canvas id="frequentCrimesChart"></canvas>
                            </div>
                            <div class="chart-footer-text">
                                Top 7 crime types by monthly incident count
                            </div>
                        </div>

                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title">Emerging Crime Threats</h3>
                                <div class="chart-icon">
                                    <i class="fas fa-exclamation-circle"></i>
                                </div>
                            </div>
                            <div class="emerging-threats">
                                <div class="threat-item high">
                                    <div class="threat-icon">
                                        <i class="fas fa-arrow-up"></i>
                                    </div>
                                    <div class="threat-content">
                                        <h5>Vehicle Crimes</h5>
                                        <p>12.5% increase - Requires immediate attention</p>
                                    </div>
                                </div>
                                <div class="threat-item high">
                                    <div class="threat-icon">
                                        <i class="fas fa-arrow-up"></i>
                                    </div>
                                    <div class="threat-content">
                                        <h5>Vandalism</h5>
                                        <p>11.2% increase - Growing concern in public areas</p>
                                    </div>
                                </div>
                                <div class="threat-item medium">
                                    <div class="threat-icon">
                                        <i class="fas fa-arrow-up"></i>
                                    </div>
                                    <div class="threat-content">
                                        <h5>Theft & Robbery</h5>
                                        <p>8.3% increase - Concentrated in commercial districts</p>
                                    </div>
                                </div>
                                <div class="threat-item low">
                                    <div class="threat-icon">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="threat-content">
                                        <h5>Assault Cases</h5>
                                        <p>3.7% decrease - Positive trend, continue monitoring</p>
                                    </div>
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
        let crimeTypeOverviewChart, crimeTypeTrendChart, frequentCrimesChart;

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

            if (crimeTypeOverviewChart) crimeTypeOverviewChart.destroy();
            if (crimeTypeTrendChart) crimeTypeTrendChart.destroy();
            if (frequentCrimesChart) frequentCrimesChart.destroy();

            // Crime Type Overview Pie Chart
            const overviewCtx = document.getElementById('crimeTypeOverviewChart').getContext('2d');
            crimeTypeOverviewChart = new Chart(overviewCtx, {
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
                            '#ec4899',
                            colors.successColor,
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
                                font: { size: 13 },
                                padding: 15,
                                generateLabels: function(chart) {
                                    const data = chart.data;
                                    return data.labels.map((label, i) => {
                                        const value = data.datasets[0].data[i];
                                        const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return {
                                            text: `${label}: ${value.toLocaleString()} (${percentage}%)`,
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

            // Crime Type Trend Line Chart
            const trendCtx = document.getElementById('crimeTypeTrendChart').getContext('2d');
            crimeTypeTrendChart = new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: ['January', 'February', 'March', 'April', 'May', 'June'],
                    datasets: [
                        {
                            label: 'Theft & Robbery',
                            data: [542, 567, 589, 601, 623, 620],
                            borderColor: colors.errorColor,
                            backgroundColor: hexToRgba(colors.errorColor, 0.1),
                            borderWidth: 2,
                            tension: 0.4,
                            fill: false
                        },
                        {
                            label: 'Burglary',
                            data: [398, 412, 425, 437, 445, 467],
                            borderColor: colors.warningColor,
                            backgroundColor: hexToRgba(colors.warningColor, 0.1),
                            borderWidth: 2,
                            tension: 0.4,
                            fill: false
                        },
                        {
                            label: 'Assault',
                            data: [378, 371, 365, 358, 352, 332],
                            borderColor: '#8b5cf6',
                            backgroundColor: 'rgba(139, 92, 246, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: false
                        },
                        {
                            label: 'Vandalism',
                            data: [289, 298, 312, 325, 337, 315],
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: false
                        },
                        {
                            label: 'Vehicle Crimes',
                            data: [178, 189, 201, 212, 225, 229],
                            borderColor: '#ec4899',
                            backgroundColor: 'rgba(236, 72, 153, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: false
                        },
                        {
                            label: 'Drug-Related',
                            data: [148, 149, 150, 149, 148, 148],
                            borderColor: colors.successColor,
                            backgroundColor: hexToRgba(colors.successColor, 0.1),
                            borderWidth: 2,
                            tension: 0.4,
                            fill: false
                        },
                        {
                            label: 'Others',
                            data: [98, 95, 92, 89, 95, 94],
                            borderColor: colors.textSecondary,
                            backgroundColor: hexToRgba(colors.textSecondary, 0.1),
                            borderWidth: 2,
                            tension: 0.4,
                            fill: false
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
                            labels: {
                                color: colors.textColor,
                                font: { size: 13 },
                                padding: 12,
                                usePointStyle: true
                            }
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
                            ticks: { color: colors.textColor, font: { size: 12 } },
                            grid: { display: false }
                        }
                    }
                }
            });

            // Frequent Crimes Bar Chart
            const frequentCtx = document.getElementById('frequentCrimesChart').getContext('2d');
            frequentCrimesChart = new Chart(frequentCtx, {
                type: 'bar',
                data: {
                    labels: ['Theft', 'Burglary', 'Assault', 'Vandalism', 'Vehicle', 'Drug', 'Others'],
                    datasets: [{
                        label: 'Monthly Incidents',
                        data: [620, 467, 332, 315, 229, 148, 94],
                        backgroundColor: [
                            hexToRgba(colors.errorColor, 0.8),
                            hexToRgba(colors.warningColor, 0.8),
                            'rgba(139, 92, 246, 0.8)',
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(236, 72, 153, 0.8)',
                            hexToRgba(colors.successColor, 0.8),
                            hexToRgba(colors.textSecondary, 0.8)
                        ],
                        borderColor: [
                            colors.errorColor,
                            colors.warningColor,
                            '#8b5cf6',
                            '#3b82f6',
                            '#ec4899',
                            colors.successColor,
                            colors.textSecondary
                        ],
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            callbacks: { label: (context) => 'Incidents: ' + context.parsed.y }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { color: colors.textColor, font: { size: 12 } },
                            grid: { color: colors.gridColor }
                        },
                        x: {
                            ticks: { color: colors.textColor, font: { size: 12 } },
                            grid: { display: false }
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