<?php
// Authentication check - must be at the top of every admin page
require_once '../../api/middleware/auth.php';
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
    <link rel="stylesheet" href="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js" />
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
            
            <div class="sub-container">
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
                                <span class="stat-trend up">
                                    <i class="fas fa-arrow-up"></i> 5.2%
                                </span>
                                <span style="margin-left: 0.5rem;">vs last month</span>
                            </div>
                        </div>

                        <!-- Open Cases Card -->
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-card-icon danger">
                                    <i class="fas fa-folder-open"></i>
                                </div>
                                <div class="stat-card-info">
                                    <div class="stat-card-label">Open Cases</div>
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
                                    <div class="stat-card-label">Closed Cases</div>
                                    <div class="stat-card-value" id="closed-cases">0</div>
                                </div>
                            </div>
                            <div class="stat-card-footer">
                                <span style="color: var(--success-color); font-weight:600;">65.3%</span>
                                <span style="margin-left: 0.5rem;">resolution rate</span>
                            </div>
                        </div>

                        <!-- Today's Crimes Card -->
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-card-icon warning">
                                    <i class="fas fa-calendar-day"></i>
                                </div>
                                <div class="stat-card-info">
                                    <div class="stat-card-label">Today's Crimes</div>
                                    <div class="stat-card-value" id="today-crimes">0</div>
                                </div>
                            </div>
                            <div class="stat-card-footer">
                                <span class="stat-trend down">
                                    <i class="fas fa-arrow-down"></i> 2.1%
                                </span>
                                <span style="margin-left: 0.5rem;">vs yesterday</span>
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
                                    <option value="density">Density Map (Barangay Boundaries)</option>
                                    <option value="markers">Individual Markers</option>
                                </select>
                            </div>
                            <div class="filter-item">
                                <label for="crime-type-filter">
                                    <i class="fas fa-tag"></i>
                                    Crime Type
                                </label>
                                <select id="crime-type-filter">
                                    <option value="all">All Types</option>
                                    <option value="theft">Theft</option>
                                    <option value="robbery">Robbery</option>
                                    <option value="assault">Assault</option>
                                    <option value="burglary">Burglary</option>
                                    <option value="drug">Drug-Related</option>
                                    <option value="vandalism">Vandalism</option>
                                    <option value="fraud">Fraud</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="filter-item">
                                <label for="status-filter">
                                    <i class="fas fa-info-circle"></i>
                                    Case Status
                                </label>
                                <select id="status-filter">
                                    <option value="all">All Status</option>
                                    <option value="open">Open</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </div>
                            <div class="filter-item">
                                <label for="date-from">
                                    <i class="fas fa-calendar-alt"></i>
                                    Date From
                                </label>
                                <input type="date" id="date-from">
                            </div>
                            <div class="filter-item">
                                <label for="date-to">
                                    <i class="fas fa-calendar-alt"></i>
                                    Date To
                                </label>
                                <input type="date" id="date-to">
                            </div>
                        </div>
                    </div>

                    <!-- Interactive Map -->
                    <div id="crime-map"></div>

                    <!-- Legend -->
                    <div class="legend">
                        <div class="legend-header">
                            <h3>
                                <i class="fas fa-map-marked-alt"></i>
                                Crime Type Legend
                            </h3>
                        </div>
                        <div class="legend-grid">
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #ef4444;"></div>
                                <span class="legend-label">Theft</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #dc2626;"></div>
                                <span class="legend-label">Robbery</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #f59e0b;"></div>
                                <span class="legend-label">Assault</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #8b5cf6;"></div>
                                <span class="legend-label">Burglary</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #3b82f6;"></div>
                                <span class="legend-label">Drug-Related</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #10b981;"></div>
                                <span class="legend-label">Vandalism</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #f97316;"></div>
                                <span class="legend-label">Fraud</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #6b7280;"></div>
                                <span class="legend-label">Other</span>
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
        // Quezon City boundary polygon (more accurate outline)
        const quezonCityPolygon = [
            // Northern boundary (Novaliches/Caloocan border)
            [14.7650, 121.0000],
            [14.7600, 121.0100],
            [14.7550, 121.0200],
            [14.7500, 121.0300],
            [14.7450, 121.0400],
            [14.7400, 121.0500],
            // Northeast (towards Marikina/San Mateo)
            [14.7300, 121.0600],
            [14.7200, 121.0700],
            [14.7100, 121.0800],
            [14.7000, 121.0900],
            [14.6900, 121.1000],
            // East boundary (Marikina border)
            [14.6700, 121.1050],
            [14.6500, 121.1000],
            [14.6300, 121.0900],
            // Southeast (towards Pasig/Mandaluyong)
            [14.6100, 121.0800],
            [14.5950, 121.0700],
            [14.5900, 121.0600],
            // South boundary (Mandaluyong/San Juan border)
            [14.5850, 121.0500],
            [14.5900, 121.0400],
            [14.5950, 121.0300],
            [14.6000, 121.0200],
            // Southwest (towards Manila)
            [14.6050, 121.0100],
            [14.6100, 121.0000],
            [14.6150, 120.9950],
            // West boundary (Caloocan border) - THIS IS KEY FOR CALOOCAN
            [14.6300, 120.9900],
            [14.6500, 120.9850],
            [14.6700, 120.9900],
            [14.6900, 120.9950],
            [14.7100, 121.0000],
            [14.7300, 120.9950],
            [14.7500, 120.9900],
            // Back to start (Northwest to North)
            [14.7650, 121.0000]
        ];

        // Calculate bounds from the QC polygon
        const quezonCityBounds = L.latLngBounds(quezonCityPolygon);

        // Strict bounds for Quezon City only (no padding to prevent seeing other cities)
        const strictBounds = L.latLngBounds(
            [14.5800, 120.9800], // Southwest
            [14.7700, 121.1100]  // Northeast
        );

        // Initialize map centered on Quezon City with strict restrictions
        const map = L.map('crime-map', {
            center: [14.6500, 121.0400], // Center of QC
            zoom: 13,
            minZoom: 12,
            maxZoom: 18,
            maxBounds: strictBounds,
            maxBoundsViscosity: 1.0 // Prevents dragging outside bounds completely
        });

        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 18
        }).addTo(map);

        // Create outer bounds for the mask (covers the world)
        const outerBounds = [
            [-90, -180],
            [-90, 180],
            [90, 180],
            [90, -180],
            [-90, -180]
        ];

        // Create a mask layer to gray out/disable areas outside QC
        const maskLayer = L.polygon([outerBounds, quezonCityPolygon], {
            color: 'transparent',
            fillColor: '#111827',
            fillOpacity: 0.85,
            interactive: false
        }).addTo(map);

        // Add Quezon City boundary overlay (on top of mask)
        const boundaryStyle = {
            color: '#4c8a89',
            weight: 4,
            opacity: 1,
            fillColor: 'transparent',
            fillOpacity: 0,
            dashArray: null
        };

        L.polygon(quezonCityPolygon, boundaryStyle)
            .addTo(map)
            .bindPopup('<strong>Quezon City</strong><br>Crime Data Analytics Coverage Area');

        // Function to auto-zoom map to fit all visible crime markers
        function fitMapToCrimes(crimesArray) {
            if (crimesArray.length === 0) {
                // If no crimes, fit to QC bounds
                map.fitBounds(quezonCityBounds, { padding: [50, 50] });
                return;
            }

            // Create bounds from crime locations
            const crimePoints = crimesArray.map(crime => [crime.lat, crime.lng]);
            const crimeBounds = L.latLngBounds(crimePoints);

            // Fit map to crime markers with padding
            map.fitBounds(crimeBounds, {
                padding: [50, 50],
                maxZoom: 15 // Don't zoom in too much
            });
        }

        // Crime type colors for legends
        const crimeColors = {
            'theft': '#ef4444',
            'robbery': '#dc2626',
            'assault': '#f59e0b',
            'burglary': '#8b5cf6',
            'drug': '#3b82f6',
            'vandalism': '#10b981',
            'fraud': '#f97316',
            'other': '#6b7280'
        };

        // Enhanced sample crime data with more incidents
        let crimeData = [
            { id: 1, type: 'theft', lat: 14.6760, lng: 121.0437, date: '2025-12-13', time: '14:30', barangay: 'Commonwealth', status: 'open', description: 'Mobile phone theft reported' },
            { id: 2, type: 'robbery', lat: 14.6500, lng: 121.0500, date: '2025-12-12', time: '22:15', barangay: 'Cubao', status: 'closed', description: 'Armed robbery at convenience store' },
            { id: 3, type: 'assault', lat: 14.7000, lng: 121.0300, date: '2025-12-14', time: '01:20', barangay: 'Fairview', status: 'open', description: 'Physical assault incident' },
            { id: 4, type: 'burglary', lat: 14.6300, lng: 121.0600, date: '2025-12-11', time: '03:45', barangay: 'Kamias', status: 'closed', description: 'Residential burglary' },
            { id: 5, type: 'drug', lat: 14.6900, lng: 121.0250, date: '2025-12-13', time: '18:00', barangay: 'Novaliches', status: 'open', description: 'Drug-related arrest' },
            { id: 6, type: 'theft', lat: 14.6770, lng: 121.0447, date: '2025-12-13', time: '16:45', barangay: 'Commonwealth', status: 'open', description: 'Bag snatching incident' },
            { id: 7, type: 'vandalism', lat: 14.6510, lng: 121.0510, date: '2025-12-12', time: '20:30', barangay: 'Cubao', status: 'closed', description: 'Graffiti on public property' },
            { id: 8, type: 'fraud', lat: 14.7010, lng: 121.0310, date: '2025-12-14', time: '11:00', barangay: 'Fairview', status: 'open', description: 'Credit card fraud reported' },
            { id: 9, type: 'assault', lat: 14.6310, lng: 121.0610, date: '2025-12-11', time: '23:20', barangay: 'Kamias', status: 'closed', description: 'Bar fight incident' },
            { id: 10, type: 'burglary', lat: 14.6910, lng: 121.0260, date: '2025-12-13', time: '02:15', barangay: 'Novaliches', status: 'open', description: 'Business break-in' }
        ];

        let markers = [];
        let heatLayer = null;
        let currentVisualization = 'markers'; // 'markers' or 'density'

        // Create marker for each crime
        function createMarker(crime) {
            const color = crimeColors[crime.type] || crimeColors['other'];

            const icon = L.divIcon({
                className: 'custom-marker',
                html: `<div style="background-color: ${color}; width: 25px; height: 25px; border-radius: 50%; border: 3px solid white; box-shadow: 0 0 10px rgba(0,0,0,0.5);"></div>`,
                iconSize: [25, 25],
                iconAnchor: [12, 12]
            });

            const crimeTypeIcons = {
                'theft': 'fa-shopping-bag',
                'robbery': 'fa-user-secret',
                'assault': 'fa-hand-fist',
                'burglary': 'fa-house-damage',
                'drug': 'fa-pills',
                'vandalism': 'fa-spray-can',
                'fraud': 'fa-file-invoice-dollar',
                'other': 'fa-question-circle'
            };

            const marker = L.marker([crime.lat, crime.lng], { icon: icon })
                .bindPopup(`
                    <div class="crime-marker-popup">
                        <h4>
                            <i class="fas ${crimeTypeIcons[crime.type] || 'fa-exclamation-triangle'}"></i>
                            ${crime.type.charAt(0).toUpperCase() + crime.type.slice(1)}
                        </h4>
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-calendar"></i> Date:</span>
                            <span class="info-value">${crime.date}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-clock"></i> Time:</span>
                            <span class="info-value">${crime.time}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-map-marker-alt"></i> Barangay:</span>
                            <span class="info-value">${crime.barangay}</span>
                        </div>
                        <div class="info-divider"></div>
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-info-circle"></i> Status:</span>
                            <span class="status-badge status-${crime.status}">
                                <i class="fas ${crime.status === 'open' ? 'fa-folder-open' : 'fa-check-circle'}"></i>
                                ${crime.status.toUpperCase()}
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-file-alt"></i> Details:</span>
                            <span class="info-value">${crime.description}</span>
                        </div>
                    </div>
                `)
                .addTo(map);

            return { marker, data: crime };
        }

        // Initialize all markers and auto-zoom to fit them
        function initializeMarkers() {
            markers = crimeData.map(crime => createMarker(crime));
            updateStats();

            // Auto-zoom to fit all crime markers
            fitMapToCrimes(crimeData);
        }

        // Create heat map layer from crime data
        function createHeatLayer(crimesArray) {
            // Remove existing heat layer if any
            if (heatLayer) {
                map.removeLayer(heatLayer);
                heatLayer = null;
            }

            if (crimesArray.length === 0) return;

            // Create heat points [lat, lng, intensity]
            const heatPoints = crimesArray.map(crime => {
                // Assign intensity based on crime type severity
                let intensity = 0.5;
                if (crime.type === 'robbery' || crime.type === 'assault') {
                    intensity = 1.0;
                } else if (crime.type === 'burglary' || crime.type === 'drug') {
                    intensity = 0.8;
                } else if (crime.type === 'theft' || crime.type === 'fraud') {
                    intensity = 0.6;
                }
                return [crime.lat, crime.lng, intensity];
            });

            // Create and add heat layer
            heatLayer = L.heatLayer(heatPoints, {
                radius: 35,
                blur: 25,
                maxZoom: 15,
                max: 1.0,
                gradient: {
                    0.0: '#22c55e',  // Green - Low
                    0.3: '#84cc16',  // Light green
                    0.5: '#eab308',  // Yellow - Medium
                    0.7: '#f97316',  // Orange
                    0.85: '#ef4444', // Red - High
                    1.0: '#dc2626'   // Dark red - Very High
                }
            }).addTo(map);
        }

        // Show markers mode
        function showMarkers(crimesArray) {
            // Remove heat layer
            if (heatLayer) {
                map.removeLayer(heatLayer);
                heatLayer = null;
            }

            // Show filtered markers
            const filteredIds = new Set(crimesArray.map(c => c.id));
            markers.forEach(({ marker, data }) => {
                if (filteredIds.has(data.id)) {
                    marker.addTo(map);
                } else {
                    map.removeLayer(marker);
                }
            });
        }

        // Show heat map mode
        function showHeatMap(crimesArray) {
            // Hide all markers
            markers.forEach(({ marker }) => {
                map.removeLayer(marker);
            });

            // Create heat layer
            createHeatLayer(crimesArray);
        }

        // Switch visualization mode
        function switchVisualization(mode) {
            currentVisualization = mode;
            const filtered = getFilteredCrimes();

            if (mode === 'density') {
                showHeatMap(filtered);
            } else {
                showMarkers(filtered);
            }

            // Auto-zoom to fit crimes
            fitMapToCrimes(filtered);
        }

        // Update statistics
        function updateStats() {
            const filtered = getFilteredCrimes();
            const today = new Date().toISOString().split('T')[0];

            document.getElementById('total-crimes').textContent = filtered.length;
            document.getElementById('open-cases').textContent = filtered.filter(c => c.status === 'open').length;
            document.getElementById('closed-cases').textContent = filtered.filter(c => c.status === 'closed').length;
            document.getElementById('today-crimes').textContent = filtered.filter(c => c.date === today).length;
        }

        // Get filtered crimes based on controls
        function getFilteredCrimes() {
            const typeFilter = document.getElementById('crime-type-filter').value;
            const statusFilter = document.getElementById('status-filter').value;
            const dateFrom = document.getElementById('date-from').value;
            const dateTo = document.getElementById('date-to').value;

            return crimeData.filter(crime => {
                if (typeFilter !== 'all' && crime.type !== typeFilter) return false;
                if (statusFilter !== 'all' && crime.status !== statusFilter) return false;
                if (dateFrom && crime.date < dateFrom) return false;
                if (dateTo && crime.date > dateTo) return false;
                return true;
            });
        }

        // Apply filters and auto-zoom to visible markers
        function applyFilters() {
            const filtered = getFilteredCrimes();
            const filteredIds = new Set(filtered.map(c => c.id));

            markers.forEach(({ marker, data }) => {
                if (filteredIds.has(data.id)) {
                    marker.addTo(map);
                } else {
                    map.removeLayer(marker);
                }
            });
            updateStats();

            // Auto-zoom to fit filtered crime markers
            fitMapToCrimes(filtered);
        }

        // Event listeners
        document.getElementById('crime-type-filter').addEventListener('change', applyFilters);
        document.getElementById('status-filter').addEventListener('change', applyFilters);
        document.getElementById('date-from').addEventListener('change', applyFilters);
        document.getElementById('date-to').addEventListener('change', applyFilters);

        // Initialize map with markers
        initializeMarkers();

        fetch('/api/crimes')
            .then(response => response.json())
            .then(data => {
                crimeData = data;
                markers.forEach(({ marker }) => map.removeLayer(marker));
                initializeMarkers();
            });
    </script>
</body>
</html>