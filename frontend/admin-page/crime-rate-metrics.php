<?php
// Authentication check - must be at the top of every admin page
require_once '../../api/middleware/auth.php';
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
    <style>
        .metrics-card {
            background: var(--card-bg-1);
            border: 1px solid var(--border-color-1);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .metrics-card-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .metrics-card-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .metrics-card-icon.danger { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
        .metrics-card-icon.warning { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        .metrics-card-icon.info { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
        .metrics-card-icon.success { background: rgba(34, 197, 94, 0.1); color: #22c55e; }
        .metrics-card-icon.primary { background: rgba(76, 138, 137, 0.1); color: #4c8a89; }

        .metrics-card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-color-1);
            margin: 0;
        }

        .metrics-card-subtitle {
            font-size: 0.875rem;
            color: var(--text-secondary-1);
            margin: 0;
        }

        .metrics-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .metrics-stat {
            background: var(--card-bg-1);
            border: 1px solid var(--border-color-1);
            border-radius: 12px;
            padding: 1.5rem;
        }

        .metrics-stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.75rem;
        }

        .metrics-stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .metrics-stat-change {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .metrics-stat-change.up { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
        .metrics-stat-change.down { background: rgba(34, 197, 94, 0.1); color: #22c55e; }
        .metrics-stat-change.neutral { background: rgba(107, 114, 128, 0.1); color: #6b7280; }

        .metrics-stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-color-1);
            margin-bottom: 0.25rem;
        }

        .metrics-stat-label {
            font-size: 0.875rem;
            color: var(--text-secondary-1);
        }

        .chart-container {
            position: relative;
            height: 350px;
            margin-top: 1rem;
        }

        .chart-container-small {
            position: relative;
            height: 280px;
            margin-top: 1rem;
        }

        .two-column-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .three-column-layout {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
        }

        .district-comparison-table {
            width: 100%;
            border-collapse: collapse;
        }

        .district-comparison-table th,
        .district-comparison-table td {
            padding: 0.875rem 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color-1);
        }

        .district-comparison-table th {
            font-weight: 600;
            color: var(--text-color-1);
            background: var(--bg-color-1);
            font-size: 0.85rem;
        }

        .district-comparison-table td {
            color: var(--text-secondary-1);
        }

        .district-comparison-table tr:hover {
            background: var(--bg-color-1);
        }

        .rate-badge {
            padding: 0.375rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .rate-badge.high { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
        .rate-badge.medium { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        .rate-badge.low { background: rgba(34, 197, 94, 0.1); color: #22c55e; }

        .change-indicator {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.8rem;
        }

        .change-indicator.up { color: #ef4444; }
        .change-indicator.down { color: #22c55e; }
        .change-indicator.neutral { color: #6b7280; }

        .population-input-section {
            background: var(--bg-color-1);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .population-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .population-input-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .population-input-group label {
            font-size: 0.8rem;
            color: var(--text-secondary-1);
            font-weight: 500;
        }

        .population-input-group input {
            padding: 0.5rem 0.75rem;
            border: 1px solid var(--border-color-1);
            border-radius: 6px;
            background: var(--card-bg-1);
            color: var(--text-color-1);
            font-size: 0.9rem;
        }

        .calculation-formula {
            background: var(--bg-color-1);
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1rem;
            font-family: monospace;
            font-size: 0.9rem;
            color: var(--text-secondary-1);
        }

        .calculation-formula code {
            color: var(--primary-color-1);
            font-weight: 600;
        }

        .mini-sparkline {
            display: flex;
            align-items: flex-end;
            gap: 2px;
            height: 30px;
        }

        .mini-sparkline-bar {
            width: 6px;
            border-radius: 2px;
            transition: height 0.3s ease;
        }

        @media (max-width: 1200px) {
            .three-column-layout { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 992px) {
            .two-column-layout,
            .three-column-layout { grid-template-columns: 1fr; }
        }
    </style>
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
                            <a href="/" class="breadcrumb-link"><span>Home</span></a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="/analytics" class="breadcrumb-link"><span>Key Metrics</span></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <span>Crime Rate Metrics</span>
                        </li>
                    </ol>
                </nav>
                <h1>Crime Rate Metrics - Quezon City</h1>
                <p>Comprehensive crime rate analysis per 1,000 population. Track crime rate trends, compare districts, and monitor percentage changes over time.</p>
            </div>

            <div class="sub-container">
                <div class="page-content">
                    <!-- Key Crime Rate Statistics -->
                    <div class="metrics-stats">
                        <div class="metrics-stat">
                            <div class="metrics-stat-header">
                                <div class="metrics-stat-icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <span class="metrics-stat-change up">
                                    <i class="fas fa-arrow-up"></i> +5.2%
                                </span>
                            </div>
                            <div class="metrics-stat-value">8.47</div>
                            <div class="metrics-stat-label">Crime Rate per 1,000 Population</div>
                        </div>

                        <div class="metrics-stat">
                            <div class="metrics-stat-header">
                                <div class="metrics-stat-icon" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                                    <i class="fas fa-users"></i>
                                </div>
                                <span class="metrics-stat-change neutral">
                                    <i class="fas fa-minus"></i> 0%
                                </span>
                            </div>
                            <div class="metrics-stat-value">2.96M</div>
                            <div class="metrics-stat-label">Total Population</div>
                        </div>

                        <div class="metrics-stat">
                            <div class="metrics-stat-header">
                                <div class="metrics-stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <span class="metrics-stat-change up">
                                    <i class="fas fa-arrow-up"></i> +3.8%
                                </span>
                            </div>
                            <div class="metrics-stat-value">25,071</div>
                            <div class="metrics-stat-label">Total Crimes (2025)</div>
                        </div>

                        <div class="metrics-stat">
                            <div class="metrics-stat-header">
                                <div class="metrics-stat-icon" style="background: rgba(34, 197, 94, 0.1); color: #22c55e;">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <span class="metrics-stat-change down">
                                    <i class="fas fa-arrow-down"></i> -2.1%
                                </span>
                            </div>
                            <div class="metrics-stat-value">4.23</div>
                            <div class="metrics-stat-label">Violent Crime Rate</div>
                        </div>

                        <div class="metrics-stat">
                            <div class="metrics-stat-header">
                                <div class="metrics-stat-icon" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                                    <i class="fas fa-home"></i>
                                </div>
                                <span class="metrics-stat-change up">
                                    <i class="fas fa-arrow-up"></i> +8.5%
                                </span>
                            </div>
                            <div class="metrics-stat-value">4.24</div>
                            <div class="metrics-stat-label">Property Crime Rate</div>
                        </div>
                    </div>

                    <!-- Crime Rate Trend Chart -->
                    <div class="metrics-card">
                        <div class="metrics-card-header">
                            <div class="metrics-card-icon info">
                                <i class="fas fa-chart-area"></i>
                            </div>
                            <div>
                                <h3 class="metrics-card-title">Crime Rate Trends</h3>
                                <p class="metrics-card-subtitle">Crime rate per 1,000 population over the last 12 months</p>
                            </div>
                        </div>
                        <div class="chart-container">
                            <canvas id="crimeRateTrendChart"></canvas>
                        </div>
                    </div>

                    <div class="two-column-layout">
                        <!-- Crime Rate by District -->
                        <div class="metrics-card">
                            <div class="metrics-card-header">
                                <div class="metrics-card-icon warning">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div>
                                    <h3 class="metrics-card-title">Crime Rate by District</h3>
                                    <p class="metrics-card-subtitle">Comparison of crime rates across districts</p>
                                </div>
                            </div>
                            <div class="chart-container-small">
                                <canvas id="districtComparisonChart"></canvas>
                            </div>
                        </div>

                        <!-- Crime Type Distribution -->
                        <div class="metrics-card">
                            <div class="metrics-card-header">
                                <div class="metrics-card-icon danger">
                                    <i class="fas fa-chart-pie"></i>
                                </div>
                                <div>
                                    <h3 class="metrics-card-title">Crime Rate by Type</h3>
                                    <p class="metrics-card-subtitle">Distribution of crime rates by category</p>
                                </div>
                            </div>
                            <div class="chart-container-small">
                                <canvas id="crimeTypeRateChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- District Comparison Table -->
                    <div class="metrics-card">
                        <div class="metrics-card-header">
                            <div class="metrics-card-icon primary">
                                <i class="fas fa-table"></i>
                            </div>
                            <div>
                                <h3 class="metrics-card-title">District Crime Rate Comparison</h3>
                                <p class="metrics-card-subtitle">Detailed breakdown by district with population data</p>
                            </div>
                        </div>
                        <table class="district-comparison-table">
                            <thead>
                                <tr>
                                    <th>District/Barangay</th>
                                    <th>Population</th>
                                    <th>Total Crimes</th>
                                    <th>Crime Rate</th>
                                    <th>Change</th>
                                    <th>Risk Level</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Cubao</strong></td>
                                    <td>185,420</td>
                                    <td>2,847</td>
                                    <td><strong>15.35</strong></td>
                                    <td>
                                        <span class="change-indicator up">
                                            <i class="fas fa-arrow-up"></i> +12.3%
                                        </span>
                                    </td>
                                    <td><span class="rate-badge high">High</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Novaliches</strong></td>
                                    <td>312,850</td>
                                    <td>3,892</td>
                                    <td><strong>12.44</strong></td>
                                    <td>
                                        <span class="change-indicator up">
                                            <i class="fas fa-arrow-up"></i> +8.7%
                                        </span>
                                    </td>
                                    <td><span class="rate-badge high">High</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Commonwealth</strong></td>
                                    <td>245,300</td>
                                    <td>2,410</td>
                                    <td><strong>9.82</strong></td>
                                    <td>
                                        <span class="change-indicator up">
                                            <i class="fas fa-arrow-up"></i> +4.2%
                                        </span>
                                    </td>
                                    <td><span class="rate-badge medium">Medium</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Fairview</strong></td>
                                    <td>289,670</td>
                                    <td>2,156</td>
                                    <td><strong>7.44</strong></td>
                                    <td>
                                        <span class="change-indicator down">
                                            <i class="fas fa-arrow-down"></i> -2.1%
                                        </span>
                                    </td>
                                    <td><span class="rate-badge medium">Medium</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Diliman</strong></td>
                                    <td>198,450</td>
                                    <td>1,245</td>
                                    <td><strong>6.27</strong></td>
                                    <td>
                                        <span class="change-indicator down">
                                            <i class="fas fa-arrow-down"></i> -5.4%
                                        </span>
                                    </td>
                                    <td><span class="rate-badge low">Low</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Project 4</strong></td>
                                    <td>156,780</td>
                                    <td>1,478</td>
                                    <td><strong>9.43</strong></td>
                                    <td>
                                        <span class="change-indicator up">
                                            <i class="fas fa-arrow-up"></i> +6.8%
                                        </span>
                                    </td>
                                    <td><span class="rate-badge medium">Medium</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Batasan Hills</strong></td>
                                    <td>178,920</td>
                                    <td>1,089</td>
                                    <td><strong>6.09</strong></td>
                                    <td>
                                        <span class="change-indicator neutral">
                                            <i class="fas fa-minus"></i> 0%
                                        </span>
                                    </td>
                                    <td><span class="rate-badge low">Low</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Kamuning</strong></td>
                                    <td>124,560</td>
                                    <td>892</td>
                                    <td><strong>7.16</strong></td>
                                    <td>
                                        <span class="change-indicator down">
                                            <i class="fas fa-arrow-down"></i> -3.2%
                                        </span>
                                    </td>
                                    <td><span class="rate-badge medium">Medium</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="two-column-layout">
                        <!-- Year-over-Year Comparison -->
                        <div class="metrics-card">
                            <div class="metrics-card-header">
                                <div class="metrics-card-icon success">
                                    <i class="fas fa-exchange-alt"></i>
                                </div>
                                <div>
                                    <h3 class="metrics-card-title">Year-over-Year Comparison</h3>
                                    <p class="metrics-card-subtitle">Crime rate changes compared to previous year</p>
                                </div>
                            </div>
                            <div class="chart-container-small">
                                <canvas id="yearOverYearChart"></canvas>
                            </div>
                        </div>

                        <!-- Calculation Formula -->
                        <div class="metrics-card">
                            <div class="metrics-card-header">
                                <div class="metrics-card-icon info">
                                    <i class="fas fa-calculator"></i>
                                </div>
                                <div>
                                    <h3 class="metrics-card-title">Rate Calculation</h3>
                                    <p class="metrics-card-subtitle">How crime rate is calculated</p>
                                </div>
                            </div>
                            <div class="calculation-formula">
                                <p><strong>Formula:</strong></p>
                                <code>Crime Rate = (Total Crimes / Population) × 1,000</code>
                                <br><br>
                                <p><strong>Example (Quezon City 2025):</strong></p>
                                <code>(25,071 / 2,960,000) × 1,000 = 8.47 per 1,000</code>
                            </div>
                            <div class="population-input-section" style="margin-top: 1.5rem;">
                                <h4 style="margin: 0 0 1rem 0; color: var(--text-color-1); font-size: 0.95rem;">Population Data (2025 Census)</h4>
                                <div class="population-form">
                                    <div class="population-input-group">
                                        <label>Total Population</label>
                                        <input type="text" value="2,960,000" readonly>
                                    </div>
                                    <div class="population-input-group">
                                        <label>Population Growth Rate</label>
                                        <input type="text" value="1.12%" readonly>
                                    </div>
                                    <div class="population-input-group">
                                        <label>Last Census Date</label>
                                        <input type="text" value="May 2020" readonly>
                                    </div>
                                    <div class="population-input-group">
                                        <label>Data Source</label>
                                        <input type="text" value="PSA Projection" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Percentage Change Analysis -->
                    <div class="metrics-card">
                        <div class="metrics-card-header">
                            <div class="metrics-card-icon warning">
                                <i class="fas fa-percentage"></i>
                            </div>
                            <div>
                                <h3 class="metrics-card-title">Percentage Change Analysis</h3>
                                <p class="metrics-card-subtitle">Monthly percentage changes in crime rates</p>
                            </div>
                        </div>
                        <div class="chart-container">
                            <canvas id="percentageChangeChart"></canvas>
                        </div>
                    </div>

                    <!-- Legend -->
                    <div class="legend">
                        <div class="legend-header">
                            <h3><i class="fas fa-info-circle"></i> Crime Rate Legend</h3>
                        </div>
                        <div class="legend-grid">
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #ef4444;"></div>
                                <span class="legend-label">High Risk (Rate > 10 per 1,000)</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #f59e0b;"></div>
                                <span class="legend-label">Medium Risk (Rate 5-10 per 1,000)</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #22c55e;"></div>
                                <span class="legend-label">Low Risk (Rate < 5 per 1,000)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include '../includes/admin-footer.php' ?>
    </div>

    <script>
        // Crime Rate Trend Chart
        const trendCtx = document.getElementById('crimeRateTrendChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Overall Crime Rate',
                    data: [7.8, 8.1, 8.3, 8.0, 8.5, 8.9, 9.2, 8.8, 8.4, 8.2, 8.5, 8.47],
                    borderColor: '#4c8a89',
                    backgroundColor: 'rgba(76, 138, 137, 0.1)',
                    fill: true,
                    tension: 0.4
                }, {
                    label: 'Violent Crime Rate',
                    data: [4.5, 4.6, 4.4, 4.3, 4.5, 4.8, 4.6, 4.4, 4.2, 4.1, 4.3, 4.23],
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    fill: true,
                    tension: 0.4
                }, {
                    label: 'Property Crime Rate',
                    data: [3.3, 3.5, 3.9, 3.7, 4.0, 4.1, 4.6, 4.4, 4.2, 4.1, 4.2, 4.24],
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Rate per 1,000 Population' }
                    }
                }
            }
        });

        // District Comparison Chart
        const districtCtx = document.getElementById('districtComparisonChart').getContext('2d');
        new Chart(districtCtx, {
            type: 'bar',
            data: {
                labels: ['Cubao', 'Novaliches', 'Commonwealth', 'Project 4', 'Fairview', 'Kamuning', 'Diliman', 'Batasan'],
                datasets: [{
                    label: 'Crime Rate per 1,000',
                    data: [15.35, 12.44, 9.82, 9.43, 7.44, 7.16, 6.27, 6.09],
                    backgroundColor: [
                        '#ef4444', '#ef4444', '#f59e0b', '#f59e0b',
                        '#f59e0b', '#f59e0b', '#22c55e', '#22c55e'
                    ],
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        title: { display: true, text: 'Rate per 1,000' }
                    }
                }
            }
        });

        // Crime Type Rate Chart
        const typeCtx = document.getElementById('crimeTypeRateChart').getContext('2d');
        new Chart(typeCtx, {
            type: 'doughnut',
            data: {
                labels: ['Theft', 'Robbery', 'Assault', 'Burglary', 'Drug-Related', 'Others'],
                datasets: [{
                    data: [2.85, 1.92, 1.38, 1.12, 0.72, 0.48],
                    backgroundColor: [
                        '#ef4444', '#dc2626', '#f59e0b', '#8b5cf6', '#3b82f6', '#6b7280'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right' }
                }
            }
        });

        // Year-over-Year Chart
        const yoyCtx = document.getElementById('yearOverYearChart').getContext('2d');
        new Chart(yoyCtx, {
            type: 'bar',
            data: {
                labels: ['2021', '2022', '2023', '2024', '2025'],
                datasets: [{
                    label: 'Crime Rate',
                    data: [7.2, 7.8, 8.1, 8.05, 8.47],
                    backgroundColor: '#4c8a89',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 6,
                        max: 10,
                        title: { display: true, text: 'Rate per 1,000' }
                    }
                }
            }
        });

        // Percentage Change Chart
        const changeCtx = document.getElementById('percentageChangeChart').getContext('2d');
        new Chart(changeCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Monthly % Change',
                    data: [2.5, 3.8, 2.5, -3.6, 6.3, 4.7, 3.4, -4.3, -4.5, -2.4, 3.7, -0.4],
                    backgroundColor: function(context) {
                        const value = context.dataset.data[context.dataIndex];
                        return value >= 0 ? 'rgba(239, 68, 68, 0.7)' : 'rgba(34, 197, 94, 0.7)';
                    },
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        title: { display: true, text: 'Percentage Change (%)' }
                    }
                }
            }
        });
    </script>
</body>
</html>
