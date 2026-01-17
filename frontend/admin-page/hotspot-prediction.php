<?php
// Authentication check - must be at the top of every admin page
require_once '../../api/middleware/auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotspot Prediction | Crime Dep.</title>
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
        #prediction-map {
            height: calc(100vh - 400px);
            min-height: 400px;
            max-height: 600px;
            width: 100%;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            margin: 20px 0;
        }

        .prediction-card {
            background: var(--card-bg-1);
            border: 1px solid var(--border-color-1);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .prediction-card-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .prediction-card-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .prediction-card-icon.danger { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
        .prediction-card-icon.warning { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        .prediction-card-icon.info { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
        .prediction-card-icon.success { background: rgba(34, 197, 94, 0.1); color: #22c55e; }
        .prediction-card-icon.primary { background: rgba(76, 138, 137, 0.1); color: #4c8a89; }

        .prediction-card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-color-1);
            margin: 0;
        }

        .prediction-card-subtitle {
            font-size: 0.875rem;
            color: var(--text-secondary-1);
            margin: 0;
        }

        .prediction-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .prediction-stat {
            background: var(--card-bg-1);
            border: 1px solid var(--border-color-1);
            border-radius: 12px;
            padding: 1.25rem;
            text-align: center;
        }

        .prediction-stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color-1);
        }

        .prediction-stat-value.danger { color: #ef4444; }
        .prediction-stat-value.warning { color: #f59e0b; }
        .prediction-stat-value.success { color: #22c55e; }

        .prediction-stat-label {
            font-size: 0.875rem;
            color: var(--text-secondary-1);
            margin-top: 0.25rem;
        }

        .time-filter {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .time-filter-btn {
            padding: 0.5rem 1rem;
            border: 1px solid var(--border-color-1);
            background: var(--card-bg-1);
            border-radius: 8px;
            font-size: 0.875rem;
            color: var(--text-color-1);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .time-filter-btn:hover,
        .time-filter-btn.active {
            background: var(--primary-color-1);
            color: white;
            border-color: var(--primary-color-1);
        }

        .hotspot-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .hotspot-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            background: var(--bg-color-1);
            border-radius: 8px;
            border-left: 4px solid;
            transition: all 0.2s ease;
        }

        .hotspot-item:hover { transform: translateX(5px); }
        .hotspot-item.high-risk { border-left-color: #ef4444; }
        .hotspot-item.medium-risk { border-left-color: #f59e0b; }
        .hotspot-item.low-risk { border-left-color: #22c55e; }

        .hotspot-info { display: flex; flex-direction: column; gap: 0.25rem; }
        .hotspot-name { font-weight: 600; color: var(--text-color-1); }
        .hotspot-details { font-size: 0.875rem; color: var(--text-secondary-1); }

        .probability-badge {
            padding: 0.375rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .probability-badge.high { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
        .probability-badge.medium { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        .probability-badge.low { background: rgba(34, 197, 94, 0.1); color: #22c55e; }

        .confidence-indicator {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .confidence-bar {
            width: 40px;
            height: 6px;
            background: var(--border-color-1);
            border-radius: 3px;
            overflow: hidden;
        }

        .confidence-bar-fill {
            height: 100%;
            border-radius: 3px;
        }

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

        .comparison-table {
            width: 100%;
            border-collapse: collapse;
        }

        .comparison-table th,
        .comparison-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color-1);
        }

        .comparison-table th {
            font-weight: 600;
            color: var(--text-color-1);
            background: var(--bg-color-1);
        }

        .comparison-table td {
            color: var(--text-secondary-1);
        }

        .comparison-table .accuracy {
            font-weight: 600;
        }

        .comparison-table .accuracy.good { color: #22c55e; }
        .comparison-table .accuracy.moderate { color: #f59e0b; }
        .comparison-table .accuracy.poor { color: #ef4444; }

        .model-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .model-info-item {
            background: var(--bg-color-1);
            padding: 1rem;
            border-radius: 8px;
        }

        .model-info-label {
            font-size: 0.75rem;
            color: var(--text-secondary-1);
            text-transform: uppercase;
            margin-bottom: 0.25rem;
        }

        .model-info-value {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-color-1);
        }

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
                            <span>Hotspot Prediction</span>
                        </li>
                    </ol>
                </nav>
                <h1>Crime Hotspot Prediction - Quezon City</h1>
                <p>Machine learning-powered hotspot detection algorithm predicting high-risk crime areas based on historical data patterns, time factors, and environmental variables.</p>
            </div>

            <div class="sub-container">
                <div class="page-content">
                    <!-- Prediction Statistics -->
                    <div class="prediction-stats">
                        <div class="prediction-stat">
                            <div class="prediction-stat-value danger">8</div>
                            <div class="prediction-stat-label">Predicted Hotspots</div>
                        </div>
                        <div class="prediction-stat">
                            <div class="prediction-stat-value warning">15</div>
                            <div class="prediction-stat-label">Watch Areas</div>
                        </div>
                        <div class="prediction-stat">
                            <div class="prediction-stat-value success">87%</div>
                            <div class="prediction-stat-label">Model Accuracy</div>
                        </div>
                        <div class="prediction-stat">
                            <div class="prediction-stat-value">92%</div>
                            <div class="prediction-stat-label">Confidence Level</div>
                        </div>
                        <div class="prediction-stat">
                            <div class="prediction-stat-value">24h</div>
                            <div class="prediction-stat-label">Prediction Window</div>
                        </div>
                    </div>

                    <!-- Time-based Hotspot Prediction Filter -->
                    <div class="prediction-card">
                        <div class="prediction-card-header">
                            <div class="prediction-card-icon info">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <h3 class="prediction-card-title">Time-Based Prediction</h3>
                                <p class="prediction-card-subtitle">Select prediction time window</p>
                            </div>
                        </div>
                        <div class="time-filter">
                            <button class="time-filter-btn active" data-hours="6">Next 6 Hours</button>
                            <button class="time-filter-btn" data-hours="12">Next 12 Hours</button>
                            <button class="time-filter-btn" data-hours="24">Next 24 Hours</button>
                            <button class="time-filter-btn" data-hours="48">Next 48 Hours</button>
                            <button class="time-filter-btn" data-hours="168">Next 7 Days</button>
                        </div>
                    </div>

                    <!-- Predicted Hotspots Map -->
                    <div class="prediction-card">
                        <div class="prediction-card-header">
                            <div class="prediction-card-icon danger">
                                <i class="fas fa-map-marked-alt"></i>
                            </div>
                            <div>
                                <h3 class="prediction-card-title">Predicted Hotspots on Map</h3>
                                <p class="prediction-card-subtitle">Visual display of predicted crime hotspots with probability scores</p>
                            </div>
                        </div>
                        <div id="prediction-map"></div>
                    </div>

                    <div class="two-column-layout">
                        <!-- Predicted Hotspots with Probability Scores -->
                        <div class="prediction-card">
                            <div class="prediction-card-header">
                                <div class="prediction-card-icon warning">
                                    <i class="fas fa-crosshairs"></i>
                                </div>
                                <div>
                                    <h3 class="prediction-card-title">Hotspot Probability Scores</h3>
                                    <p class="prediction-card-subtitle">Predicted areas with confidence levels</p>
                                </div>
                            </div>
                            <div class="hotspot-list">
                                <div class="hotspot-item high-risk">
                                    <div class="hotspot-info">
                                        <span class="hotspot-name">Cubao Commercial District</span>
                                        <span class="hotspot-details">Theft, Robbery - Peak: 6PM-10PM</span>
                                    </div>
                                    <div class="probability-badge high">
                                        <span>92%</span>
                                        <div class="confidence-indicator">
                                            <div class="confidence-bar">
                                                <div class="confidence-bar-fill" style="width: 95%; background: #ef4444;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="hotspot-item high-risk">
                                    <div class="hotspot-info">
                                        <span class="hotspot-name">Novaliches Proper</span>
                                        <span class="hotspot-details">Burglary, Assault - Peak: 11PM-3AM</span>
                                    </div>
                                    <div class="probability-badge high">
                                        <span>88%</span>
                                        <div class="confidence-indicator">
                                            <div class="confidence-bar">
                                                <div class="confidence-bar-fill" style="width: 90%; background: #ef4444;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="hotspot-item high-risk">
                                    <div class="hotspot-info">
                                        <span class="hotspot-name">Commonwealth Market</span>
                                        <span class="hotspot-details">Theft, Fraud - Peak: 2PM-6PM</span>
                                    </div>
                                    <div class="probability-badge high">
                                        <span>85%</span>
                                        <div class="confidence-indicator">
                                            <div class="confidence-bar">
                                                <div class="confidence-bar-fill" style="width: 88%; background: #ef4444;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="hotspot-item medium-risk">
                                    <div class="hotspot-info">
                                        <span class="hotspot-name">Fairview Terraces</span>
                                        <span class="hotspot-details">Theft - Peak: 5PM-9PM</span>
                                    </div>
                                    <div class="probability-badge medium">
                                        <span>72%</span>
                                        <div class="confidence-indicator">
                                            <div class="confidence-bar">
                                                <div class="confidence-bar-fill" style="width: 78%; background: #f59e0b;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="hotspot-item medium-risk">
                                    <div class="hotspot-info">
                                        <span class="hotspot-name">Project 4</span>
                                        <span class="hotspot-details">Drug-Related - Peak: 10PM-2AM</span>
                                    </div>
                                    <div class="probability-badge medium">
                                        <span>68%</span>
                                        <div class="confidence-indicator">
                                            <div class="confidence-bar">
                                                <div class="confidence-bar-fill" style="width: 72%; background: #f59e0b;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Historical vs Predicted Comparison -->
                        <div class="prediction-card">
                            <div class="prediction-card-header">
                                <div class="prediction-card-icon primary">
                                    <i class="fas fa-balance-scale-right"></i>
                                </div>
                                <div>
                                    <h3 class="prediction-card-title">Historical vs Predicted</h3>
                                    <p class="prediction-card-subtitle">Model accuracy comparison with actual incidents</p>
                                </div>
                            </div>
                            <table class="comparison-table">
                                <thead>
                                    <tr>
                                        <th>Area</th>
                                        <th>Predicted</th>
                                        <th>Actual</th>
                                        <th>Accuracy</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Cubao</td>
                                        <td>12 incidents</td>
                                        <td>14 incidents</td>
                                        <td class="accuracy good">86%</td>
                                    </tr>
                                    <tr>
                                        <td>Novaliches</td>
                                        <td>8 incidents</td>
                                        <td>9 incidents</td>
                                        <td class="accuracy good">89%</td>
                                    </tr>
                                    <tr>
                                        <td>Commonwealth</td>
                                        <td>10 incidents</td>
                                        <td>11 incidents</td>
                                        <td class="accuracy good">91%</td>
                                    </tr>
                                    <tr>
                                        <td>Fairview</td>
                                        <td>6 incidents</td>
                                        <td>8 incidents</td>
                                        <td class="accuracy moderate">75%</td>
                                    </tr>
                                    <tr>
                                        <td>Project 4</td>
                                        <td>5 incidents</td>
                                        <td>5 incidents</td>
                                        <td class="accuracy good">100%</td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="model-info">
                                <div class="model-info-item">
                                    <div class="model-info-label">Training Data</div>
                                    <div class="model-info-value">12 months</div>
                                </div>
                                <div class="model-info-item">
                                    <div class="model-info-label">Last Updated</div>
                                    <div class="model-info-value">Today, 6:00 AM</div>
                                </div>
                                <div class="model-info-item">
                                    <div class="model-info-label">Overall Accuracy</div>
                                    <div class="model-info-value">87.2%</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Prediction Trend Chart -->
                    <div class="prediction-card">
                        <div class="prediction-card-header">
                            <div class="prediction-card-icon info">
                                <i class="fas fa-chart-area"></i>
                            </div>
                            <div>
                                <h3 class="prediction-card-title">Prediction Accuracy Trend</h3>
                                <p class="prediction-card-subtitle">Model performance over the last 12 weeks</p>
                            </div>
                        </div>
                        <div class="chart-container">
                            <canvas id="accuracyTrendChart"></canvas>
                        </div>
                    </div>

                    <!-- Model Insights -->
                    <div class="prediction-card">
                        <div class="prediction-card-header">
                            <div class="prediction-card-icon success">
                                <i class="fas fa-brain"></i>
                            </div>
                            <div>
                                <h3 class="prediction-card-title">ML Model Insights</h3>
                                <p class="prediction-card-subtitle">Key findings from the prediction algorithm</p>
                            </div>
                        </div>
                        <div class="hotspot-list">
                            <div class="hotspot-item low-risk">
                                <div class="hotspot-info">
                                    <span class="hotspot-name">High correlation detected: Weekend nights + Commercial areas</span>
                                    <span class="hotspot-details">85% of theft incidents occur between 8PM-12AM in commercial zones on weekends</span>
                                </div>
                            </div>
                            <div class="hotspot-item low-risk">
                                <div class="hotspot-info">
                                    <span class="hotspot-name">Seasonal pattern identified: Holiday spike</span>
                                    <span class="hotspot-details">Crime rates increase by 32% during holiday seasons, particularly theft and fraud</span>
                                </div>
                            </div>
                            <div class="hotspot-item low-risk">
                                <div class="hotspot-info">
                                    <span class="hotspot-name">Geographic cluster: Transport hubs</span>
                                    <span class="hotspot-details">Areas within 500m of major transport terminals show 45% higher incident rates</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Legend -->
                    <div class="legend">
                        <div class="legend-header">
                            <h3><i class="fas fa-info-circle"></i> Probability Legend</h3>
                        </div>
                        <div class="legend-grid">
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #dc2626;"></div>
                                <span class="legend-label">High Probability (>80%)</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #f59e0b;"></div>
                                <span class="legend-label">Medium Probability (50-80%)</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #eab308;"></div>
                                <span class="legend-label">Low-Medium (30-50%)</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #22c55e;"></div>
                                <span class="legend-label">Low Probability (<30%)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include '../includes/admin-footer.php' ?>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>
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

        const map = L.map('prediction-map', {
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

        // Prediction hotspots with probability scores
        const predictionHotspots = [
            { lat: 14.6180, lng: 121.0550, prob: 92, name: 'Cubao Commercial District', confidence: 95 },
            { lat: 14.7100, lng: 121.0200, prob: 88, name: 'Novaliches Proper', confidence: 90 },
            { lat: 14.6760, lng: 121.0437, prob: 85, name: 'Commonwealth Market', confidence: 88 },
            { lat: 14.7000, lng: 121.0700, prob: 72, name: 'Fairview Terraces', confidence: 78 },
            { lat: 14.6350, lng: 121.0600, prob: 68, name: 'Project 4', confidence: 72 },
            { lat: 14.6500, lng: 121.0300, prob: 55, name: 'Diliman', confidence: 65 },
            { lat: 14.6900, lng: 121.0500, prob: 48, name: 'Batasan Hills', confidence: 60 },
            { lat: 14.6400, lng: 121.0400, prob: 35, name: 'Kamuning', confidence: 55 }
        ];

        function getColor(prob) {
            if (prob >= 80) return '#dc2626';
            if (prob >= 50) return '#f59e0b';
            if (prob >= 30) return '#eab308';
            return '#22c55e';
        }

        predictionHotspots.forEach(hotspot => {
            const color = getColor(hotspot.prob);
            const radius = 300 + (hotspot.prob * 4);

            // Outer circle
            L.circle([hotspot.lat, hotspot.lng], {
                color: color,
                fillColor: color,
                fillOpacity: 0.3,
                radius: radius,
                weight: 2
            }).addTo(map);

            // Inner circle
            L.circle([hotspot.lat, hotspot.lng], {
                color: color,
                fillColor: color,
                fillOpacity: 0.6,
                radius: radius * 0.4,
                weight: 0
            }).addTo(map).bindPopup(`
                <div style="font-family: sans-serif; min-width: 200px;">
                    <h4 style="margin: 0 0 10px 0; color: ${color};">
                        <i class="fas fa-crosshairs"></i> ${hotspot.name}
                    </h4>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                        <span style="color: #666;">Probability:</span>
                        <span style="font-weight: 600; color: ${color};">${hotspot.prob}%</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #666;">Confidence:</span>
                        <span style="font-weight: 600; color: #4c8a89;">${hotspot.confidence}%</span>
                    </div>
                </div>
            `);
        });

        // Time filter buttons
        document.querySelectorAll('.time-filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.time-filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Accuracy Trend Chart
        const ctx = document.getElementById('accuracyTrendChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6', 'Week 7', 'Week 8', 'Week 9', 'Week 10', 'Week 11', 'Week 12'],
                datasets: [{
                    label: 'Prediction Accuracy',
                    data: [82, 84, 83, 86, 85, 87, 86, 88, 87, 89, 88, 87],
                    borderColor: '#4c8a89',
                    backgroundColor: 'rgba(76, 138, 137, 0.1)',
                    fill: true,
                    tension: 0.4
                }, {
                    label: 'Target Accuracy',
                    data: [85, 85, 85, 85, 85, 85, 85, 85, 85, 85, 85, 85],
                    borderColor: '#22c55e',
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
                    y: { beginAtZero: false, min: 70, max: 100 }
                }
            }
        });
    </script>
</body>
</html>
