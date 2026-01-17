<?php
// Authentication check - must be at the top of every admin page
require_once '../../api/middleware/auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Response Metrics | Crime Dep.</title>
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
        .response-card {
            background: var(--card-bg-1);
            border: 1px solid var(--border-color-1);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .response-card-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .response-card-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .response-card-icon.danger { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
        .response-card-icon.warning { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        .response-card-icon.info { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
        .response-card-icon.success { background: rgba(34, 197, 94, 0.1); color: #22c55e; }
        .response-card-icon.primary { background: rgba(76, 138, 137, 0.1); color: #4c8a89; }

        .response-card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-color-1);
            margin: 0;
        }

        .response-card-subtitle {
            font-size: 0.875rem;
            color: var(--text-secondary-1);
            margin: 0;
        }

        .response-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .response-stat {
            background: var(--card-bg-1);
            border: 1px solid var(--border-color-1);
            border-radius: 12px;
            padding: 1.5rem;
            position: relative;
        }

        .response-stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.75rem;
        }

        .response-stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .response-stat-change {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .response-stat-change.improved { background: rgba(34, 197, 94, 0.1); color: #22c55e; }
        .response-stat-change.declined { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
        .response-stat-change.neutral { background: rgba(107, 114, 128, 0.1); color: #6b7280; }

        .response-stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-color-1);
            margin-bottom: 0.25rem;
        }

        .response-stat-unit {
            font-size: 1rem;
            font-weight: 400;
            color: var(--text-secondary-1);
        }

        .response-stat-label {
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

        .priority-response-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .priority-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: var(--bg-color-1);
            border-radius: 8px;
            border-left: 4px solid;
        }

        .priority-item.critical { border-left-color: #dc2626; }
        .priority-item.high { border-left-color: #ef4444; }
        .priority-item.medium { border-left-color: #f59e0b; }
        .priority-item.low { border-left-color: #22c55e; }

        .priority-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .priority-info {
            flex: 1;
        }

        .priority-name {
            font-weight: 600;
            color: var(--text-color-1);
            font-size: 0.95rem;
            margin-bottom: 0.25rem;
        }

        .priority-benchmark {
            font-size: 0.8rem;
            color: var(--text-secondary-1);
        }

        .priority-time {
            text-align: right;
        }

        .priority-time-value {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .priority-time-value.excellent { color: #22c55e; }
        .priority-time-value.good { color: #3b82f6; }
        .priority-time-value.fair { color: #f59e0b; }
        .priority-time-value.poor { color: #ef4444; }

        .priority-time-label {
            font-size: 0.75rem;
            color: var(--text-secondary-1);
        }

        .priority-status {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 600;
            margin-top: 0.25rem;
            display: inline-block;
        }

        .priority-status.met { background: rgba(34, 197, 94, 0.1); color: #22c55e; }
        .priority-status.close { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        .priority-status.missed { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

        .district-response-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
        }

        .district-card {
            background: var(--bg-color-1);
            border-radius: 8px;
            padding: 1rem;
            text-align: center;
        }

        .district-name {
            font-weight: 600;
            color: var(--text-color-1);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .district-time {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .district-time.fast { color: #22c55e; }
        .district-time.normal { color: #3b82f6; }
        .district-time.slow { color: #f59e0b; }
        .district-time.critical { color: #ef4444; }

        .district-label {
            font-size: 0.75rem;
            color: var(--text-secondary-1);
        }

        .district-trend {
            margin-top: 0.5rem;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.25rem;
        }

        .district-trend.improved { color: #22c55e; }
        .district-trend.declined { color: #ef4444; }

        .benchmark-table {
            width: 100%;
            border-collapse: collapse;
        }

        .benchmark-table th,
        .benchmark-table td {
            padding: 0.875rem 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color-1);
        }

        .benchmark-table th {
            font-weight: 600;
            color: var(--text-color-1);
            background: var(--bg-color-1);
            font-size: 0.85rem;
        }

        .benchmark-table td {
            color: var(--text-secondary-1);
        }

        .benchmark-badge {
            padding: 0.375rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .benchmark-badge.excellent { background: rgba(34, 197, 94, 0.1); color: #22c55e; }
        .benchmark-badge.good { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
        .benchmark-badge.fair { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        .benchmark-badge.poor { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

        .time-breakdown {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.5rem;
            margin-top: 1rem;
            padding: 1rem;
            background: var(--bg-color-1);
            border-radius: 8px;
        }

        .time-segment {
            text-align: center;
            padding: 0.75rem;
            border-right: 1px solid var(--border-color-1);
        }

        .time-segment:last-child {
            border-right: none;
        }

        .time-segment-value {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-color-1);
        }

        .time-segment-label {
            font-size: 0.7rem;
            color: var(--text-secondary-1);
            margin-top: 0.25rem;
        }

        .response-timeline {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.5rem;
            background: var(--bg-color-1);
            border-radius: 8px;
            margin-top: 1rem;
            position: relative;
        }

        .response-timeline::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 80px;
            right: 80px;
            height: 4px;
            background: linear-gradient(to right, #22c55e, #3b82f6, #f59e0b, #ef4444);
            border-radius: 2px;
            transform: translateY(-50%);
        }

        .timeline-point {
            display: flex;
            flex-direction: column;
            align-items: center;
            z-index: 1;
        }

        .timeline-dot {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 3px solid var(--card-bg-1);
            margin-bottom: 0.5rem;
        }

        .timeline-dot.start { background: #22c55e; }
        .timeline-dot.dispatch { background: #3b82f6; }
        .timeline-dot.enroute { background: #f59e0b; }
        .timeline-dot.arrival { background: #ef4444; }

        .timeline-label {
            font-size: 0.75rem;
            color: var(--text-secondary-1);
            text-align: center;
        }

        .timeline-time {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-color-1);
        }

        @media (max-width: 1200px) {
            .three-column-layout { grid-template-columns: repeat(2, 1fr); }
            .time-breakdown { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 992px) {
            .two-column-layout,
            .three-column-layout { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .time-breakdown { grid-template-columns: 1fr; }
            .time-segment { border-right: none; border-bottom: 1px solid var(--border-color-1); }
            .time-segment:last-child { border-bottom: none; }
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
                            <span>Response Metrics</span>
                        </li>
                    </ol>
                </nav>
                <h1>Response Time Metrics - Quezon City</h1>
                <p>Monitor and analyze police response times across different priority levels, districts, and time periods. Track performance against benchmarks for efficient emergency response.</p>
            </div>

            <div class="sub-container">
                <div class="page-content">
                    <!-- Key Response Statistics -->
                    <div class="response-stats">
                        <div class="response-stat">
                            <div class="response-stat-header">
                                <div class="response-stat-icon" style="background: rgba(76, 138, 137, 0.1); color: #4c8a89;">
                                    <i class="fas fa-stopwatch"></i>
                                </div>
                                <span class="response-stat-change improved">
                                    <i class="fas fa-arrow-down"></i> -1.2 min
                                </span>
                            </div>
                            <div class="response-stat-value">8.4 <span class="response-stat-unit">min</span></div>
                            <div class="response-stat-label">Average Response Time</div>
                        </div>

                        <div class="response-stat">
                            <div class="response-stat-header">
                                <div class="response-stat-icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                                    <i class="fas fa-bolt"></i>
                                </div>
                                <span class="response-stat-change improved">
                                    <i class="fas fa-arrow-down"></i> -0.8 min
                                </span>
                            </div>
                            <div class="response-stat-value">4.2 <span class="response-stat-unit">min</span></div>
                            <div class="response-stat-label">Critical Priority Response</div>
                        </div>

                        <div class="response-stat">
                            <div class="response-stat-header">
                                <div class="response-stat-icon" style="background: rgba(34, 197, 94, 0.1); color: #22c55e;">
                                    <i class="fas fa-check-double"></i>
                                </div>
                                <span class="response-stat-change improved">
                                    <i class="fas fa-arrow-up"></i> +3.2%
                                </span>
                            </div>
                            <div class="response-stat-value">87.5<span class="response-stat-unit">%</span></div>
                            <div class="response-stat-label">Benchmark Met Rate</div>
                        </div>

                        <div class="response-stat">
                            <div class="response-stat-header">
                                <div class="response-stat-icon" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                                    <i class="fas fa-car"></i>
                                </div>
                                <span class="response-stat-change neutral">
                                    <i class="fas fa-minus"></i> 0
                                </span>
                            </div>
                            <div class="response-stat-value">156</div>
                            <div class="response-stat-label">Active Patrol Units</div>
                        </div>

                        <div class="response-stat">
                            <div class="response-stat-header">
                                <div class="response-stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                                    <i class="fas fa-phone-alt"></i>
                                </div>
                                <span class="response-stat-change declined">
                                    <i class="fas fa-arrow-up"></i> +127
                                </span>
                            </div>
                            <div class="response-stat-value">2,847</div>
                            <div class="response-stat-label">Calls This Month</div>
                        </div>
                    </div>

                    <!-- Response Time Breakdown -->
                    <div class="response-card">
                        <div class="response-card-header">
                            <div class="response-card-icon info">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <h3 class="response-card-title">Response Time Breakdown</h3>
                                <p class="response-card-subtitle">Average time for each phase of emergency response</p>
                            </div>
                        </div>
                        <div class="response-timeline">
                            <div class="timeline-point">
                                <div class="timeline-dot start"></div>
                                <div class="timeline-time">0:00</div>
                                <div class="timeline-label">Call Received</div>
                            </div>
                            <div class="timeline-point">
                                <div class="timeline-dot dispatch"></div>
                                <div class="timeline-time">1:24</div>
                                <div class="timeline-label">Unit Dispatched</div>
                            </div>
                            <div class="timeline-point">
                                <div class="timeline-dot enroute"></div>
                                <div class="timeline-time">2:18</div>
                                <div class="timeline-label">En Route</div>
                            </div>
                            <div class="timeline-point">
                                <div class="timeline-dot arrival"></div>
                                <div class="timeline-time">8:24</div>
                                <div class="timeline-label">On Scene</div>
                            </div>
                        </div>
                        <div class="time-breakdown">
                            <div class="time-segment">
                                <div class="time-segment-value">1:24</div>
                                <div class="time-segment-label">Call Processing</div>
                            </div>
                            <div class="time-segment">
                                <div class="time-segment-value">0:54</div>
                                <div class="time-segment-label">Dispatch Time</div>
                            </div>
                            <div class="time-segment">
                                <div class="time-segment-value">6:06</div>
                                <div class="time-segment-label">Travel Time</div>
                            </div>
                            <div class="time-segment">
                                <div class="time-segment-value">8:24</div>
                                <div class="time-segment-label">Total Response</div>
                            </div>
                        </div>
                    </div>

                    <!-- Response Time Trend Chart -->
                    <div class="response-card">
                        <div class="response-card-header">
                            <div class="response-card-icon primary">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div>
                                <h3 class="response-card-title">Response Time Trends</h3>
                                <p class="response-card-subtitle">Average response time over the last 12 months</p>
                            </div>
                        </div>
                        <div class="chart-container">
                            <canvas id="responseTrendChart"></canvas>
                        </div>
                    </div>

                    <div class="two-column-layout">
                        <!-- Response by Priority -->
                        <div class="response-card">
                            <div class="response-card-header">
                                <div class="response-card-icon danger">
                                    <i class="fas fa-exclamation-circle"></i>
                                </div>
                                <div>
                                    <h3 class="response-card-title">Response Time by Priority</h3>
                                    <p class="response-card-subtitle">Performance against priority-based benchmarks</p>
                                </div>
                            </div>
                            <div class="priority-response-list">
                                <div class="priority-item critical">
                                    <div class="priority-icon" style="background: rgba(220, 38, 38, 0.1); color: #dc2626;">
                                        <i class="fas fa-skull-crossbones"></i>
                                    </div>
                                    <div class="priority-info">
                                        <div class="priority-name">Critical (Life Threatening)</div>
                                        <div class="priority-benchmark">Benchmark: 5 min | Actual: 4.2 min</div>
                                    </div>
                                    <div class="priority-time">
                                        <div class="priority-time-value excellent">4.2</div>
                                        <div class="priority-time-label">minutes</div>
                                        <span class="priority-status met">BENCHMARK MET</span>
                                    </div>
                                </div>

                                <div class="priority-item high">
                                    <div class="priority-icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                    <div class="priority-info">
                                        <div class="priority-name">High (In Progress Crime)</div>
                                        <div class="priority-benchmark">Benchmark: 8 min | Actual: 7.1 min</div>
                                    </div>
                                    <div class="priority-time">
                                        <div class="priority-time-value good">7.1</div>
                                        <div class="priority-time-label">minutes</div>
                                        <span class="priority-status met">BENCHMARK MET</span>
                                    </div>
                                </div>

                                <div class="priority-item medium">
                                    <div class="priority-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                                        <i class="fas fa-info-circle"></i>
                                    </div>
                                    <div class="priority-info">
                                        <div class="priority-name">Medium (Just Occurred)</div>
                                        <div class="priority-benchmark">Benchmark: 15 min | Actual: 12.8 min</div>
                                    </div>
                                    <div class="priority-time">
                                        <div class="priority-time-value good">12.8</div>
                                        <div class="priority-time-label">minutes</div>
                                        <span class="priority-status met">BENCHMARK MET</span>
                                    </div>
                                </div>

                                <div class="priority-item low">
                                    <div class="priority-icon" style="background: rgba(34, 197, 94, 0.1); color: #22c55e;">
                                        <i class="fas fa-clipboard-list"></i>
                                    </div>
                                    <div class="priority-info">
                                        <div class="priority-name">Low (Report/Non-Emergency)</div>
                                        <div class="priority-benchmark">Benchmark: 30 min | Actual: 24.5 min</div>
                                    </div>
                                    <div class="priority-time">
                                        <div class="priority-time-value excellent">24.5</div>
                                        <div class="priority-time-label">minutes</div>
                                        <span class="priority-status met">BENCHMARK MET</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Response by Priority Chart -->
                        <div class="response-card">
                            <div class="response-card-header">
                                <div class="response-card-icon warning">
                                    <i class="fas fa-chart-bar"></i>
                                </div>
                                <div>
                                    <h3 class="response-card-title">Priority Comparison</h3>
                                    <p class="response-card-subtitle">Actual vs benchmark response times</p>
                                </div>
                            </div>
                            <div class="chart-container-small">
                                <canvas id="priorityComparisonChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Response Time by District -->
                    <div class="response-card">
                        <div class="response-card-header">
                            <div class="response-card-icon success">
                                <i class="fas fa-map-marked-alt"></i>
                            </div>
                            <div>
                                <h3 class="response-card-title">Response Time by District</h3>
                                <p class="response-card-subtitle">Average response time across different areas</p>
                            </div>
                        </div>
                        <div class="district-response-grid">
                            <div class="district-card">
                                <div class="district-name">Diliman</div>
                                <div class="district-time fast">6.2</div>
                                <div class="district-label">minutes avg</div>
                                <div class="district-trend improved">
                                    <i class="fas fa-arrow-down"></i> -0.8 min
                                </div>
                            </div>
                            <div class="district-card">
                                <div class="district-name">Cubao</div>
                                <div class="district-time normal">7.8</div>
                                <div class="district-label">minutes avg</div>
                                <div class="district-trend improved">
                                    <i class="fas fa-arrow-down"></i> -1.2 min
                                </div>
                            </div>
                            <div class="district-card">
                                <div class="district-name">Commonwealth</div>
                                <div class="district-time normal">8.5</div>
                                <div class="district-label">minutes avg</div>
                                <div class="district-trend improved">
                                    <i class="fas fa-arrow-down"></i> -0.5 min
                                </div>
                            </div>
                            <div class="district-card">
                                <div class="district-name">Fairview</div>
                                <div class="district-time slow">9.8</div>
                                <div class="district-label">minutes avg</div>
                                <div class="district-trend declined">
                                    <i class="fas fa-arrow-up"></i> +0.3 min
                                </div>
                            </div>
                            <div class="district-card">
                                <div class="district-name">Novaliches</div>
                                <div class="district-time slow">10.2</div>
                                <div class="district-label">minutes avg</div>
                                <div class="district-trend improved">
                                    <i class="fas fa-arrow-down"></i> -0.7 min
                                </div>
                            </div>
                            <div class="district-card">
                                <div class="district-name">Batasan Hills</div>
                                <div class="district-time critical">11.5</div>
                                <div class="district-label">minutes avg</div>
                                <div class="district-trend declined">
                                    <i class="fas fa-arrow-up"></i> +1.1 min
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="two-column-layout">
                        <!-- Hourly Response Pattern -->
                        <div class="response-card">
                            <div class="response-card-header">
                                <div class="response-card-icon info">
                                    <i class="fas fa-sun"></i>
                                </div>
                                <div>
                                    <h3 class="response-card-title">Hourly Response Pattern</h3>
                                    <p class="response-card-subtitle">Response time variation throughout the day</p>
                                </div>
                            </div>
                            <div class="chart-container-small">
                                <canvas id="hourlyPatternChart"></canvas>
                            </div>
                        </div>

                        <!-- Response Benchmark Table -->
                        <div class="response-card">
                            <div class="response-card-header">
                                <div class="response-card-icon primary">
                                    <i class="fas fa-trophy"></i>
                                </div>
                                <div>
                                    <h3 class="response-card-title">Response Time Benchmarks</h3>
                                    <p class="response-card-subtitle">Performance standards and current status</p>
                                </div>
                            </div>
                            <table class="benchmark-table">
                                <thead>
                                    <tr>
                                        <th>Metric</th>
                                        <th>Benchmark</th>
                                        <th>Current</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Critical Response</td>
                                        <td>&lt; 5 min</td>
                                        <td><strong>4.2 min</strong></td>
                                        <td><span class="benchmark-badge excellent">Excellent</span></td>
                                    </tr>
                                    <tr>
                                        <td>High Priority</td>
                                        <td>&lt; 8 min</td>
                                        <td><strong>7.1 min</strong></td>
                                        <td><span class="benchmark-badge good">Good</span></td>
                                    </tr>
                                    <tr>
                                        <td>Overall Average</td>
                                        <td>&lt; 10 min</td>
                                        <td><strong>8.4 min</strong></td>
                                        <td><span class="benchmark-badge good">Good</span></td>
                                    </tr>
                                    <tr>
                                        <td>Dispatch Time</td>
                                        <td>&lt; 2 min</td>
                                        <td><strong>1.4 min</strong></td>
                                        <td><span class="benchmark-badge excellent">Excellent</span></td>
                                    </tr>
                                    <tr>
                                        <td>Night Response</td>
                                        <td>&lt; 12 min</td>
                                        <td><strong>10.8 min</strong></td>
                                        <td><span class="benchmark-badge good">Good</span></td>
                                    </tr>
                                    <tr>
                                        <td>Rural Areas</td>
                                        <td>&lt; 15 min</td>
                                        <td><strong>12.3 min</strong></td>
                                        <td><span class="benchmark-badge good">Good</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Monthly Response Analysis -->
                    <div class="response-card">
                        <div class="response-card-header">
                            <div class="response-card-icon warning">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div>
                                <h3 class="response-card-title">Monthly Response Analysis</h3>
                                <p class="response-card-subtitle">Call volume and response time correlation</p>
                            </div>
                        </div>
                        <div class="chart-container">
                            <canvas id="monthlyAnalysisChart"></canvas>
                        </div>
                    </div>

                    <!-- Legend -->
                    <div class="legend">
                        <div class="legend-header">
                            <h3><i class="fas fa-info-circle"></i> Response Time Legend</h3>
                        </div>
                        <div class="legend-grid">
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #22c55e;"></div>
                                <span class="legend-label">Excellent (Under benchmark)</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #3b82f6;"></div>
                                <span class="legend-label">Good (Within benchmark)</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #f59e0b;"></div>
                                <span class="legend-label">Fair (Near benchmark limit)</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #ef4444;"></div>
                                <span class="legend-label">Poor (Above benchmark)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include '../includes/admin-footer.php' ?>
    </div>

    <script>
        // Response Time Trend Chart
        const trendCtx = document.getElementById('responseTrendChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Average Response Time (min)',
                    data: [9.8, 9.5, 9.2, 9.0, 8.8, 8.9, 8.7, 8.6, 8.5, 8.5, 8.4, 8.4],
                    borderColor: '#4c8a89',
                    backgroundColor: 'rgba(76, 138, 137, 0.1)',
                    fill: true,
                    tension: 0.4
                }, {
                    label: 'Target (min)',
                    data: [10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10],
                    borderColor: '#22c55e',
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
                        min: 6,
                        max: 12,
                        title: { display: true, text: 'Minutes' }
                    }
                }
            }
        });

        // Priority Comparison Chart
        const priorityCtx = document.getElementById('priorityComparisonChart').getContext('2d');
        new Chart(priorityCtx, {
            type: 'bar',
            data: {
                labels: ['Critical', 'High', 'Medium', 'Low'],
                datasets: [{
                    label: 'Actual (min)',
                    data: [4.2, 7.1, 12.8, 24.5],
                    backgroundColor: '#4c8a89',
                    borderRadius: 6
                }, {
                    label: 'Benchmark (min)',
                    data: [5, 8, 15, 30],
                    backgroundColor: '#94a3b8',
                    borderRadius: 6
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
                        title: { display: true, text: 'Minutes' }
                    }
                }
            }
        });

        // Hourly Pattern Chart
        const hourlyCtx = document.getElementById('hourlyPatternChart').getContext('2d');
        new Chart(hourlyCtx, {
            type: 'line',
            data: {
                labels: ['12AM', '2AM', '4AM', '6AM', '8AM', '10AM', '12PM', '2PM', '4PM', '6PM', '8PM', '10PM'],
                datasets: [{
                    label: 'Response Time (min)',
                    data: [10.5, 9.8, 8.2, 7.5, 8.8, 8.2, 7.9, 8.1, 9.2, 9.8, 10.2, 10.8],
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    fill: true,
                    tension: 0.4
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
                        max: 12,
                        title: { display: true, text: 'Minutes' }
                    }
                }
            }
        });

        // Monthly Analysis Chart
        const monthlyCtx = document.getElementById('monthlyAnalysisChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Call Volume',
                    data: [2456, 2312, 2678, 2534, 2789, 2890, 2945, 2867, 2756, 2834, 2712, 2847],
                    backgroundColor: '#3b82f6',
                    borderRadius: 4,
                    yAxisID: 'y'
                }, {
                    label: 'Avg Response (min)',
                    data: [9.8, 9.5, 9.2, 9.0, 8.8, 8.9, 8.7, 8.6, 8.5, 8.5, 8.4, 8.4],
                    type: 'line',
                    borderColor: '#ef4444',
                    backgroundColor: 'transparent',
                    tension: 0.4,
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
                        title: { display: true, text: 'Call Volume' }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        beginAtZero: false,
                        min: 6,
                        max: 12,
                        grid: { drawOnChartArea: false },
                        title: { display: true, text: 'Response Time (min)' }
                    }
                }
            }
        });
    </script>
</body>
</html>
