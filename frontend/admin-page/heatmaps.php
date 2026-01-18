<?php
// Authentication check - must be at the top of every admin page
require_once '../../api/middleware/auth.php';
require_once '../../api/config.php';

// Get crime categories for filter dropdown
$categoriesQuery = "SELECT id, category_code, category_name, color, icon FROM crime_department_crime_categories WHERE is_active = 1 ORDER BY category_name";
$categoriesResult = $mysqli->query($categoriesQuery);
$categories = [];
while ($row = $categoriesResult->fetch_assoc()) {
    $categories[] = $row;
}

// Get barangays for filter
$barangaysQuery = "SELECT id, barangay_name, district FROM crime_department_barangays WHERE is_active = 1 ORDER BY barangay_name";
$barangaysResult = $mysqli->query($barangaysQuery);
$barangays = [];
while ($row = $barangaysResult->fetch_assoc()) {
    $barangays[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Heatmaps | Crime Dep.</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/admin-header.css">
    <link rel="stylesheet" href="../css/hero.css">
    <link rel="stylesheet" href="../css/sidebar-footer.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="../css/crime-mapping.css">
    <link rel="icon" type="image/x-icon" href="../image/favicon.ico">
    <style>
        #crime-map {
            height: calc(100vh - 350px);
            min-height: 500px;
            max-height: 800px;
            width: 100%;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            margin: 20px 0;
        }
        .heatmap-controls-panel {
            background: var(--primary-bg-1);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        .heatmap-slider-group {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        .slider-item {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .slider-item label {
            font-size: 0.875rem;
            color: var(--text-secondary-1);
        }
        .slider-item input[type="range"] {
            width: 100%;
        }
        .slider-value {
            font-weight: 600;
            color: var(--text-primary-1);
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
                            <a href="/analytics" class="breadcrumb-link"><span>Analytics</span></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <span>Heatmaps</span>
                        </li>
                    </ol>
                </nav>
                <h1>Crime Heatmaps - Quezon City</h1>
                <p>Visualize crime density and hotspots across Quezon City using interactive heatmaps. Adjust parameters to analyze crime concentration patterns.</p>
            </div>

            <div class="sub-container">
                <div class="page-content">
                    <!-- Statistics Cards -->
                    <div class="dashboard-grid">
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-card-icon primary">
                                    <i class="fas fa-fire"></i>
                                </div>
                                <div class="stat-card-info">
                                    <div class="stat-card-label">Hotspot Areas</div>
                                    <div class="stat-card-value" id="hotspot-count">0</div>
                                </div>
                            </div>
                            <div class="stat-card-footer">
                                <span style="color: var(--text-secondary-1);">High density zones</span>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-card-icon danger">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="stat-card-info">
                                    <div class="stat-card-label">Total Incidents</div>
                                    <div class="stat-card-value" id="total-incidents">0</div>
                                </div>
                            </div>
                            <div class="stat-card-footer">
                                <span style="color: var(--text-secondary-1);">In selected period</span>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-card-icon warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <div class="stat-card-info">
                                    <div class="stat-card-label">Peak District</div>
                                    <div class="stat-card-value" id="peak-district" style="font-size: 1.25rem;">-</div>
                                </div>
                            </div>
                            <div class="stat-card-footer">
                                <span style="color: var(--text-secondary-1);">Highest concentration</span>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-card-icon success">
                                    <i class="fas fa-chart-area"></i>
                                </div>
                                <div class="stat-card-info">
                                    <div class="stat-card-label">Coverage</div>
                                    <div class="stat-card-value" id="coverage-barangays">0</div>
                                </div>
                            </div>
                            <div class="stat-card-footer">
                                <span style="color: var(--text-secondary-1);">Barangays affected</span>
                            </div>
                        </div>
                    </div>

                    <!-- Map Filters -->
                    <div class="map-controls">
                        <div class="map-controls-header">
                            <h3 class="map-controls-title">
                                <i class="fas fa-filter"></i>
                                Filter Heatmap Data
                            </h3>
                        </div>
                        <div class="filter-group">
                            <div class="filter-item">
                                <label for="period-filter"><i class="fas fa-clock"></i> Time Period</label>
                                <select id="period-filter">
                                    <option value="week">Last 7 Days</option>
                                    <option value="month" selected>Last 30 Days</option>
                                    <option value="year">Last Year</option>
                                    <option value="all">All Time</option>
                                </select>
                            </div>
                            <div class="filter-item">
                                <label for="crime-type-filter"><i class="fas fa-tag"></i> Crime Type</label>
                                <select id="crime-type-filter">
                                    <option value="all">All Types</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="filter-item">
                                <label for="barangay-filter"><i class="fas fa-map-marker-alt"></i> Barangay</label>
                                <select id="barangay-filter">
                                    <option value="all">All Barangays</option>
                                    <?php foreach ($barangays as $brgy): ?>
                                        <option value="<?php echo $brgy['id']; ?>"><?php echo htmlspecialchars($brgy['barangay_name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Heatmap Controls -->
                    <div class="heatmap-controls-panel">
                        <h4 style="margin-bottom: 1rem; color: var(--text-primary-1);"><i class="fas fa-sliders-h"></i> Heatmap Settings</h4>
                        <div class="heatmap-slider-group">
                            <div class="slider-item">
                                <label>Radius: <span class="slider-value" id="radius-value">25</span>px</label>
                                <input type="range" id="heat-radius" min="10" max="50" value="25">
                            </div>
                            <div class="slider-item">
                                <label>Blur: <span class="slider-value" id="blur-value">15</span>px</label>
                                <input type="range" id="heat-blur" min="5" max="30" value="15">
                            </div>
                            <div class="slider-item">
                                <label>Intensity: <span class="slider-value" id="intensity-value">1.0</span></label>
                                <input type="range" id="heat-intensity" min="0.1" max="2" step="0.1" value="1">
                            </div>
                        </div>
                    </div>

                    <!-- Loading Indicator -->
                    <div id="map-loading" class="map-loading" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i> Loading heatmap data...
                    </div>

                    <!-- Interactive Map -->
                    <div id="crime-map"></div>

                    <!-- Legend -->
                    <div class="legend">
                        <div class="legend-header">
                            <h3><i class="fas fa-fire"></i> Crime Density Legend</h3>
                        </div>
                        <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem;">
                            <span style="color: var(--text-secondary-1);">Low</span>
                            <div style="flex: 1; height: 20px; background: linear-gradient(to right, #22c55e, #84cc16, #eab308, #f97316, #dc2626); border-radius: 4px;"></div>
                            <span style="color: var(--text-secondary-1);">High</span>
                        </div>
                    </div>

                    <!-- Top Hotspots Table -->
                    <div class="chart-card" style="margin-top: 1.5rem;">
                        <div class="chart-header">
                            <h3 class="chart-title">Top Crime Hotspots</h3>
                            <div class="chart-icon"><i class="fas fa-list-ol"></i></div>
                        </div>
                        <div id="hotspots-table" class="barangay-list">
                            <div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i> Loading hotspots...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include('../includes/admin-footer.php') ?>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>
    <script>
        // Quezon City boundary
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

        // Initialize map
        const map = L.map('crime-map', {
            center: [14.6760, 121.0437],
            zoom: 12,
            minZoom: 11,
            maxZoom: 18,
            maxBounds: strictBounds,
            maxBoundsViscosity: 1.0
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 18
        }).addTo(map);

        // Mask outside QC
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
            weight: 4,
            opacity: 1,
            fillColor: 'transparent',
            fillOpacity: 0
        }).addTo(map);

        let heatLayer = null;
        let crimeData = [];

        // Load heatmap data
        async function loadHeatmapData() {
            const period = document.getElementById('period-filter').value;
            const category = document.getElementById('crime-type-filter').value;
            const barangay = document.getElementById('barangay-filter').value;

            document.getElementById('map-loading').style.display = 'flex';

            try {
                let url = `../../api/retrieve/crime-mapping.php?period=${period}&limit=500`;
                if (category !== 'all') url += `&category_id=${category}`;
                if (barangay !== 'all') url += `&barangay_id=${barangay}`;

                const response = await fetch(url);
                const result = await response.json();

                if (result.success) {
                    crimeData = result.data.incidents || [];
                    updateHeatmap();
                    updateStats(result.data);
                    updateHotspotsTable(result.data.clusters || []);
                }
            } catch (error) {
                console.error('Error loading heatmap:', error);
            } finally {
                document.getElementById('map-loading').style.display = 'none';
            }
        }

        // Update heatmap layer
        function updateHeatmap() {
            if (heatLayer) {
                map.removeLayer(heatLayer);
            }

            if (crimeData.length === 0) return;

            const radius = parseInt(document.getElementById('heat-radius').value);
            const blur = parseInt(document.getElementById('heat-blur').value);
            const intensity = parseFloat(document.getElementById('heat-intensity').value);

            const heatPoints = crimeData.map(crime => {
                let weight = 0.5;
                if (crime.severity === 'critical') weight = 1.0;
                else if (crime.severity === 'high') weight = 0.8;
                else if (crime.severity === 'medium') weight = 0.6;
                return [crime.lat, crime.lng, weight * intensity];
            });

            heatLayer = L.heatLayer(heatPoints, {
                radius: radius,
                blur: blur,
                maxZoom: 15,
                gradient: {
                    0.0: '#22c55e',
                    0.3: '#84cc16',
                    0.5: '#eab308',
                    0.7: '#f97316',
                    0.85: '#ef4444',
                    1.0: '#dc2626'
                }
            }).addTo(map);
        }

        // Update statistics
        function updateStats(data) {
            const incidents = data.incidents || [];
            const clusters = data.clusters || [];

            document.getElementById('total-incidents').textContent = incidents.length.toLocaleString();

            // Count hotspots (barangays with > 3 incidents)
            const hotspots = clusters.filter(c => c.count > 3).length;
            document.getElementById('hotspot-count').textContent = hotspots;

            // Find peak district
            const districtCounts = {};
            incidents.forEach(inc => {
                if (inc.district) {
                    districtCounts[inc.district] = (districtCounts[inc.district] || 0) + 1;
                }
            });
            const peakDistrict = Object.entries(districtCounts).sort((a, b) => b[1] - a[1])[0];
            document.getElementById('peak-district').textContent = peakDistrict ? peakDistrict[0] : '-';

            // Count affected barangays
            const affectedBarangays = new Set(incidents.map(i => i.barangay)).size;
            document.getElementById('coverage-barangays').textContent = affectedBarangays;
        }

        // Update hotspots table
        function updateHotspotsTable(clusters) {
            const container = document.getElementById('hotspots-table');
            const sortedClusters = clusters.sort((a, b) => b.count - a.count).slice(0, 10);

            if (sortedClusters.length === 0) {
                container.innerHTML = '<div class="no-data">No hotspot data available</div>';
                return;
            }

            container.innerHTML = sortedClusters.map((cluster, index) => `
                <div class="barangay-item" onclick="map.setView([${cluster.lat}, ${cluster.lng}], 15)" style="cursor: pointer;">
                    <div class="barangay-position">${index + 1}</div>
                    <div class="barangay-details">
                        <div class="barangay-name">${cluster.barangay_name}</div>
                        <div class="barangay-location">
                            <i class="fas fa-map-marker-alt"></i>
                            Click to zoom
                        </div>
                    </div>
                    <div class="barangay-count">${cluster.count}</div>
                </div>
            `).join('');
        }

        // Slider event listeners
        document.getElementById('heat-radius').addEventListener('input', function(e) {
            document.getElementById('radius-value').textContent = e.target.value;
            updateHeatmap();
        });

        document.getElementById('heat-blur').addEventListener('input', function(e) {
            document.getElementById('blur-value').textContent = e.target.value;
            updateHeatmap();
        });

        document.getElementById('heat-intensity').addEventListener('input', function(e) {
            document.getElementById('intensity-value').textContent = e.target.value;
            updateHeatmap();
        });

        // Filter event listeners
        document.getElementById('period-filter').addEventListener('change', loadHeatmapData);
        document.getElementById('crime-type-filter').addEventListener('change', loadHeatmapData);
        document.getElementById('barangay-filter').addEventListener('change', loadHeatmapData);

        // Initial load
        document.addEventListener('DOMContentLoaded', loadHeatmapData);
    </script>

    <style>
        .map-loading {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            background: var(--primary-bg-1);
            border-radius: 8px;
            margin-bottom: 1rem;
            color: var(--text-secondary-1);
        }
        .map-loading i { margin-right: 0.5rem; }
        .no-data {
            text-align: center;
            padding: 2rem;
            color: var(--text-secondary-1);
        }
        .loading-spinner {
            text-align: center;
            padding: 2rem;
            color: var(--text-secondary-1);
        }
    </style>
</body>
</html>
