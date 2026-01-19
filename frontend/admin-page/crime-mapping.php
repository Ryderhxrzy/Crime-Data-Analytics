<?php
// Authentication check - must be at the top of every admin page
require_once '../../api/middleware/auth.php';
require_once '../../api/config.php';

// Get crime categories for filter dropdown
$categoriesQuery = "SELECT id, category_code, category_name, color_code as color, icon FROM crime_department_crime_categories WHERE is_active = 1 ORDER BY category_name";
$categoriesResult = $mysqli->query($categoriesQuery);
$categories = [];
while ($row = $categoriesResult->fetch_assoc()) {
    $categories[] = $row;
}

// Get barangays for filter
$barangaysQuery = "SELECT id, barangay_name, city_municipality as district FROM crime_department_barangays WHERE is_active = 1 ORDER BY barangay_name";
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
    <title>Crime Mapping | Crime Dep.</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/admin-header.css">
    <link rel="stylesheet" href="../css/hero.css">
    <link rel="stylesheet" href="../css/sidebar-footer.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="../css/crime-mapping.css">
    <link rel="icon" type="image/x-icon" href="../image/favicon.ico">
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
                            <span>Crime Mapping</span>
                        </li>
                    </ol>
                </nav>
                <h1>Crime Mapping - Quezon City</h1>
                <p>Interactive crime map showing reported incidents across Quezon City. Click on markers to view detailed information about each crime incident including type, date, time, barangay, and case status.</p>
            </div>

            <div class="sub-container" style="padding-bottom: 1.5rem">
                <div class="page-content">
                    <div class="dashboard-grid">
                        <!-- Total Crimes Card -->
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-card-icon primary">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <div class="stat-card-info">
                                    <div class="stat-card-label">Total Incidents</div>
                                    <div class="stat-card-value" id="total-crimes">0</div>
                                </div>
                            </div>
                            <div class="stat-card-footer">
                                <span id="total-trend" class="stat-trend">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </span>
                                <span style="margin-left: 0.5rem;">in selected period</span>
                            </div>
                        </div>

                        <!-- Open Cases Card -->
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-card-icon danger">
                                    <i class="fas fa-folder-open"></i>
                                </div>
                                <div class="stat-card-info">
                                    <div class="stat-card-label">Under Investigation</div>
                                    <div class="stat-card-value" id="open-cases">0</div>
                                </div>
                            </div>
                            <div class="stat-card-footer">
                                <span style="color: var(--text-secondary-1);">
                                    Active investigations
                                </span>
                            </div>
                        </div>

                        <!-- Closed Cases Card -->
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-card-icon success">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="stat-card-info">
                                    <div class="stat-card-label">Cleared Cases</div>
                                    <div class="stat-card-value" id="closed-cases">0</div>
                                </div>
                            </div>
                            <div class="stat-card-footer">
                                <span id="resolution-rate" style="color: var(--success-color); font-weight:600;">0%</span>
                                <span style="margin-left: 0.5rem;">resolution rate</span>
                            </div>
                        </div>

                        <!-- Reported Cases Card -->
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-card-icon warning">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <div class="stat-card-info">
                                    <div class="stat-card-label">Reported Cases</div>
                                    <div class="stat-card-value" id="reported-cases">0</div>
                                </div>
                            </div>
                            <div class="stat-card-footer">
                                <span style="color: var(--text-secondary-1);">
                                    Pending investigation
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Map Controls -->
                    <div class="map-controls">
                        <div class="map-controls-header">
                            <h3 class="map-controls-title">
                                <i class="fas fa-filter"></i>
                                Filter Crime Data
                            </h3>
                        </div>
                        <div class="filter-group">
                            <div class="filter-item">
                                <label for="visualization-mode">
                                    <i class="fas fa-map"></i>
                                    Visualization Mode
                                </label>
                                <select id="visualization-mode">
                                    <option value="markers">Individual Markers</option>
                                    <option value="heatmap">Heat Map</option>
                                    <option value="clusters">Cluster View</option>
                                </select>
                            </div>
                            <div class="filter-item">
                                <label for="period-filter">
                                    <i class="fas fa-clock"></i>
                                    Time Period
                                </label>
                                <select id="period-filter">
                                    <option value="week">Last 7 Days</option>
                                    <option value="month" selected>Last 30 Days</option>
                                    <option value="year">Last Year</option>
                                    <option value="all">All Time</option>
                                </select>
                            </div>
                            <div class="filter-item">
                                <label for="crime-type-filter">
                                    <i class="fas fa-tag"></i>
                                    Crime Type
                                </label>
                                <select id="crime-type-filter">
                                    <option value="all">All Types</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="filter-item">
                                <label for="status-filter">
                                    <i class="fas fa-info-circle"></i>
                                    Case Status
                                </label>
                                <select id="status-filter">
                                    <option value="all">All Status</option>
                                    <option value="reported">Reported</option>
                                    <option value="under_investigation">Under Investigation</option>
                                    <option value="resolved">Resolved</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </div>
                            <div class="filter-item">
                                <label for="barangay-filter">
                                    <i class="fas fa-map-marker-alt"></i>
                                    Barangay
                                </label>
                                <select id="barangay-filter">
                                    <option value="all">All Barangays</option>
                                    <?php foreach ($barangays as $brgy): ?>
                                        <option value="<?php echo $brgy['id']; ?>"><?php echo htmlspecialchars($brgy['barangay_name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Heatmap Density Controls (shown only when heatmap mode is selected) -->
                    <div id="heatmap-controls" class="heatmap-controls-panel" style="display: none;">
                        <h4 class="heatmap-controls-title"><i class="fas fa-sliders-h"></i> Heatmap Density Settings</h4>
                        <div class="heatmap-slider-group">
                            <div class="slider-item">
                                <label>Radius: <span class="slider-value" id="radius-value">25</span>px</label>
                                <input type="range" id="heat-radius" min="10" max="60" value="25">
                            </div>
                            <div class="slider-item">
                                <label>Blur: <span class="slider-value" id="blur-value">15</span>px</label>
                                <input type="range" id="heat-blur" min="5" max="40" value="15">
                            </div>
                            <div class="slider-item">
                                <label>Intensity: <span class="slider-value" id="intensity-value">1.0</span></label>
                                <input type="range" id="heat-intensity" min="0.1" max="3" step="0.1" value="1">
                            </div>
                            <div class="slider-item">
                                <label>Max Zoom: <span class="slider-value" id="maxzoom-value">18</span></label>
                                <input type="range" id="heat-maxzoom" min="10" max="18" value="18">
                            </div>
                        </div>
                    </div>

                    <!-- Loading indicator -->
                    <div id="map-loading" class="map-loading" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i> Loading crime data...
                    </div>

                    <!-- Interactive Map -->
                    <div id="crime-map"></div>

                    <!-- Legend for Markers/Clusters -->
                    <div class="legend" id="categories-legend">
                        <div class="legend-header">
                            <h3>
                                <i class="fas fa-map-marked-alt"></i>
                                Crime Categories
                            </h3>
                        </div>
                        <div class="legend-grid" id="legend-grid">
                            <?php foreach (array_slice($categories, 0, 8) as $cat): ?>
                                <div class="legend-item">
                                    <div class="legend-color" style="background-color: <?php echo $cat['color'] ?? '#6b7280'; ?>;"></div>
                                    <span class="legend-label"><?php echo htmlspecialchars($cat['category_name']); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Legend for Heatmap (hidden by default) -->
                    <div class="legend heatmap-legend" id="heatmap-legend" style="display: none;">
                        <div class="legend-header">
                            <h3>
                                <i class="fas fa-fire"></i>
                                Crime Density
                            </h3>
                        </div>
                        <div class="density-legend-content">
                            <div class="density-labels">
                                <span class="density-label low">Low</span>
                                <span class="density-label high">High</span>
                            </div>
                            <div class="density-gradient"></div>
                            <div class="density-description">
                                <div class="density-item">
                                    <span class="density-dot" style="background: #22c55e;"></span>
                                    <span>Low incident density</span>
                                </div>
                                <div class="density-item">
                                    <span class="density-dot" style="background: #eab308;"></span>
                                    <span>Medium incident density</span>
                                </div>
                                <div class="density-item">
                                    <span class="density-dot" style="background: #dc2626;"></span>
                                    <span>High incident density (Hotspot)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Incidents List -->
                    <div class="incidents-section">
                        <div class="section-header">
                            <h2 class="section-title">Recent Incidents on Map</h2>
                            <span id="incidents-count" class="incidents-count">0 incidents</span>
                        </div>
                        <div id="incidents-list" class="incidents-list">
                            <div class="loading-spinner">
                                <i class="fas fa-spinner fa-spin"></i> Loading incidents...
                            </div>
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
        // Category colors from database
        const categoryColors = <?php echo json_encode(array_column(array_map(function($cat) {
            return ['id' => $cat['id'], 'color' => $cat['color'] ?? '#6b7280'];
        }, $categories), 'color', 'id')); ?>;

        // Initialize map
        const map = L.map('crime-map', {
            center: [14.6760, 121.0437],
            zoom: 12,
            minZoom: 11,
            maxZoom: 18
        });

        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 18
        }).addTo(map);

        // Load QC boundary from GeoJSON file
        let qcBoundaryLayer = null;
        let qcMaskLayer = null;

        async function loadQCBoundary() {
            try {
                const response = await fetch('../../qc_map.geojson');
                const geojsonData = await response.json();

                if (geojsonData.features && geojsonData.features.length > 0) {
                    const qcFeature = geojsonData.features[0];

                    // Create mask layer to dim areas outside QC
                    const outerBounds = [[-90, -180], [-90, 180], [90, 180], [90, -180], [-90, -180]];
                    const qcCoords = qcFeature.geometry.coordinates[0].map(coord => [coord[1], coord[0]]);

                    qcMaskLayer = L.polygon([outerBounds, qcCoords], {
                        color: 'transparent',
                        fillColor: '#111827',
                        fillOpacity: 0.7,
                        interactive: false
                    }).addTo(map);

                    // Add the QC boundary layer with styled border
                    qcBoundaryLayer = L.geoJSON(geojsonData, {
                        style: {
                            color: '#4c8a89',
                            weight: 3,
                            opacity: 1,
                            fillColor: 'transparent',
                            fillOpacity: 0
                        },
                        onEachFeature: function(feature, layer) {
                            layer.bindPopup('<strong>Quezon City</strong><br>Crime Data Analytics Coverage Area');
                        }
                    }).addTo(map);

                    // Get bounds from the GeoJSON and set map constraints
                    const bounds = qcBoundaryLayer.getBounds();
                    map.setMaxBounds(bounds.pad(0.1));
                    map.fitBounds(bounds);
                } else {
                    console.warn('QC boundary GeoJSON is empty, using default view');
                }
            } catch (error) {
                console.error('Failed to load QC boundary:', error);
            }
        }

        // Load boundary on page load
        loadQCBoundary();

        // Global variables
        let crimeData = [];
        let markers = [];
        let heatLayer = null;
        let markerClusterGroup = null;

        // Create custom marker
        function createMarker(crime) {
            const color = crime.color || categoryColors[crime.category_id] || '#e74c3c';

            const icon = L.divIcon({
                className: 'custom-marker',
                html: `<div style="background-color: ${color}; width: 20px; height: 20px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"></div>`,
                iconSize: [20, 20],
                iconAnchor: [10, 10]
            });

            const marker = L.marker([crime.lat, crime.lng], { icon: icon })
                .bindPopup(`
                    <div class="crime-marker-popup">
                        <h4><i class="fas ${crime.icon || 'fa-exclamation-circle'}"></i> ${crime.category || 'Unknown'}</h4>
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-heading"></i> Title:</span>
                            <span class="info-value">${crime.title || 'N/A'}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-calendar"></i> Date:</span>
                            <span class="info-value">${crime.date}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-clock"></i> Time:</span>
                            <span class="info-value">${crime.time || 'N/A'}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-map-marker-alt"></i> Location:</span>
                            <span class="info-value">${crime.barangay || 'Unknown'}, ${crime.district || ''}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-map-pin"></i> Address:</span>
                            <span class="info-value">${crime.address || 'N/A'}</span>
                        </div>
                        <div class="info-divider"></div>
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-info-circle"></i> Status:</span>
                            <span class="status-badge status-${crime.status}">
                                ${(crime.status || 'unknown').replace('_', ' ').toUpperCase()}
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-fingerprint"></i> Case:</span>
                            <span class="info-value">${crime.code || 'N/A'}</span>
                        </div>
                        ${crime.description ? `
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-file-alt"></i> Details:</span>
                            <span class="info-value">${crime.description.substring(0, 150)}${crime.description.length > 150 ? '...' : ''}</span>
                        </div>
                        ` : ''}
                    </div>
                `);

            return marker;
        }

        // Clear all markers
        function clearMarkers() {
            markers.forEach(m => map.removeLayer(m));
            markers = [];
            if (heatLayer) {
                map.removeLayer(heatLayer);
                heatLayer = null;
            }
        }

        // Render markers
        function renderMarkers(data) {
            clearMarkers();

            data.forEach(crime => {
                const marker = createMarker(crime);
                marker.addTo(map);
                markers.push(marker);
            });

            if (data.length > 0) {
                const bounds = L.latLngBounds(data.map(c => [c.lat, c.lng]));
                map.fitBounds(bounds, { padding: [50, 50], maxZoom: 14 });
            }
        }

        // Render heatmap
        function renderHeatmap(data) {
            clearMarkers();

            if (data.length === 0) return;

            // Get settings from sliders
            const radius = parseInt(document.getElementById('heat-radius').value);
            const blur = parseInt(document.getElementById('heat-blur').value);
            const intensity = parseFloat(document.getElementById('heat-intensity').value);
            const maxZoom = parseInt(document.getElementById('heat-maxzoom').value);

            const heatPoints = data.map(crime => {
                let weight = 0.5;
                if (crime.severity === 'critical') weight = 1.0;
                else if (crime.severity === 'high') weight = 0.8;
                else if (crime.severity === 'medium') weight = 0.6;
                return [crime.lat, crime.lng, weight * intensity];
            });

            heatLayer = L.heatLayer(heatPoints, {
                radius: radius,
                blur: blur,
                maxZoom: maxZoom,
                minOpacity: 0.4,
                gradient: {
                    0.0: '#22c55e',
                    0.25: '#84cc16',
                    0.4: '#eab308',
                    0.6: '#f97316',
                    0.8: '#ef4444',
                    1.0: '#dc2626'
                }
            }).addTo(map);
        }

        // Update heatmap when settings change
        function updateHeatmapSettings() {
            if (document.getElementById('visualization-mode').value === 'heatmap' && crimeData.length > 0) {
                renderHeatmap(crimeData);
            }
        }

        // Render clusters
        function renderClusters(clusters) {
            clearMarkers();

            clusters.forEach(cluster => {
                if (cluster.count === 0) return;

                const size = Math.min(50, 20 + cluster.count * 2);
                const color = cluster.count > 10 ? '#dc2626' : cluster.count > 5 ? '#f97316' : '#22c55e';

                const icon = L.divIcon({
                    className: 'cluster-marker',
                    html: `<div style="background-color: ${color}; width: ${size}px; height: ${size}px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: ${size > 30 ? '14px' : '12px'};">${cluster.count}</div>`,
                    iconSize: [size, size],
                    iconAnchor: [size/2, size/2]
                });

                const marker = L.marker([cluster.lat, cluster.lng], { icon: icon })
                    .bindPopup(`<strong>${cluster.barangay_name}</strong><br>${cluster.count} incidents`)
                    .addTo(map);
                markers.push(marker);
            });
        }

        // Update statistics display
        function updateStats(data) {
            const total = data.incidents ? data.incidents.length : 0;
            const underInvestigation = data.incidents ? data.incidents.filter(c => c.status === 'under_investigation').length : 0;
            const cleared = data.incidents ? data.incidents.filter(c => c.clearance === 'cleared').length : 0;
            const reported = data.incidents ? data.incidents.filter(c => c.status === 'reported').length : 0;
            const resolutionRate = total > 0 ? Math.round((cleared / total) * 100) : 0;

            document.getElementById('total-crimes').textContent = total.toLocaleString();
            document.getElementById('open-cases').textContent = underInvestigation.toLocaleString();
            document.getElementById('closed-cases').textContent = cleared.toLocaleString();
            document.getElementById('reported-cases').textContent = reported.toLocaleString();
            document.getElementById('resolution-rate').textContent = resolutionRate + '%';
            document.getElementById('total-trend').innerHTML = `<i class="fas fa-map-marker-alt"></i> ${total}`;
            document.getElementById('incidents-count').textContent = total + ' incidents';
        }

        // Render incidents list
        function renderIncidentsList(incidents) {
            const container = document.getElementById('incidents-list');

            if (!incidents || incidents.length === 0) {
                container.innerHTML = '<div class="no-data">No incidents found for the selected filters.</div>';
                return;
            }

            const html = incidents.slice(0, 10).map(inc => `
                <div class="incident-item" onclick="map.setView([${inc.lat}, ${inc.lng}], 16)">
                    <div class="incident-icon" style="background-color: ${inc.color || '#e74c3c'}">
                        <i class="fas ${inc.icon || 'fa-exclamation-circle'}"></i>
                    </div>
                    <div class="incident-details">
                        <div class="incident-title">${inc.title || inc.category}</div>
                        <div class="incident-meta">
                            <span><i class="fas fa-map-marker-alt"></i> ${inc.barangay || 'Unknown'}</span>
                            <span><i class="fas fa-calendar"></i> ${inc.date}</span>
                        </div>
                    </div>
                    <div class="incident-status status-${inc.status}">
                        ${(inc.status || 'unknown').replace('_', ' ')}
                    </div>
                </div>
            `).join('');

            container.innerHTML = html;
        }

        // Load crime data from API
        async function loadCrimeData() {
            const period = document.getElementById('period-filter').value;
            const category = document.getElementById('crime-type-filter').value;
            const status = document.getElementById('status-filter').value;
            const barangay = document.getElementById('barangay-filter').value;
            const mode = document.getElementById('visualization-mode').value;

            // Show loading
            document.getElementById('map-loading').style.display = 'flex';

            try {
                let url = `../../api/retrieve/crime-mapping.php?period=${period}&limit=200`;
                if (category !== 'all') url += `&category_id=${category}`;
                if (status !== 'all') url += `&status=${status}`;
                if (barangay !== 'all') url += `&barangay_id=${barangay}`;

                const response = await fetch(url);
                const result = await response.json();

                if (result.success) {
                    crimeData = result.data.incidents || [];

                    updateStats(result.data);

                    if (mode === 'markers') {
                        renderMarkers(crimeData);
                    } else if (mode === 'heatmap') {
                        renderHeatmap(crimeData);
                    } else if (mode === 'clusters') {
                        renderClusters(result.data.clusters || []);
                    }

                    renderIncidentsList(crimeData);
                } else {
                    console.error('API Error:', result.error);
                }
            } catch (error) {
                console.error('Fetch Error:', error);
            } finally {
                document.getElementById('map-loading').style.display = 'none';
            }
        }

        // Event listeners for filters
        document.getElementById('period-filter').addEventListener('change', loadCrimeData);
        document.getElementById('crime-type-filter').addEventListener('change', loadCrimeData);
        document.getElementById('status-filter').addEventListener('change', loadCrimeData);
        document.getElementById('barangay-filter').addEventListener('change', loadCrimeData);

        // Visualization mode change - show/hide heatmap controls and legends
        document.getElementById('visualization-mode').addEventListener('change', function() {
            const heatmapControls = document.getElementById('heatmap-controls');
            const categoriesLegend = document.getElementById('categories-legend');
            const heatmapLegend = document.getElementById('heatmap-legend');

            if (this.value === 'heatmap') {
                heatmapControls.style.display = 'block';
                categoriesLegend.style.display = 'none';
                heatmapLegend.style.display = 'block';
            } else {
                heatmapControls.style.display = 'none';
                categoriesLegend.style.display = 'block';
                heatmapLegend.style.display = 'none';
            }
            loadCrimeData();
        });

        // Heatmap slider event listeners
        document.getElementById('heat-radius').addEventListener('input', function(e) {
            document.getElementById('radius-value').textContent = e.target.value;
            updateHeatmapSettings();
        });

        document.getElementById('heat-blur').addEventListener('input', function(e) {
            document.getElementById('blur-value').textContent = e.target.value;
            updateHeatmapSettings();
        });

        document.getElementById('heat-intensity').addEventListener('input', function(e) {
            document.getElementById('intensity-value').textContent = e.target.value;
            updateHeatmapSettings();
        });

        document.getElementById('heat-maxzoom').addEventListener('input', function(e) {
            document.getElementById('maxzoom-value').textContent = e.target.value;
            updateHeatmapSettings();
        });

        // Initial load
        document.addEventListener('DOMContentLoaded', loadCrimeData);
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

        .map-loading i {
            margin-right: 0.5rem;
        }

        /* Heatmap Controls Panel */
        .heatmap-controls-panel {
            background: var(--primary-bg-1);
            padding: 1.25rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            border: 1px solid var(--border-color-1);
        }

        .heatmap-controls-title {
            margin: 0 0 1rem 0;
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-primary-1);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .heatmap-controls-title i {
            color: #4c8a89;
        }

        .heatmap-slider-group {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1.25rem;
        }

        .slider-item {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .slider-item label {
            font-size: 0.875rem;
            color: var(--text-secondary-1);
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .slider-value {
            font-weight: 600;
            color: #4c8a89;
        }

        .slider-item input[type="range"] {
            width: 100%;
            height: 6px;
            border-radius: 3px;
            background: var(--border-color-1);
            outline: none;
            -webkit-appearance: none;
            appearance: none;
        }

        .slider-item input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #4c8a89;
            cursor: pointer;
            border: 2px solid white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .slider-item input[type="range"]::-moz-range-thumb {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #4c8a89;
            cursor: pointer;
            border: 2px solid white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        /* Heatmap Density Legend */
        .heatmap-legend .density-legend-content {
            padding: 0.5rem 0;
        }

        .density-labels {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .density-label {
            font-size: 0.8125rem;
            font-weight: 600;
        }

        .density-label.low {
            color: #22c55e;
        }

        .density-label.high {
            color: #dc2626;
        }

        .density-gradient {
            height: 16px;
            border-radius: 8px;
            background: linear-gradient(to right, #22c55e, #84cc16, #eab308, #f97316, #ef4444, #dc2626);
            margin-bottom: 1rem;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
        }

        .density-description {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .density-item {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            font-size: 0.8125rem;
            color: var(--text-secondary-1);
        }

        .density-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .incidents-section {
            background: var(--primary-bg-1);
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: 1.5rem;
            border: 1px solid var(--border-color-1);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--border-color-1);
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-primary-1);
        }

        .incidents-count {
            font-size: 0.875rem;
            color: var(--text-secondary-1);
            background: var(--secondary-bg-1);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
        }

        .incidents-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            max-height: 400px;
            overflow-y: auto;
        }

        .incident-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.875rem;
            background: var(--secondary-bg-1);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            border: 1px solid var(--border-color-1);
        }

        .incident-item:hover {
            background: var(--tertiary-bg-1);
            border-color: var(--primary-color-1);
        }

        .incident-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            flex-shrink: 0;
        }

        .incident-details {
            flex: 1;
            min-width: 0;
        }

        .incident-title {
            font-weight: 500;
            color: var(--text-primary-1);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .incident-meta {
            display: flex;
            gap: 1rem;
            font-size: 0.75rem;
            color: var(--text-secondary-1);
            margin-top: 0.25rem;
            flex-wrap: wrap;
        }

        .incident-meta span {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .incident-status {
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            text-transform: uppercase;
            font-weight: 600;
            white-space: nowrap;
        }

        .incident-status.status-reported {
            background: #fef3c7;
            color: #92400e;
        }

        .incident-status.status-under_investigation {
            background: #dbeafe;
            color: #1e40af;
        }

        .incident-status.status-resolved,
        .incident-status.status-closed {
            background: #d1fae5;
            color: #065f46;
        }

        /* Mobile Responsive Styles */
        @media (max-width: 768px) {
            .incidents-section {
                padding: 1rem;
                margin-top: 1rem;
            }

            .section-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .section-title {
                font-size: 1rem;
            }

            .incidents-list {
                max-height: 350px;
                gap: 0.5rem;
            }

            .incident-item {
                flex-wrap: wrap;
                padding: 0.75rem;
                gap: 0.75rem;
            }

            .incident-icon {
                width: 36px;
                height: 36px;
            }

            .incident-details {
                flex: 1 1 calc(100% - 100px);
            }

            .incident-title {
                font-size: 0.875rem;
                white-space: normal;
                line-height: 1.3;
            }

            .incident-meta {
                gap: 0.5rem;
            }

            .incident-status {
                margin-top: 0.5rem;
                width: 100%;
                text-align: center;
            }

            /* Heatmap controls mobile */
            .heatmap-controls-panel {
                padding: 1rem;
            }

            .heatmap-slider-group {
                grid-template-columns: 1fr 1fr;
                gap: 1rem;
            }

            /* Legend mobile */
            .density-gradient {
                height: 12px;
            }

            .density-description {
                gap: 0.375rem;
            }

            .density-item {
                font-size: 0.75rem;
            }

            .density-dot {
                width: 10px;
                height: 10px;
            }
        }

        @media (max-width: 480px) {
            .incident-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .incident-icon {
                align-self: flex-start;
            }

            .incident-details {
                width: 100%;
            }

            .incident-meta {
                flex-direction: column;
                gap: 0.25rem;
            }

            .incident-status {
                align-self: flex-start;
                width: auto;
            }

            .heatmap-slider-group {
                grid-template-columns: 1fr;
            }
        }

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

        /* Popup styles */
        .crime-marker-popup {
            min-width: 250px;
        }

        .crime-marker-popup h4 {
            margin: 0 0 0.75rem 0;
            color: #1f2937;
            font-size: 1rem;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 0.5rem;
        }

        .crime-marker-popup h4 i {
            margin-right: 0.5rem;
        }

        .crime-marker-popup .info-row {
            display: flex;
            margin-bottom: 0.5rem;
            font-size: 0.85rem;
        }

        .crime-marker-popup .info-label {
            font-weight: 500;
            color: #6b7280;
            min-width: 80px;
        }

        .crime-marker-popup .info-label i {
            margin-right: 0.25rem;
            width: 14px;
        }

        .crime-marker-popup .info-value {
            color: #1f2937;
        }

        .crime-marker-popup .info-divider {
            border-top: 1px solid #e5e7eb;
            margin: 0.75rem 0;
        }

        .crime-marker-popup .status-badge {
            display: inline-block;
            padding: 0.15rem 0.5rem;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .crime-marker-popup .status-badge.status-reported {
            background: #fef3c7;
            color: #92400e;
        }

        .crime-marker-popup .status-badge.status-under_investigation {
            background: #dbeafe;
            color: #1e40af;
        }

        .crime-marker-popup .status-badge.status-resolved,
        .crime-marker-popup .status-badge.status-closed {
            background: #d1fae5;
            color: #065f46;
        }
    </style>
</body>
</html>
