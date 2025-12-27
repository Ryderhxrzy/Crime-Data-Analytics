<?php
// Authentication check - must be at the top of every admin page
require_once '../../api/middleware/auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Time-Based Trends - Crime Data Analytics</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/admin-header.css">
    <link rel="stylesheet" href="../css/hero.css">
    <link rel="stylesheet" href="../css/sidebar-footer.css">
    <link rel="stylesheet" href="../css/timebased-trend.css">
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
                            <span>Time-Based Trends</span>
                        </li>
                    </ol>
                </nav>
                <h1>Time-Based Trends</h1>
                <p>Analyze crime patterns across different time periods. Track hourly, daily, and monthly trends to identify when crimes usually occur and compare activity between different time frames.</p>
            </div>

            <div class="sub-container">
                <div class="page-content">
                    <!-- Hourly Crime Distribution -->
                    <div class="chart-grid-single">
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title">Crime Distribution by Hour (24-Hour Analysis)</h3>
                                <div class="chart-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                            <div class="chart-canvas-container">
                                <canvas id="hourlyTrendChart"></canvas>
                            </div>
                            <div class="chart-footer-text">
                                Peak hours: 6PM - 9PM (avg 47 incidents/hour) • Lowest: 3AM - 6AM (avg 5 incidents/hour)
                            </div>
                        </div>
                    </div>

                    <!-- Daily Crime Distribution -->
                    <div class="chart-grid-single">
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title">Crime Distribution by Day of Week</h3>
                                <div class="chart-icon">
                                    <i class="fas fa-calendar-week"></i>
                                </div>
                            </div>
                            <div class="chart-canvas-container">
                                <canvas id="dailyTrendChart"></canvas>
                            </div>
                            <div class="chart-footer-text">
                                Highest crime day: Friday (67 incidents) • Lowest: Sunday (36 incidents)
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Crime Trend -->
                    <div class="chart-grid-single">
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title">Monthly Crime Trend (Last 12 Months)</h3>
                                <div class="chart-icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                            </div>
                            <div class="chart-canvas-container">
                                <canvas id="monthlyTrendChart"></canvas>
                            </div>
                            <div class="chart-footer-text">
                                Peak month: June 2024 (1,892 incidents) • Overall trend: +12.3% compared to last year
                            </div>
                        </div>
                    </div>

                    <!-- Time Period Comparison -->
                    <div class="chart-grid-double">
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title">Quarter-over-Quarter Comparison</h3>
                                <div class="chart-icon">
                                    <i class="fas fa-chart-bar"></i>
                                </div>
                            </div>
                            <div class="chart-canvas-container medium">
                                <canvas id="quarterComparisonChart"></canvas>
                            </div>
                            <div class="chart-footer-text">
                                Q2 2024 shows highest crime rate with 4,415 total incidents
                            </div>
                        </div>

                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title">Year-over-Year Comparison</h3>
                                <div class="chart-icon">
                                    <i class="fas fa-chart-area"></i>
                                </div>
                            </div>
                            <div class="chart-canvas-container medium">
                                <canvas id="yearComparisonChart"></canvas>
                            </div>
                            <div class="chart-footer-text">
                                2024 shows 12.3% increase compared to 2023
                            </div>
                        </div>
                    </div>

                    <!-- Time-Based Insights -->
                    <div class="insights-section">
                        <div class="chart-header">
                            <h3 class="chart-title">Time-Based Insights</h3>
                            <div class="chart-icon">
                                <i class="fas fa-lightbulb"></i>
                            </div>
                        </div>
                        <div class="insights-grid">
                            <div class="insight-card">
                                <div class="insight-icon">
                                    <i class="fas fa-sun"></i>
                                </div>
                                <div class="insight-content">
                                    <h4>Peak Crime Time</h4>
                                    <p class="insight-value">6:00 PM - 9:00 PM</p>
                                    <p class="insight-description">Evening hours show the highest crime activity, accounting for 23% of daily incidents.</p>
                                </div>
                            </div>

                            <div class="insight-card">
                                <div class="insight-icon">
                                    <i class="fas fa-moon"></i>
                                </div>
                                <div class="insight-content">
                                    <h4>Safest Time</h4>
                                    <p class="insight-value">3:00 AM - 6:00 AM</p>
                                    <p class="insight-description">Early morning hours have the lowest crime rate, with only 2% of daily incidents.</p>
                                </div>
                            </div>

                            <div class="insight-card">
                                <div class="insight-icon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <div class="insight-content">
                                    <h4>Most Active Day</h4>
                                    <p class="insight-value">Friday</p>
                                    <p class="insight-description">Friday consistently shows 18% more incidents than the weekly average.</p>
                                </div>
                            </div>

                            <div class="insight-card">
                                <div class="insight-icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div class="insight-content">
                                    <h4>Trending Pattern</h4>
                                    <p class="insight-value">+12.3% YoY</p>
                                    <p class="insight-description">Overall upward trend observed compared to previous year, requiring increased vigilance.</p>
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
        let hourlyTrendChart, dailyTrendChart, monthlyTrendChart, quarterComparisonChart, yearComparisonChart;

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

            if (hourlyTrendChart) hourlyTrendChart.destroy();
            if (dailyTrendChart) dailyTrendChart.destroy();
            if (monthlyTrendChart) monthlyTrendChart.destroy();
            if (quarterComparisonChart) quarterComparisonChart.destroy();
            if (yearComparisonChart) yearComparisonChart.destroy();

            // Hourly Trend Chart
            const hourlyCtx = document.getElementById('hourlyTrendChart').getContext('2d');
            hourlyTrendChart = new Chart(hourlyCtx, {
                type: 'line',
                data: {
                    labels: ['12AM', '1AM', '2AM', '3AM', '4AM', '5AM', '6AM', '7AM', '8AM', '9AM', '10AM', '11AM',
                             '12PM', '1PM', '2PM', '3PM', '4PM', '5PM', '6PM', '7PM', '8PM', '9PM', '10PM', '11PM'],
                    datasets: [{
                        label: 'Crime Incidents',
                        data: [8, 6, 4, 5, 6, 9, 12, 15, 18, 22, 26, 28, 32, 35, 38, 42, 45, 46, 47, 45, 42, 38, 28, 15],
                        borderColor: colors.errorColor,
                        backgroundColor: hexToRgba(colors.errorColor, 0.1),
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
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
                            labels: { color: colors.textColor, font: { size: 14, weight: '600' }, padding: 15 }
                        },
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
                            ticks: { color: colors.textColor, font: { size: 11 } },
                            grid: { display: false }
                        }
                    }
                }
            });

            // Daily Trend Chart
            const dailyCtx = document.getElementById('dailyTrendChart').getContext('2d');
            dailyTrendChart = new Chart(dailyCtx, {
                type: 'bar',
                data: {
                    labels: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                    datasets: [{
                        label: 'Crime Incidents',
                        data: [42, 38, 48, 52, 67, 59, 36],
                        backgroundColor: [
                            hexToRgba(colors.primaryColor, 0.8),
                            hexToRgba(colors.primaryColor, 0.8),
                            hexToRgba(colors.primaryColor, 0.8),
                            hexToRgba(colors.primaryColor, 0.8),
                            hexToRgba(colors.errorColor, 0.8),
                            hexToRgba(colors.primaryColor, 0.8),
                            hexToRgba(colors.successColor, 0.8)
                        ],
                        borderColor: [
                            colors.primaryColor, colors.primaryColor, colors.primaryColor,
                            colors.primaryColor, colors.errorColor, colors.primaryColor, colors.successColor
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

            // Monthly Trend Chart
            const monthlyCtx = document.getElementById('monthlyTrendChart').getContext('2d');
            monthlyTrendChart = new Chart(monthlyCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: '2024',
                        data: [1247, 1089, 1378, 1034, 1489, 1892, 1654, 1523, 1445, 1389, 1267, 1198],
                        borderColor: colors.primaryColor,
                        backgroundColor: hexToRgba(colors.primaryColor, 0.1),
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: { color: colors.textColor, font: { size: 14, weight: '600' }, padding: 15 }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            callbacks: { label: (context) => 'Incidents: ' + context.parsed.y.toLocaleString() }
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

            // Quarter Comparison Chart
            const quarterCtx = document.getElementById('quarterComparisonChart').getContext('2d');
            quarterComparisonChart = new Chart(quarterCtx, {
                type: 'bar',
                data: {
                    labels: ['Q1 2024', 'Q2 2024', 'Q3 2024', 'Q4 2024'],
                    datasets: [{
                        label: 'Total Incidents',
                        data: [3714, 4415, 4622, 3854],
                        backgroundColor: hexToRgba(colors.warningColor, 0.8),
                        borderColor: colors.warningColor,
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
                            callbacks: { label: (context) => 'Incidents: ' + context.parsed.y.toLocaleString() }
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

            // Year Comparison Chart
            const yearCtx = document.getElementById('yearComparisonChart').getContext('2d');
            yearComparisonChart = new Chart(yearCtx, {
                type: 'bar',
                data: {
                    labels: ['2022', '2023', '2024'],
                    datasets: [{
                        label: 'Annual Incidents',
                        data: [14235, 14892, 16605],
                        backgroundColor: [
                            hexToRgba(colors.successColor, 0.8),
                            hexToRgba(colors.warningColor, 0.8),
                            hexToRgba(colors.errorColor, 0.8)
                        ],
                        borderColor: [colors.successColor, colors.warningColor, colors.errorColor],
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
                            callbacks: { label: (context) => 'Incidents: ' + context.parsed.y.toLocaleString() }
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