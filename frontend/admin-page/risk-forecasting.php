<?php
// Authentication check - must be at the top of every admin page
require_once '../../api/middleware/auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Risk Forecasting | Crime Dep.</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/admin-header.css">
    <link rel="stylesheet" href="../css/hero.css">
    <link rel="stylesheet" href="../css/sidebar-footer.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="../css/crime-mapping.css">
    <link rel="icon" type="image/x-icon" href="../image/favicon.ico">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        #risk-map {
            height: calc(100vh - 400px);
            min-height: 400px;
            max-height: 600px;
            width: 100%;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            margin: 20px 0;
        }

        .forecast-card {
            background: var(--card-bg-1);
            border: 1px solid var(--border-color-1);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .forecast-card-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .forecast-card-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .forecast-card-icon.danger { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
        .forecast-card-icon.warning { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        .forecast-card-icon.info { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
        .forecast-card-icon.success { background: rgba(34, 197, 94, 0.1); color: #22c55e; }

        .forecast-card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-color-1);
            margin: 0;
        }

        .forecast-card-subtitle {
            font-size: 0.875rem;
            color: var(--text-secondary-1);
            margin: 0;
        }

        .risk-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .risk-stat {
            background: var(--card-bg-1);
            border: 1px solid var(--border-color-1);
            border-radius: 12px;
            padding: 1.25rem;
            text-align: center;
        }

        .risk-stat-value {
            font-size: 2rem;
            font-weight: 700;
        }

        .risk-stat-value.high { color: #ef4444; }
        .risk-stat-value.medium { color: #f59e0b; }
        .risk-stat-value.low { color: #22c55e; }
        .risk-stat-value.primary { color: var(--primary-color-1); }

        .risk-stat-label {
            font-size: 0.875rem;
            color: var(--text-secondary-1);
            margin-top: 0.25rem;
        }

        .risk-factors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1rem;
        }

        .risk-factor-item {
            background: var(--bg-color-1);
            border-radius: 8px;
            padding: 1rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .risk-factor-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .risk-factor-info {
            flex: 1;
        }

        .risk-factor-name {
            font-weight: 600;
            color: var(--text-color-1);
            font-size: 0.9rem;
        }

        .risk-factor-weight {
            font-size: 0.8rem;
            color: var(--text-secondary-1);
        }

        .risk-factor-bar {
            height: 6px;
            background: var(--border-color-1);
            border-radius: 3px;
            margin-top: 0.5rem;
            overflow: hidden;
        }

        .risk-factor-bar-fill {
            height: 100%;
            border-radius: 3px;
        }

        .area-risk-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .area-risk-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            background: var(--bg-color-1);
            border-radius: 8px;
            border-left: 4px solid;
        }

        .area-risk-item.critical { border-left-color: #dc2626; }
        .area-risk-item.high { border-left-color: #ef4444; }
        .area-risk-item.medium { border-left-color: #f59e0b; }
        .area-risk-item.low { border-left-color: #22c55e; }

        .area-info { display: flex; flex-direction: column; gap: 0.25rem; }
        .area-name { font-weight: 600; color: var(--text-color-1); }
        .area-details { font-size: 0.875rem; color: var(--text-secondary-1); }

        .risk-score {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .risk-score.critical { background: rgba(220, 38, 38, 0.1); color: #dc2626; }
        .risk-score.high { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
        .risk-score.medium { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        .risk-score.low { background: rgba(34, 197, 94, 0.1); color: #22c55e; }

        .chart-container {
            position: relative;
            height: 300px;
            margin-top: 1rem;
        }

        .two-column-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .alert-threshold {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            background: var(--bg-color-1);
            border-radius: 8px;
            margin-bottom: 0.75rem;
        }

        .alert-threshold-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .threshold-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .threshold-status {
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .threshold-status.triggered { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
        .threshold-status.normal { background: rgba(34, 197, 94, 0.1); color: #22c55e; }

        @media (max-width: 992px) {
            .two-column-layout { grid-template-columns: 1fr; }
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
                            <a href="/analytics" class="breadcrumb-link"><span>Predictive Tools</span></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <span>Risk Forecasting</span>
                        </li>
                    </ol>
                </nav>
                <h1>Risk Forecasting - Quezon City</h1>
                <p>Comprehensive risk assessment using weighted scoring algorithm. Analyze risk levels by area, track risk trends over time, and monitor risk threshold alerts.</p>
            </div>

            <div class="sub-container">
                <div class="page-content">
                    <!-- Risk Statistics -->
                    <div class="risk-stats">
                        <div class="risk-stat">
                            <div class="risk-stat-value high">8</div>
                            <div class="risk-stat-label">Critical Risk Areas</div>
                        </div>
                        <div class="risk-stat">
                            <div class="risk-stat-value medium">15</div>
                            <div class="risk-stat-label">Elevated Risk Areas</div>
                        </div>
                        <div class="risk-stat">
                            <div class="risk-stat-value low">23</div>
                            <div class="risk-stat-label">Normal Risk Areas</div>
                        </div>
                        <div class="risk-stat">
                            <div class="risk-stat-value primary">72.5</div>
                            <div class="risk-stat-label">Average Risk Score</div>
                        </div>
                    </div>

                    <!-- Risk Map (Color-coded by area) -->
                    <div class="forecast-card">
                        <div class="forecast-card-header">
                            <div class="forecast-card-icon danger">
                                <i class="fas fa-map-marked-alt"></i>
                            </div>
                            <div>
                                <h3 class="forecast-card-title">Risk Levels by Area</h3>
                                <p class="forecast-card-subtitle">Color-coded map showing risk assessment for each barangay/district</p>
                            </div>
                        </div>
                        <div id="risk-map"></div>
                    </div>

                    <div class="two-column-layout">
                        <!-- Risk Trend Over Time -->
                        <div class="forecast-card">
                            <div class="forecast-card-header">
                                <div class="forecast-card-icon info">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div>
                                    <h3 class="forecast-card-title">Risk Trend Over Time</h3>
                                    <p class="forecast-card-subtitle">Historical risk score progression (Last 12 months)</p>
                                </div>
                            </div>
                            <div class="chart-container">
                                <canvas id="riskTrendChart"></canvas>
                            </div>
                        </div>

                        <!-- Risk Factors Breakdown -->
                        <div class="forecast-card">
                            <div class="forecast-card-header">
                                <div class="forecast-card-icon warning">
                                    <i class="fas fa-balance-scale"></i>
                                </div>
                                <div>
                                    <h3 class="forecast-card-title">Risk Factors Breakdown</h3>
                                    <p class="forecast-card-subtitle">Contributing factors with weighted scores</p>
                                </div>
                            </div>
                            <div class="risk-factors-grid">
                                <div class="risk-factor-item">
                                    <div class="risk-factor-icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                                        <i class="fas fa-history"></i>
                                    </div>
                                    <div class="risk-factor-info">
                                        <div class="risk-factor-name">Historical Crime Rate</div>
                                        <div class="risk-factor-weight">Weight: 35% | Score: 82/100</div>
                                        <div class="risk-factor-bar">
                                            <div class="risk-factor-bar-fill" style="width: 82%; background: #ef4444;"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="risk-factor-item">
                                    <div class="risk-factor-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div class="risk-factor-info">
                                        <div class="risk-factor-name">Population Density</div>
                                        <div class="risk-factor-weight">Weight: 20% | Score: 75/100</div>
                                        <div class="risk-factor-bar">
                                            <div class="risk-factor-bar-fill" style="width: 75%; background: #f59e0b;"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="risk-factor-item">
                                    <div class="risk-factor-icon" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="risk-factor-info">
                                        <div class="risk-factor-name">Time-based Patterns</div>
                                        <div class="risk-factor-weight">Weight: 15% | Score: 68/100</div>
                                        <div class="risk-factor-bar">
                                            <div class="risk-factor-bar-fill" style="width: 68%; background: #3b82f6;"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="risk-factor-item">
                                    <div class="risk-factor-icon" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                                        <i class="fas fa-building"></i>
                                    </div>
                                    <div class="risk-factor-info">
                                        <div class="risk-factor-name">Infrastructure Score</div>
                                        <div class="risk-factor-weight">Weight: 15% | Score: 58/100</div>
                                        <div class="risk-factor-bar">
                                            <div class="risk-factor-bar-fill" style="width: 58%; background: #8b5cf6;"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="risk-factor-item">
                                    <div class="risk-factor-icon" style="background: rgba(34, 197, 94, 0.1); color: #22c55e;">
                                        <i class="fas fa-lightbulb"></i>
                                    </div>
                                    <div class="risk-factor-info">
                                        <div class="risk-factor-name">Street Lighting</div>
                                        <div class="risk-factor-weight">Weight: 10% | Score: 45/100</div>
                                        <div class="risk-factor-bar">
                                            <div class="risk-factor-bar-fill" style="width: 45%; background: #22c55e;"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="risk-factor-item">
                                    <div class="risk-factor-icon" style="background: rgba(107, 114, 128, 0.1); color: #6b7280;">
                                        <i class="fas fa-shield-alt"></i>
                                    </div>
                                    <div class="risk-factor-info">
                                        <div class="risk-factor-name">Police Presence</div>
                                        <div class="risk-factor-weight">Weight: 5% | Score: 62/100</div>
                                        <div class="risk-factor-bar">
                                            <div class="risk-factor-bar-fill" style="width: 62%; background: #6b7280;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Area Risk Assessment -->
                    <div class="forecast-card">
                        <div class="forecast-card-header">
                            <div class="forecast-card-icon danger">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div>
                                <h3 class="forecast-card-title">Area Risk Assessment</h3>
                                <p class="forecast-card-subtitle">Ranked list of areas by calculated risk score</p>
                            </div>
                        </div>
                        <div class="area-risk-list">
                            <div class="area-risk-item critical">
                                <div class="area-info">
                                    <span class="area-name">Cubao Commercial District</span>
                                    <span class="area-details">High crime history, dense population, poor lighting</span>
                                </div>
                                <span class="risk-score critical">Score: 92</span>
                            </div>
                            <div class="area-risk-item critical">
                                <div class="area-info">
                                    <span class="area-name">Novaliches Proper</span>
                                    <span class="area-details">Elevated crime rate, limited police coverage</span>
                                </div>
                                <span class="risk-score critical">Score: 88</span>
                            </div>
                            <div class="area-risk-item high">
                                <div class="area-info">
                                    <span class="area-name">Commonwealth Market</span>
                                    <span class="area-details">High foot traffic, frequent theft incidents</span>
                                </div>
                                <span class="risk-score high">Score: 81</span>
                            </div>
                            <div class="area-risk-item high">
                                <div class="area-info">
                                    <span class="area-name">Project 4</span>
                                    <span class="area-details">Drug-related incidents, night-time risk</span>
                                </div>
                                <span class="risk-score high">Score: 76</span>
                            </div>
                            <div class="area-risk-item medium">
                                <div class="area-info">
                                    <span class="area-name">Fairview Terraces Vicinity</span>
                                    <span class="area-details">Moderate risk, commercial area</span>
                                </div>
                                <span class="risk-score medium">Score: 65</span>
                            </div>
                            <div class="area-risk-item medium">
                                <div class="area-info">
                                    <span class="area-name">Batasan Hills</span>
                                    <span class="area-details">Residential area, occasional incidents</span>
                                </div>
                                <span class="risk-score medium">Score: 58</span>
                            </div>
                            <div class="area-risk-item low">
                                <div class="area-info">
                                    <span class="area-name">UP Diliman Campus</span>
                                    <span class="area-details">Good security, controlled access</span>
                                </div>
                                <span class="risk-score low">Score: 32</span>
                            </div>
                        </div>
                    </div>

                    <!-- Risk Threshold Alerts -->
                    <div class="forecast-card">
                        <div class="forecast-card-header">
                            <div class="forecast-card-icon warning">
                                <i class="fas fa-bell"></i>
                            </div>
                            <div>
                                <h3 class="forecast-card-title">Risk Threshold Alerts</h3>
                                <p class="forecast-card-subtitle">Monitoring thresholds and current alert status</p>
                            </div>
                        </div>
                        <div class="alert-threshold">
                            <div class="alert-threshold-info">
                                <span class="threshold-indicator" style="background: #dc2626;"></span>
                                <div>
                                    <div style="font-weight: 600; color: var(--text-color-1);">Critical Risk Threshold (Score > 85)</div>
                                    <div style="font-size: 0.875rem; color: var(--text-secondary-1);">2 areas currently exceed this threshold</div>
                                </div>
                            </div>
                            <span class="threshold-status triggered">TRIGGERED</span>
                        </div>
                        <div class="alert-threshold">
                            <div class="alert-threshold-info">
                                <span class="threshold-indicator" style="background: #ef4444;"></span>
                                <div>
                                    <div style="font-weight: 600; color: var(--text-color-1);">High Risk Threshold (Score > 70)</div>
                                    <div style="font-size: 0.875rem; color: var(--text-secondary-1);">4 areas currently exceed this threshold</div>
                                </div>
                            </div>
                            <span class="threshold-status triggered">TRIGGERED</span>
                        </div>
                        <div class="alert-threshold">
                            <div class="alert-threshold-info">
                                <span class="threshold-indicator" style="background: #f59e0b;"></span>
                                <div>
                                    <div style="font-weight: 600; color: var(--text-color-1);">Elevated Risk Threshold (Score > 50)</div>
                                    <div style="font-size: 0.875rem; color: var(--text-secondary-1);">6 areas currently exceed this threshold</div>
                                </div>
                            </div>
                            <span class="threshold-status triggered">TRIGGERED</span>
                        </div>
                        <div class="alert-threshold">
                            <div class="alert-threshold-info">
                                <span class="threshold-indicator" style="background: #22c55e;"></span>
                                <div>
                                    <div style="font-weight: 600; color: var(--text-color-1);">Weekly Crime Spike (>20% increase)</div>
                                    <div style="font-size: 0.875rem; color: var(--text-secondary-1);">No significant spike detected this week</div>
                                </div>
                            </div>
                            <span class="threshold-status normal">NORMAL</span>
                        </div>
                    </div>

                    <!-- Legend -->
                    <div class="legend">
                        <div class="legend-header">
                            <h3><i class="fas fa-info-circle"></i> Risk Score Legend</h3>
                        </div>
                        <div class="legend-grid">
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #dc2626;"></div>
                                <span class="legend-label">Critical (85-100)</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #ef4444;"></div>
                                <span class="legend-label">High (70-84)</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #f59e0b;"></div>
                                <span class="legend-label">Medium (50-69)</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #22c55e;"></div>
                                <span class="legend-label">Low (0-49)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include '../includes/admin-footer.php' ?>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Quezon City boundary polygon
        const quezonCityPolygon = [
            [14.7650, 121.0000], [14.7600, 121.0100], [14.7550, 121.0200],
            [14.7500, 121.0300], [14.7450, 121.0400], [14.7400, 121.0500],
            [14.7300, 121.0600], [14.7200, 121.0700], [14.7100, 121.0800],
            [14.7000, 121.0900], [14.6900, 121.1000], [14.6700, 121.1050],
            [14.6500, 121.1000], [14.6300, 121.0900], [14.6100, 121.0800],
            [14.5950, 121.0700], [14.5900, 121.0600], [14.5850, 121.0500],
            [14.5900, 121.0400], [14.5950, 121.0300], [14.6000, 121.0200],
            [14.6050, 121.0100], [14.6100, 121.0000], [14.6150, 120.9950],
            [14.6300, 120.9900], [14.6500, 120.9850], [14.6700, 120.9900],
            [14.6900, 120.9950], [14.7100, 121.0000], [14.7300, 120.9950],
            [14.7500, 120.9900], [14.7650, 121.0000]
        ];

        const strictBounds = L.latLngBounds([14.5800, 120.9800], [14.7700, 121.1100]);

        const map = L.map('risk-map', {
            center: [14.6500, 121.0400],
            zoom: 12,
            minZoom: 11,
            maxZoom: 16,
            maxBounds: strictBounds,
            maxBoundsViscosity: 1.0
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 18
        }).addTo(map);

        // Mask layer
        const outerBounds = [[-90, -180], [-90, 180], [90, 180], [90, -180], [-90, -180]];
        L.polygon([outerBounds, quezonCityPolygon], {
            color: 'transparent',
            fillColor: '#111827',
            fillOpacity: 0.85,
            interactive: false
        }).addTo(map);

        // QC boundary
        L.polygon(quezonCityPolygon, {
            color: '#4c8a89',
            weight: 3,
            opacity: 1,
            fillColor: 'transparent',
            fillOpacity: 0
        }).addTo(map);

        // Risk areas with color-coded polygons
        const riskAreas = [
            { name: 'Cubao', score: 92, center: [14.6180, 121.0550], color: '#dc2626' },
            { name: 'Novaliches', score: 88, center: [14.7100, 121.0200], color: '#dc2626' },
            { name: 'Commonwealth', score: 81, center: [14.6760, 121.0437], color: '#ef4444' },
            { name: 'Project 4', score: 76, center: [14.6350, 121.0600], color: '#ef4444' },
            { name: 'Fairview', score: 65, center: [14.7000, 121.0700], color: '#f59e0b' },
            { name: 'Batasan Hills', score: 58, center: [14.6900, 121.0500], color: '#f59e0b' },
            { name: 'Diliman', score: 45, center: [14.6500, 121.0300], color: '#22c55e' },
            { name: 'UP Campus', score: 32, center: [14.6550, 121.0650], color: '#22c55e' }
        ];

        riskAreas.forEach(area => {
            L.circle(area.center, {
                color: area.color,
                fillColor: area.color,
                fillOpacity: 0.4,
                radius: 800,
                weight: 2
            }).addTo(map).bindPopup(`
                <div style="font-family: sans-serif; min-width: 180px;">
                    <h4 style="margin: 0 0 8px 0; color: ${area.color};">${area.name}</h4>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #666;">Risk Score:</span>
                        <span style="font-weight: 600; color: ${area.color};">${area.score}/100</span>
                    </div>
                </div>
            `);
        });

        // Risk Trend Chart
        const ctx = document.getElementById('riskTrendChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Average Risk Score',
                    data: [65, 68, 72, 70, 74, 78, 75, 80, 77, 73, 71, 72.5],
                    borderColor: '#4c8a89',
                    backgroundColor: 'rgba(76, 138, 137, 0.1)',
                    fill: true,
                    tension: 0.4
                }, {
                    label: 'Critical Threshold',
                    data: [85, 85, 85, 85, 85, 85, 85, 85, 85, 85, 85, 85],
                    borderColor: '#dc2626',
                    borderDash: [5, 5],
                    fill: false,
                    pointRadius: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: { beginAtZero: false, min: 50, max: 100 }
                }
            }
        });
    </script>
</body>
</html>
