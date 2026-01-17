<?php
// Authentication check - must be at the top of every admin page
require_once '../../api/middleware/auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clearance Metrics | Crime Dep.</title>
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
        .clearance-card {
            background: var(--card-bg-1);
            border: 1px solid var(--border-color-1);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .clearance-card-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .clearance-card-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .clearance-card-icon.danger { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
        .clearance-card-icon.warning { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        .clearance-card-icon.info { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
        .clearance-card-icon.success { background: rgba(34, 197, 94, 0.1); color: #22c55e; }
        .clearance-card-icon.primary { background: rgba(76, 138, 137, 0.1); color: #4c8a89; }

        .clearance-card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-color-1);
            margin: 0;
        }

        .clearance-card-subtitle {
            font-size: 0.875rem;
            color: var(--text-secondary-1);
            margin: 0;
        }

        .clearance-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .clearance-stat {
            background: var(--card-bg-1);
            border: 1px solid var(--border-color-1);
            border-radius: 12px;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        .clearance-stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.75rem;
        }

        .clearance-stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .clearance-stat-change {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .clearance-stat-change.up { background: rgba(34, 197, 94, 0.1); color: #22c55e; }
        .clearance-stat-change.down { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
        .clearance-stat-change.neutral { background: rgba(107, 114, 128, 0.1); color: #6b7280; }

        .clearance-stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-color-1);
            margin-bottom: 0.25rem;
        }

        .clearance-stat-label {
            font-size: 0.875rem;
            color: var(--text-secondary-1);
        }

        .clearance-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--border-color-1);
        }

        .clearance-progress-bar {
            height: 100%;
            transition: width 0.5s ease;
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

        .crime-type-clearance-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .clearance-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: var(--bg-color-1);
            border-radius: 8px;
        }

        .clearance-item-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .clearance-item-info {
            flex: 1;
        }

        .clearance-item-name {
            font-weight: 600;
            color: var(--text-color-1);
            font-size: 0.95rem;
            margin-bottom: 0.25rem;
        }

        .clearance-item-stats {
            font-size: 0.8rem;
            color: var(--text-secondary-1);
        }

        .clearance-item-rate {
            text-align: right;
        }

        .clearance-item-percentage {
            font-size: 1.25rem;
            font-weight: 700;
        }

        .clearance-item-percentage.high { color: #22c55e; }
        .clearance-item-percentage.medium { color: #f59e0b; }
        .clearance-item-percentage.low { color: #ef4444; }

        .clearance-item-bar {
            width: 80px;
            height: 6px;
            background: var(--border-color-1);
            border-radius: 3px;
            margin-top: 0.5rem;
            overflow: hidden;
        }

        .clearance-item-bar-fill {
            height: 100%;
            border-radius: 3px;
        }

        .status-tracking-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 1rem;
        }

        .status-card {
            background: var(--bg-color-1);
            border-radius: 8px;
            padding: 1rem;
            text-align: center;
            border-left: 4px solid;
        }

        .status-card.pending { border-left-color: #f59e0b; }
        .status-card.investigating { border-left-color: #3b82f6; }
        .status-card.solved { border-left-color: #22c55e; }
        .status-card.closed { border-left-color: #6b7280; }

        .status-card-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-color-1);
        }

        .status-card-label {
            font-size: 0.8rem;
            color: var(--text-secondary-1);
            margin-top: 0.25rem;
        }

        .status-card-percentage {
            font-size: 0.75rem;
            margin-top: 0.5rem;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            display: inline-block;
        }

        .status-card.pending .status-card-percentage { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        .status-card.investigating .status-card-percentage { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
        .status-card.solved .status-card-percentage { background: rgba(34, 197, 94, 0.1); color: #22c55e; }
        .status-card.closed .status-card-percentage { background: rgba(107, 114, 128, 0.1); color: #6b7280; }

        .avg-time-card {
            background: var(--bg-color-1);
            border-radius: 8px;
            padding: 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .avg-time-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
        }

        .avg-time-info {
            flex: 1;
        }

        .avg-time-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-color-1);
        }

        .avg-time-label {
            font-size: 0.85rem;
            color: var(--text-secondary-1);
        }

        .avg-time-trend {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
        }

        .avg-time-trend.improved { background: rgba(34, 197, 94, 0.1); color: #22c55e; }
        .avg-time-trend.declined { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

        .formula-box {
            background: var(--bg-color-1);
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1rem;
            font-family: monospace;
            font-size: 0.9rem;
            color: var(--text-secondary-1);
        }

        .formula-box code {
            color: var(--primary-color-1);
            font-weight: 600;
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
                            <span>Clearance Metrics</span>
                        </li>
                    </ol>
                </nav>
                <h1>Clearance Rate Metrics - Quezon City</h1>
                <p>Track case clearance rates, monitor solved vs unsolved crimes, analyze clearance by crime type, and measure average clearance time for effective law enforcement assessment.</p>
            </div>

            <div class="sub-container">
                <div class="page-content">
                    <!-- Key Clearance Statistics -->
                    <div class="clearance-stats">
                        <div class="clearance-stat">
                            <div class="clearance-stat-header">
                                <div class="clearance-stat-icon" style="background: rgba(34, 197, 94, 0.1); color: #22c55e;">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <span class="clearance-stat-change up">
                                    <i class="fas fa-arrow-up"></i> +4.2%
                                </span>
                            </div>
                            <div class="clearance-stat-value">68.5%</div>
                            <div class="clearance-stat-label">Overall Clearance Rate</div>
                            <div class="clearance-progress">
                                <div class="clearance-progress-bar" style="width: 68.5%; background: #22c55e;"></div>
                            </div>
                        </div>

                        <div class="clearance-stat">
                            <div class="clearance-stat-header">
                                <div class="clearance-stat-icon" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                                    <i class="fas fa-gavel"></i>
                                </div>
                                <span class="clearance-stat-change up">
                                    <i class="fas fa-arrow-up"></i> +127
                                </span>
                            </div>
                            <div class="clearance-stat-value">17,174</div>
                            <div class="clearance-stat-label">Total Cases Cleared</div>
                            <div class="clearance-progress">
                                <div class="clearance-progress-bar" style="width: 68.5%; background: #3b82f6;"></div>
                            </div>
                        </div>

                        <div class="clearance-stat">
                            <div class="clearance-stat-header">
                                <div class="clearance-stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <span class="clearance-stat-change up">
                                    <i class="fas fa-arrow-down"></i> -2.3 days
                                </span>
                            </div>
                            <div class="clearance-stat-value">18.7</div>
                            <div class="clearance-stat-label">Avg. Days to Clear</div>
                            <div class="clearance-progress">
                                <div class="clearance-progress-bar" style="width: 75%; background: #f59e0b;"></div>
                            </div>
                        </div>

                        <div class="clearance-stat">
                            <div class="clearance-stat-header">
                                <div class="clearance-stat-icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                                    <i class="fas fa-folder-open"></i>
                                </div>
                                <span class="clearance-stat-change down">
                                    <i class="fas fa-arrow-down"></i> -89
                                </span>
                            </div>
                            <div class="clearance-stat-value">7,897</div>
                            <div class="clearance-stat-label">Pending Cases</div>
                            <div class="clearance-progress">
                                <div class="clearance-progress-bar" style="width: 31.5%; background: #ef4444;"></div>
                            </div>
                        </div>

                        <div class="clearance-stat">
                            <div class="clearance-stat-header">
                                <div class="clearance-stat-icon" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                                    <i class="fas fa-balance-scale"></i>
                                </div>
                                <span class="clearance-stat-change up">
                                    <i class="fas fa-arrow-up"></i> +2.8%
                                </span>
                            </div>
                            <div class="clearance-stat-value">72.3%</div>
                            <div class="clearance-stat-label">Conviction Rate</div>
                            <div class="clearance-progress">
                                <div class="clearance-progress-bar" style="width: 72.3%; background: #8b5cf6;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Clearance Rate Trend Chart -->
                    <div class="clearance-card">
                        <div class="clearance-card-header">
                            <div class="clearance-card-icon success">
                                <i class="fas fa-chart-area"></i>
                            </div>
                            <div>
                                <h3 class="clearance-card-title">Clearance Rate Trends</h3>
                                <p class="clearance-card-subtitle">Monthly clearance rate progression over the past 12 months</p>
                            </div>
                        </div>
                        <div class="chart-container">
                            <canvas id="clearanceTrendChart"></canvas>
                        </div>
                    </div>

                    <div class="two-column-layout">
                        <!-- Clearance by Crime Type -->
                        <div class="clearance-card">
                            <div class="clearance-card-header">
                                <div class="clearance-card-icon warning">
                                    <i class="fas fa-list-alt"></i>
                                </div>
                                <div>
                                    <h3 class="clearance-card-title">Clearance by Crime Type</h3>
                                    <p class="clearance-card-subtitle">Clearance rates breakdown by category</p>
                                </div>
                            </div>
                            <div class="chart-container-small">
                                <canvas id="crimeTypeClearanceChart"></canvas>
                            </div>
                        </div>

                        <!-- Case Status Tracking -->
                        <div class="clearance-card">
                            <div class="clearance-card-header">
                                <div class="clearance-card-icon info">
                                    <i class="fas fa-tasks"></i>
                                </div>
                                <div>
                                    <h3 class="clearance-card-title">Case Status Tracking</h3>
                                    <p class="clearance-card-subtitle">Current distribution of case statuses</p>
                                </div>
                            </div>
                            <div class="status-tracking-grid">
                                <div class="status-card pending">
                                    <div class="status-card-value">3,218</div>
                                    <div class="status-card-label">Pending</div>
                                    <span class="status-card-percentage">12.8%</span>
                                </div>
                                <div class="status-card investigating">
                                    <div class="status-card-value">4,679</div>
                                    <div class="status-card-label">Investigating</div>
                                    <span class="status-card-percentage">18.7%</span>
                                </div>
                                <div class="status-card solved">
                                    <div class="status-card-value">12,847</div>
                                    <div class="status-card-label">Solved</div>
                                    <span class="status-card-percentage">51.2%</span>
                                </div>
                                <div class="status-card closed">
                                    <div class="status-card-value">4,327</div>
                                    <div class="status-card-label">Closed</div>
                                    <span class="status-card-percentage">17.3%</span>
                                </div>
                            </div>
                            <div class="formula-box" style="margin-top: 1.5rem;">
                                <p><strong>Clearance Rate Formula:</strong></p>
                                <code>Clearance Rate = (Solved Cases / Total Cases) × 100</code>
                                <br><br>
                                <p><strong>Current Calculation:</strong></p>
                                <code>(17,174 / 25,071) × 100 = 68.5%</code>
                            </div>
                        </div>
                    </div>

                    <!-- Clearance by Crime Type List -->
                    <div class="clearance-card">
                        <div class="clearance-card-header">
                            <div class="clearance-card-icon primary">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                            <div>
                                <h3 class="clearance-card-title">Detailed Clearance by Crime Type</h3>
                                <p class="clearance-card-subtitle">Solved vs total cases for each crime category</p>
                            </div>
                        </div>
                        <div class="crime-type-clearance-list">
                            <div class="clearance-item">
                                <div class="clearance-item-icon" style="background: rgba(34, 197, 94, 0.1); color: #22c55e;">
                                    <i class="fas fa-car-crash"></i>
                                </div>
                                <div class="clearance-item-info">
                                    <div class="clearance-item-name">Vandalism</div>
                                    <div class="clearance-item-stats">892 solved / 1,024 total cases</div>
                                </div>
                                <div class="clearance-item-rate">
                                    <div class="clearance-item-percentage high">87.1%</div>
                                    <div class="clearance-item-bar">
                                        <div class="clearance-item-bar-fill" style="width: 87.1%; background: #22c55e;"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="clearance-item">
                                <div class="clearance-item-icon" style="background: rgba(34, 197, 94, 0.1); color: #22c55e;">
                                    <i class="fas fa-fist-raised"></i>
                                </div>
                                <div class="clearance-item-info">
                                    <div class="clearance-item-name">Assault</div>
                                    <div class="clearance-item-stats">2,847 solved / 3,456 total cases</div>
                                </div>
                                <div class="clearance-item-rate">
                                    <div class="clearance-item-percentage high">82.4%</div>
                                    <div class="clearance-item-bar">
                                        <div class="clearance-item-bar-fill" style="width: 82.4%; background: #22c55e;"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="clearance-item">
                                <div class="clearance-item-icon" style="background: rgba(34, 197, 94, 0.1); color: #22c55e;">
                                    <i class="fas fa-pills"></i>
                                </div>
                                <div class="clearance-item-info">
                                    <div class="clearance-item-name">Drug-Related</div>
                                    <div class="clearance-item-stats">1,654 solved / 2,134 total cases</div>
                                </div>
                                <div class="clearance-item-rate">
                                    <div class="clearance-item-percentage high">77.5%</div>
                                    <div class="clearance-item-bar">
                                        <div class="clearance-item-bar-fill" style="width: 77.5%; background: #22c55e;"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="clearance-item">
                                <div class="clearance-item-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                                    <i class="fas fa-home"></i>
                                </div>
                                <div class="clearance-item-info">
                                    <div class="clearance-item-name">Burglary</div>
                                    <div class="clearance-item-stats">1,923 solved / 2,789 total cases</div>
                                </div>
                                <div class="clearance-item-rate">
                                    <div class="clearance-item-percentage medium">68.9%</div>
                                    <div class="clearance-item-bar">
                                        <div class="clearance-item-bar-fill" style="width: 68.9%; background: #f59e0b;"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="clearance-item">
                                <div class="clearance-item-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                                    <i class="fas fa-hand-holding-usd"></i>
                                </div>
                                <div class="clearance-item-info">
                                    <div class="clearance-item-name">Robbery</div>
                                    <div class="clearance-item-stats">3,245 solved / 4,892 total cases</div>
                                </div>
                                <div class="clearance-item-rate">
                                    <div class="clearance-item-percentage medium">66.3%</div>
                                    <div class="clearance-item-bar">
                                        <div class="clearance-item-bar-fill" style="width: 66.3%; background: #f59e0b;"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="clearance-item">
                                <div class="clearance-item-icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                                    <i class="fas fa-shopping-bag"></i>
                                </div>
                                <div class="clearance-item-info">
                                    <div class="clearance-item-name">Theft</div>
                                    <div class="clearance-item-stats">4,892 solved / 8,436 total cases</div>
                                </div>
                                <div class="clearance-item-rate">
                                    <div class="clearance-item-percentage low">58.0%</div>
                                    <div class="clearance-item-bar">
                                        <div class="clearance-item-bar-fill" style="width: 58%; background: #ef4444;"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="clearance-item">
                                <div class="clearance-item-icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                                    <i class="fas fa-credit-card"></i>
                                </div>
                                <div class="clearance-item-info">
                                    <div class="clearance-item-name">Fraud</div>
                                    <div class="clearance-item-stats">1,721 solved / 2,340 total cases</div>
                                </div>
                                <div class="clearance-item-rate">
                                    <div class="clearance-item-percentage low">45.2%</div>
                                    <div class="clearance-item-bar">
                                        <div class="clearance-item-bar-fill" style="width: 45.2%; background: #ef4444;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="two-column-layout">
                        <!-- Average Clearance Time -->
                        <div class="clearance-card">
                            <div class="clearance-card-header">
                                <div class="clearance-card-icon warning">
                                    <i class="fas fa-hourglass-half"></i>
                                </div>
                                <div>
                                    <h3 class="clearance-card-title">Average Clearance Time</h3>
                                    <p class="clearance-card-subtitle">Time taken to clear cases by crime type</p>
                                </div>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 1rem;">
                                <div class="avg-time-card">
                                    <div class="avg-time-icon" style="background: rgba(34, 197, 94, 0.1); color: #22c55e;">
                                        <i class="fas fa-car-crash"></i>
                                    </div>
                                    <div class="avg-time-info">
                                        <div class="avg-time-value">8.2 days</div>
                                        <div class="avg-time-label">Vandalism</div>
                                    </div>
                                    <span class="avg-time-trend improved"><i class="fas fa-arrow-down"></i> -1.5 days</span>
                                </div>
                                <div class="avg-time-card">
                                    <div class="avg-time-icon" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                                        <i class="fas fa-fist-raised"></i>
                                    </div>
                                    <div class="avg-time-info">
                                        <div class="avg-time-value">12.4 days</div>
                                        <div class="avg-time-label">Assault</div>
                                    </div>
                                    <span class="avg-time-trend improved"><i class="fas fa-arrow-down"></i> -2.1 days</span>
                                </div>
                                <div class="avg-time-card">
                                    <div class="avg-time-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                                        <i class="fas fa-home"></i>
                                    </div>
                                    <div class="avg-time-info">
                                        <div class="avg-time-value">21.7 days</div>
                                        <div class="avg-time-label">Burglary</div>
                                    </div>
                                    <span class="avg-time-trend declined"><i class="fas fa-arrow-up"></i> +0.8 days</span>
                                </div>
                                <div class="avg-time-card">
                                    <div class="avg-time-icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                                        <i class="fas fa-credit-card"></i>
                                    </div>
                                    <div class="avg-time-info">
                                        <div class="avg-time-value">34.5 days</div>
                                        <div class="avg-time-label">Fraud</div>
                                    </div>
                                    <span class="avg-time-trend declined"><i class="fas fa-arrow-up"></i> +3.2 days</span>
                                </div>
                            </div>
                        </div>

                        <!-- Monthly Comparison -->
                        <div class="clearance-card">
                            <div class="clearance-card-header">
                                <div class="clearance-card-icon info">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <div>
                                    <h3 class="clearance-card-title">Monthly Clearance Comparison</h3>
                                    <p class="clearance-card-subtitle">Cases cleared vs new cases received</p>
                                </div>
                            </div>
                            <div class="chart-container-small">
                                <canvas id="monthlyClearanceChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Year-over-Year Clearance -->
                    <div class="clearance-card">
                        <div class="clearance-card-header">
                            <div class="clearance-card-icon primary">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <div>
                                <h3 class="clearance-card-title">Year-over-Year Clearance Rate</h3>
                                <p class="clearance-card-subtitle">Historical clearance rate performance (2021-2025)</p>
                            </div>
                        </div>
                        <div class="chart-container">
                            <canvas id="yearOverYearChart"></canvas>
                        </div>
                    </div>

                    <!-- Legend -->
                    <div class="legend">
                        <div class="legend-header">
                            <h3><i class="fas fa-info-circle"></i> Clearance Rate Legend</h3>
                        </div>
                        <div class="legend-grid">
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #22c55e;"></div>
                                <span class="legend-label">High Clearance (> 75%)</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #f59e0b;"></div>
                                <span class="legend-label">Medium Clearance (50-75%)</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #ef4444;"></div>
                                <span class="legend-label">Low Clearance (< 50%)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include '../includes/admin-footer.php' ?>
    </div>

    <script>
        // Clearance Trend Chart
        const trendCtx = document.getElementById('clearanceTrendChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Clearance Rate (%)',
                    data: [62.3, 63.8, 64.5, 65.2, 66.1, 66.8, 67.2, 67.5, 67.8, 68.0, 68.2, 68.5],
                    borderColor: '#22c55e',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    fill: true,
                    tension: 0.4
                }, {
                    label: 'Target Rate (%)',
                    data: [70, 70, 70, 70, 70, 70, 70, 70, 70, 70, 70, 70],
                    borderColor: '#4c8a89',
                    borderDash: [5, 5],
                    fill: false,
                    pointRadius: 0
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
                        beginAtZero: false,
                        min: 50,
                        max: 80,
                        title: { display: true, text: 'Clearance Rate (%)' }
                    }
                }
            }
        });

        // Crime Type Clearance Chart
        const crimeTypeCtx = document.getElementById('crimeTypeClearanceChart').getContext('2d');
        new Chart(crimeTypeCtx, {
            type: 'bar',
            data: {
                labels: ['Vandalism', 'Assault', 'Drug', 'Burglary', 'Robbery', 'Theft', 'Fraud'],
                datasets: [{
                    label: 'Clearance Rate (%)',
                    data: [87.1, 82.4, 77.5, 68.9, 66.3, 58.0, 45.2],
                    backgroundColor: [
                        '#22c55e', '#22c55e', '#22c55e', '#f59e0b', '#f59e0b', '#ef4444', '#ef4444'
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
                        max: 100,
                        title: { display: true, text: 'Clearance Rate (%)' }
                    }
                }
            }
        });

        // Monthly Clearance Comparison Chart
        const monthlyCtx = document.getElementById('monthlyClearanceChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Cases Cleared',
                    data: [1245, 1320, 1456, 1389, 1478, 1523, 1489, 1567, 1498, 1534, 1412, 1463],
                    backgroundColor: '#22c55e',
                    borderRadius: 4
                }, {
                    label: 'New Cases',
                    data: [1890, 1756, 2012, 1934, 2087, 2156, 2234, 2189, 2067, 2145, 1978, 2123],
                    backgroundColor: '#3b82f6',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Number of Cases' }
                    }
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
                    label: 'Clearance Rate (%)',
                    data: [58.2, 61.5, 64.3, 66.8, 68.5],
                    backgroundColor: '#4c8a89',
                    borderRadius: 6
                }, {
                    label: 'Cases Cleared',
                    data: [12450, 13890, 15234, 16547, 17174],
                    backgroundColor: '#22c55e',
                    borderRadius: 6,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        beginAtZero: false,
                        min: 50,
                        max: 80,
                        title: { display: true, text: 'Clearance Rate (%)' }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        beginAtZero: true,
                        grid: { drawOnChartArea: false },
                        title: { display: true, text: 'Cases Cleared' }
                    }
                }
            }
        });
    </script>
</body>
</html>
