<?php
// Authentication check - must be at the top of every admin page
require_once '../../api/middleware/auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pattern Detection | Crime Dep.</title>
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
        #pattern-map {
            height: calc(100vh - 400px);
            min-height: 400px;
            max-height: 600px;
            width: 100%;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            margin: 20px 0;
        }

        .pattern-card {
            background: var(--card-bg-1);
            border: 1px solid var(--border-color-1);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .pattern-card-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .pattern-card-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .pattern-card-icon.danger { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
        .pattern-card-icon.warning { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        .pattern-card-icon.info { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
        .pattern-card-icon.success { background: rgba(34, 197, 94, 0.1); color: #22c55e; }
        .pattern-card-icon.primary { background: rgba(76, 138, 137, 0.1); color: #4c8a89; }
        .pattern-card-icon.purple { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }

        .pattern-card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-color-1);
            margin: 0;
        }

        .pattern-card-subtitle {
            font-size: 0.875rem;
            color: var(--text-secondary-1);
            margin: 0;
        }

        .pattern-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .pattern-stat {
            background: var(--card-bg-1);
            border: 1px solid var(--border-color-1);
            border-radius: 12px;
            padding: 1.25rem;
            text-align: center;
        }

        .pattern-stat-value {
            font-size: 2rem;
            font-weight: 700;
        }

        .pattern-stat-value.danger { color: #ef4444; }
        .pattern-stat-value.warning { color: #f59e0b; }
        .pattern-stat-value.success { color: #22c55e; }
        .pattern-stat-value.primary { color: var(--primary-color-1); }
        .pattern-stat-value.purple { color: #8b5cf6; }

        .pattern-stat-label {
            font-size: 0.875rem;
            color: var(--text-secondary-1);
            margin-top: 0.25rem;
        }

        .detected-patterns-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .pattern-item {
            background: var(--bg-color-1);
            border-radius: 12px;
            padding: 1.25rem;
            border-left: 4px solid;
            transition: all 0.2s ease;
        }

        .pattern-item:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .pattern-item.series { border-left-color: #ef4444; }
        .pattern-item.mo { border-left-color: #8b5cf6; }
        .pattern-item.temporal { border-left-color: #3b82f6; }
        .pattern-item.geographic { border-left-color: #22c55e; }

        .pattern-item-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.75rem;
        }

        .pattern-item-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .pattern-type-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .pattern-type-badge.series { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
        .pattern-type-badge.mo { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }
        .pattern-type-badge.temporal { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
        .pattern-type-badge.geographic { background: rgba(34, 197, 94, 0.1); color: #22c55e; }

        .pattern-name {
            font-weight: 600;
            font-size: 1rem;
            color: var(--text-color-1);
        }

        .similarity-score {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: var(--card-bg-1);
            border-radius: 8px;
        }

        .similarity-score-value {
            font-weight: 700;
            font-size: 1.1rem;
        }

        .similarity-score-value.high { color: #22c55e; }
        .similarity-score-value.medium { color: #f59e0b; }
        .similarity-score-value.low { color: #ef4444; }

        .similarity-bar {
            width: 60px;
            height: 6px;
            background: var(--border-color-1);
            border-radius: 3px;
            overflow: hidden;
        }

        .similarity-bar-fill {
            height: 100%;
            border-radius: 3px;
        }

        .pattern-item-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 0.75rem;
        }

        .pattern-detail {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .pattern-detail-label {
            font-size: 0.75rem;
            color: var(--text-secondary-1);
            text-transform: uppercase;
        }

        .pattern-detail-value {
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--text-color-1);
        }

        .linked-crimes {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-top: 0.75rem;
            padding-top: 0.75rem;
            border-top: 1px solid var(--border-color-1);
        }

        .crime-link {
            padding: 0.25rem 0.5rem;
            background: var(--card-bg-1);
            border: 1px solid var(--border-color-1);
            border-radius: 4px;
            font-size: 0.75rem;
            color: var(--text-color-1);
            display: flex;
            align-items: center;
            gap: 0.25rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .crime-link:hover {
            background: var(--primary-color-1);
            color: white;
            border-color: var(--primary-color-1);
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

        .mo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .mo-item {
            background: var(--bg-color-1);
            border-radius: 8px;
            padding: 1rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .mo-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .mo-info {
            flex: 1;
        }

        .mo-name {
            font-weight: 600;
            color: var(--text-color-1);
            font-size: 0.9rem;
        }

        .mo-count {
            font-size: 0.8rem;
            color: var(--text-secondary-1);
        }

        .mo-trend {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
        }

        .mo-trend.up { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
        .mo-trend.down { background: rgba(34, 197, 94, 0.1); color: #22c55e; }
        .mo-trend.stable { background: rgba(107, 114, 128, 0.1); color: #6b7280; }

        .timeline-container {
            position: relative;
            padding-left: 30px;
        }

        .timeline-line {
            position: absolute;
            left: 12px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: var(--border-color-1);
        }

        .timeline-item {
            position: relative;
            padding-bottom: 1.5rem;
        }

        .timeline-dot {
            position: absolute;
            left: -24px;
            top: 4px;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            border: 3px solid;
            background: var(--card-bg-1);
        }

        .timeline-dot.theft { border-color: #ef4444; }
        .timeline-dot.robbery { border-color: #dc2626; }
        .timeline-dot.burglary { border-color: #8b5cf6; }
        .timeline-dot.assault { border-color: #f59e0b; }

        .timeline-content {
            background: var(--bg-color-1);
            padding: 1rem;
            border-radius: 8px;
        }

        .timeline-date {
            font-size: 0.75rem;
            color: var(--text-secondary-1);
            margin-bottom: 0.5rem;
        }

        .timeline-title {
            font-weight: 600;
            color: var(--text-color-1);
            margin-bottom: 0.25rem;
        }

        .timeline-desc {
            font-size: 0.875rem;
            color: var(--text-secondary-1);
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
                            <span>Pattern Detection</span>
                        </li>
                    </ol>
                </nav>
                <h1>Crime Pattern Detection - Quezon City</h1>
                <p>Advanced pattern matching algorithm to detect crime series, identify Modus Operandi (MO) patterns, and link related criminal activities for investigative support.</p>
            </div>

            <div class="sub-container">
                <div class="page-content">
                    <!-- Pattern Statistics -->
                    <div class="pattern-stats">
                        <div class="pattern-stat">
                            <div class="pattern-stat-value danger">12</div>
                            <div class="pattern-stat-label">Active Crime Series</div>
                        </div>
                        <div class="pattern-stat">
                            <div class="pattern-stat-value purple">8</div>
                            <div class="pattern-stat-label">MO Patterns Identified</div>
                        </div>
                        <div class="pattern-stat">
                            <div class="pattern-stat-value warning">47</div>
                            <div class="pattern-stat-label">Linked Crimes</div>
                        </div>
                        <div class="pattern-stat">
                            <div class="pattern-stat-value success">89%</div>
                            <div class="pattern-stat-label">Detection Accuracy</div>
                        </div>
                        <div class="pattern-stat">
                            <div class="pattern-stat-value primary">156</div>
                            <div class="pattern-stat-label">Patterns Analyzed</div>
                        </div>
                    </div>

                    <!-- Pattern Clusters Map -->
                    <div class="pattern-card">
                        <div class="pattern-card-header">
                            <div class="pattern-card-icon danger">
                                <i class="fas fa-project-diagram"></i>
                            </div>
                            <div>
                                <h3 class="pattern-card-title">Pattern Clusters on Map</h3>
                                <p class="pattern-card-subtitle">Visualize detected crime patterns and series geographically</p>
                            </div>
                        </div>
                        <div id="pattern-map"></div>
                    </div>

                    <div class="two-column-layout">
                        <!-- Detected Crime Patterns -->
                        <div class="pattern-card">
                            <div class="pattern-card-header">
                                <div class="pattern-card-icon purple">
                                    <i class="fas fa-fingerprint"></i>
                                </div>
                                <div>
                                    <h3 class="pattern-card-title">Detected Crime Patterns</h3>
                                    <p class="pattern-card-subtitle">Crime series and patterns with similarity scores</p>
                                </div>
                            </div>
                            <div class="detected-patterns-list">
                                <div class="pattern-item series">
                                    <div class="pattern-item-header">
                                        <div class="pattern-item-title">
                                            <span class="pattern-type-badge series">Series</span>
                                            <span class="pattern-name">Cubao Snatching Series</span>
                                        </div>
                                        <div class="similarity-score">
                                            <span class="similarity-score-value high">94%</span>
                                            <div class="similarity-bar">
                                                <div class="similarity-bar-fill" style="width: 94%; background: #22c55e;"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="pattern-item-details">
                                        <div class="pattern-detail">
                                            <span class="pattern-detail-label">Crime Type</span>
                                            <span class="pattern-detail-value">Robbery/Snatching</span>
                                        </div>
                                        <div class="pattern-detail">
                                            <span class="pattern-detail-label">Time Window</span>
                                            <span class="pattern-detail-value">6PM - 9PM</span>
                                        </div>
                                        <div class="pattern-detail">
                                            <span class="pattern-detail-label">Linked Cases</span>
                                            <span class="pattern-detail-value">7 incidents</span>
                                        </div>
                                        <div class="pattern-detail">
                                            <span class="pattern-detail-label">Last Occurrence</span>
                                            <span class="pattern-detail-value">2 days ago</span>
                                        </div>
                                    </div>
                                    <div class="linked-crimes">
                                        <span class="crime-link"><i class="fas fa-link"></i> CR-2025-1847</span>
                                        <span class="crime-link"><i class="fas fa-link"></i> CR-2025-1832</span>
                                        <span class="crime-link"><i class="fas fa-link"></i> CR-2025-1819</span>
                                        <span class="crime-link"><i class="fas fa-link"></i> +4 more</span>
                                    </div>
                                </div>

                                <div class="pattern-item mo">
                                    <div class="pattern-item-header">
                                        <div class="pattern-item-title">
                                            <span class="pattern-type-badge mo">MO Pattern</span>
                                            <span class="pattern-name">Motorcycle Tandem Robbery</span>
                                        </div>
                                        <div class="similarity-score">
                                            <span class="similarity-score-value high">91%</span>
                                            <div class="similarity-bar">
                                                <div class="similarity-bar-fill" style="width: 91%; background: #22c55e;"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="pattern-item-details">
                                        <div class="pattern-detail">
                                            <span class="pattern-detail-label">Method</span>
                                            <span class="pattern-detail-value">Tandem Motorcycle</span>
                                        </div>
                                        <div class="pattern-detail">
                                            <span class="pattern-detail-label">Target</span>
                                            <span class="pattern-detail-value">Pedestrians</span>
                                        </div>
                                        <div class="pattern-detail">
                                            <span class="pattern-detail-label">Linked Cases</span>
                                            <span class="pattern-detail-value">5 incidents</span>
                                        </div>
                                        <div class="pattern-detail">
                                            <span class="pattern-detail-label">Last Occurrence</span>
                                            <span class="pattern-detail-value">4 days ago</span>
                                        </div>
                                    </div>
                                    <div class="linked-crimes">
                                        <span class="crime-link"><i class="fas fa-link"></i> CR-2025-1801</span>
                                        <span class="crime-link"><i class="fas fa-link"></i> CR-2025-1789</span>
                                        <span class="crime-link"><i class="fas fa-link"></i> +3 more</span>
                                    </div>
                                </div>

                                <div class="pattern-item temporal">
                                    <div class="pattern-item-header">
                                        <div class="pattern-item-title">
                                            <span class="pattern-type-badge temporal">Temporal</span>
                                            <span class="pattern-name">Weekend Night Burglaries</span>
                                        </div>
                                        <div class="similarity-score">
                                            <span class="similarity-score-value medium">78%</span>
                                            <div class="similarity-bar">
                                                <div class="similarity-bar-fill" style="width: 78%; background: #f59e0b;"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="pattern-item-details">
                                        <div class="pattern-detail">
                                            <span class="pattern-detail-label">Crime Type</span>
                                            <span class="pattern-detail-value">Burglary</span>
                                        </div>
                                        <div class="pattern-detail">
                                            <span class="pattern-detail-label">Time Window</span>
                                            <span class="pattern-detail-value">Fri-Sun, 1AM-4AM</span>
                                        </div>
                                        <div class="pattern-detail">
                                            <span class="pattern-detail-label">Linked Cases</span>
                                            <span class="pattern-detail-value">9 incidents</span>
                                        </div>
                                        <div class="pattern-detail">
                                            <span class="pattern-detail-label">Last Occurrence</span>
                                            <span class="pattern-detail-value">Last weekend</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="pattern-item geographic">
                                    <div class="pattern-item-header">
                                        <div class="pattern-item-title">
                                            <span class="pattern-type-badge geographic">Geographic</span>
                                            <span class="pattern-name">Fairview Vehicle Theft Cluster</span>
                                        </div>
                                        <div class="similarity-score">
                                            <span class="similarity-score-value medium">72%</span>
                                            <div class="similarity-bar">
                                                <div class="similarity-bar-fill" style="width: 72%; background: #f59e0b;"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="pattern-item-details">
                                        <div class="pattern-detail">
                                            <span class="pattern-detail-label">Crime Type</span>
                                            <span class="pattern-detail-value">Vehicle Theft</span>
                                        </div>
                                        <div class="pattern-detail">
                                            <span class="pattern-detail-label">Radius</span>
                                            <span class="pattern-detail-value">500m cluster</span>
                                        </div>
                                        <div class="pattern-detail">
                                            <span class="pattern-detail-label">Linked Cases</span>
                                            <span class="pattern-detail-value">4 incidents</span>
                                        </div>
                                        <div class="pattern-detail">
                                            <span class="pattern-detail-label">Last Occurrence</span>
                                            <span class="pattern-detail-value">1 week ago</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pattern Timeline -->
                        <div class="pattern-card">
                            <div class="pattern-card-header">
                                <div class="pattern-card-icon info">
                                    <i class="fas fa-stream"></i>
                                </div>
                                <div>
                                    <h3 class="pattern-card-title">Pattern Timeline</h3>
                                    <p class="pattern-card-subtitle">Chronological view of linked crime occurrences</p>
                                </div>
                            </div>
                            <div class="timeline-container">
                                <div class="timeline-line"></div>
                                <div class="timeline-item">
                                    <div class="timeline-dot robbery"></div>
                                    <div class="timeline-content">
                                        <div class="timeline-date">Jan 14, 2026 - 7:45 PM</div>
                                        <div class="timeline-title">Snatching Incident - Cubao</div>
                                        <div class="timeline-desc">Bag snatching near Farmers Plaza. Matches Cubao Snatching Series pattern.</div>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-dot robbery"></div>
                                    <div class="timeline-content">
                                        <div class="timeline-date">Jan 12, 2026 - 6:30 PM</div>
                                        <div class="timeline-title">Robbery - Araneta Center</div>
                                        <div class="timeline-desc">Phone snatching by motorcycle tandem. Linked to MO Pattern #2.</div>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-dot burglary"></div>
                                    <div class="timeline-content">
                                        <div class="timeline-date">Jan 11, 2026 - 2:15 AM</div>
                                        <div class="timeline-title">Burglary - Novaliches</div>
                                        <div class="timeline-desc">Residential break-in. Matches Weekend Night Burglaries temporal pattern.</div>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-dot theft"></div>
                                    <div class="timeline-content">
                                        <div class="timeline-date">Jan 10, 2026 - 11:20 PM</div>
                                        <div class="timeline-title">Vehicle Theft - Fairview</div>
                                        <div class="timeline-desc">Motorcycle theft from parking area. Part of Fairview Vehicle Theft Cluster.</div>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-dot robbery"></div>
                                    <div class="timeline-content">
                                        <div class="timeline-date">Jan 9, 2026 - 8:10 PM</div>
                                        <div class="timeline-title">Snatching - Gateway Mall</div>
                                        <div class="timeline-desc">Cellphone snatching. Similar MO to Cubao series - running suspect.</div>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-dot burglary"></div>
                                    <div class="timeline-content">
                                        <div class="timeline-date">Jan 7, 2026 - 3:30 AM</div>
                                        <div class="timeline-title">Commercial Burglary - Commonwealth</div>
                                        <div class="timeline-desc">Store break-in during weekend hours. Temporal pattern match.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modus Operandi Patterns -->
                    <div class="pattern-card">
                        <div class="pattern-card-header">
                            <div class="pattern-card-icon warning">
                                <i class="fas fa-user-secret"></i>
                            </div>
                            <div>
                                <h3 class="pattern-card-title">Modus Operandi (MO) Patterns</h3>
                                <p class="pattern-card-subtitle">Identified criminal methods and their frequency</p>
                            </div>
                        </div>
                        <div class="mo-grid">
                            <div class="mo-item">
                                <div class="mo-icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                                    <i class="fas fa-motorcycle"></i>
                                </div>
                                <div class="mo-info">
                                    <div class="mo-name">Motorcycle Tandem</div>
                                    <div class="mo-count">18 incidents</div>
                                </div>
                                <span class="mo-trend up"><i class="fas fa-arrow-up"></i> +15%</span>
                            </div>
                            <div class="mo-item">
                                <div class="mo-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                                    <i class="fas fa-running"></i>
                                </div>
                                <div class="mo-info">
                                    <div class="mo-name">Grab & Run</div>
                                    <div class="mo-count">24 incidents</div>
                                </div>
                                <span class="mo-trend up"><i class="fas fa-arrow-up"></i> +8%</span>
                            </div>
                            <div class="mo-item">
                                <div class="mo-icon" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                                    <i class="fas fa-door-open"></i>
                                </div>
                                <div class="mo-info">
                                    <div class="mo-name">Forced Entry</div>
                                    <div class="mo-count">12 incidents</div>
                                </div>
                                <span class="mo-trend stable"><i class="fas fa-minus"></i> 0%</span>
                            </div>
                            <div class="mo-item">
                                <div class="mo-icon" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                                    <i class="fas fa-user-friends"></i>
                                </div>
                                <div class="mo-info">
                                    <div class="mo-name">Distraction Theft</div>
                                    <div class="mo-count">9 incidents</div>
                                </div>
                                <span class="mo-trend down"><i class="fas fa-arrow-down"></i> -12%</span>
                            </div>
                            <div class="mo-item">
                                <div class="mo-icon" style="background: rgba(34, 197, 94, 0.1); color: #22c55e;">
                                    <i class="fas fa-key"></i>
                                </div>
                                <div class="mo-info">
                                    <div class="mo-name">Lock Picking</div>
                                    <div class="mo-count">6 incidents</div>
                                </div>
                                <span class="mo-trend down"><i class="fas fa-arrow-down"></i> -5%</span>
                            </div>
                            <div class="mo-item">
                                <div class="mo-icon" style="background: rgba(107, 114, 128, 0.1); color: #6b7280;">
                                    <i class="fas fa-mask"></i>
                                </div>
                                <div class="mo-info">
                                    <div class="mo-name">Armed Holdup</div>
                                    <div class="mo-count">5 incidents</div>
                                </div>
                                <span class="mo-trend down"><i class="fas fa-arrow-down"></i> -20%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Pattern Detection Trend -->
                    <div class="pattern-card">
                        <div class="pattern-card-header">
                            <div class="pattern-card-icon success">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div>
                                <h3 class="pattern-card-title">Pattern Detection Trend</h3>
                                <p class="pattern-card-subtitle">Monthly detected patterns and linked crimes over time</p>
                            </div>
                        </div>
                        <div class="chart-container">
                            <canvas id="patternTrendChart"></canvas>
                        </div>
                    </div>

                    <!-- Legend -->
                    <div class="legend">
                        <div class="legend-header">
                            <h3><i class="fas fa-info-circle"></i> Pattern Type Legend</h3>
                        </div>
                        <div class="legend-grid">
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #ef4444;"></div>
                                <span class="legend-label">Crime Series (Repeated by same perpetrator)</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #8b5cf6;"></div>
                                <span class="legend-label">MO Pattern (Similar method/technique)</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #3b82f6;"></div>
                                <span class="legend-label">Temporal Pattern (Time-based correlation)</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #22c55e;"></div>
                                <span class="legend-label">Geographic Cluster (Location-based)</span>
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

        const map = L.map('pattern-map', {
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

        // Pattern clusters data
        const patternClusters = [
            {
                name: 'Cubao Snatching Series',
                type: 'series',
                color: '#ef4444',
                center: [14.6200, 121.0550],
                crimes: [
                    { lat: 14.6180, lng: 121.0540, id: 'CR-2025-1847' },
                    { lat: 14.6210, lng: 121.0560, id: 'CR-2025-1832' },
                    { lat: 14.6195, lng: 121.0530, id: 'CR-2025-1819' },
                    { lat: 14.6220, lng: 121.0545, id: 'CR-2025-1805' }
                ]
            },
            {
                name: 'Motorcycle Tandem MO',
                type: 'mo',
                color: '#8b5cf6',
                center: [14.6350, 121.0450],
                crimes: [
                    { lat: 14.6340, lng: 121.0440, id: 'CR-2025-1801' },
                    { lat: 14.6365, lng: 121.0460, id: 'CR-2025-1789' },
                    { lat: 14.6355, lng: 121.0445, id: 'CR-2025-1776' }
                ]
            },
            {
                name: 'Fairview Vehicle Theft',
                type: 'geographic',
                color: '#22c55e',
                center: [14.7000, 121.0700],
                crimes: [
                    { lat: 14.6990, lng: 121.0690, id: 'CR-2025-1750' },
                    { lat: 14.7010, lng: 121.0710, id: 'CR-2025-1738' },
                    { lat: 14.6995, lng: 121.0705, id: 'CR-2025-1725' }
                ]
            },
            {
                name: 'Weekend Burglary Pattern',
                type: 'temporal',
                color: '#3b82f6',
                center: [14.7100, 121.0200],
                crimes: [
                    { lat: 14.7090, lng: 121.0190, id: 'CR-2025-1712' },
                    { lat: 14.7110, lng: 121.0210, id: 'CR-2025-1698' },
                    { lat: 14.7105, lng: 121.0195, id: 'CR-2025-1685' }
                ]
            }
        ];

        // Draw pattern clusters
        patternClusters.forEach(cluster => {
            // Draw cluster area
            L.circle(cluster.center, {
                color: cluster.color,
                fillColor: cluster.color,
                fillOpacity: 0.2,
                radius: 400,
                weight: 2,
                dashArray: '5, 5'
            }).addTo(map).bindPopup(`
                <div style="font-family: sans-serif; min-width: 200px;">
                    <h4 style="margin: 0 0 8px 0; color: ${cluster.color};">
                        <i class="fas fa-project-diagram"></i> ${cluster.name}
                    </h4>
                    <div style="font-size: 12px; color: #666;">
                        Type: ${cluster.type.charAt(0).toUpperCase() + cluster.type.slice(1)} Pattern<br>
                        Linked Crimes: ${cluster.crimes.length}
                    </div>
                </div>
            `);

            // Draw individual crime points
            cluster.crimes.forEach(crime => {
                L.circleMarker([crime.lat, crime.lng], {
                    color: cluster.color,
                    fillColor: cluster.color,
                    fillOpacity: 0.8,
                    radius: 6,
                    weight: 2
                }).addTo(map).bindPopup(`
                    <div style="font-family: sans-serif;">
                        <strong>${crime.id}</strong><br>
                        <span style="font-size: 12px; color: #666;">Part of: ${cluster.name}</span>
                    </div>
                `);
            });

            // Draw connecting lines between crimes in cluster
            if (cluster.crimes.length > 1) {
                const points = cluster.crimes.map(c => [c.lat, c.lng]);
                L.polyline(points, {
                    color: cluster.color,
                    weight: 1,
                    opacity: 0.5,
                    dashArray: '3, 6'
                }).addTo(map);
            }
        });

        // Pattern Detection Trend Chart
        const ctx = document.getElementById('patternTrendChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan'],
                datasets: [{
                    label: 'Patterns Detected',
                    data: [8, 12, 10, 15, 14, 12],
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                    fill: true,
                    tension: 0.4
                }, {
                    label: 'Linked Crimes',
                    data: [28, 42, 35, 52, 48, 47],
                    borderColor: '#4c8a89',
                    backgroundColor: 'rgba(76, 138, 137, 0.1)',
                    fill: true,
                    tension: 0.4
                }, {
                    label: 'Detection Accuracy (%)',
                    data: [82, 85, 84, 88, 87, 89],
                    borderColor: '#22c55e',
                    borderDash: [5, 5],
                    fill: false,
                    yAxisID: 'y1'
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
                        type: 'linear',
                        display: true,
                        position: 'left',
                        beginAtZero: true,
                        title: { display: true, text: 'Count' }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        min: 70,
                        max: 100,
                        grid: { drawOnChartArea: false },
                        title: { display: true, text: 'Accuracy (%)' }
                    }
                }
            }
        });
    </script>
</body>
</html>
