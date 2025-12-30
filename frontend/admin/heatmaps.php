<?php
// Authentication check - must be at the top of every admin page
require_once '../../api/middleware/auth.php';
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
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
    <link rel="stylesheet" href="../css/crime-mapping.css">
    <link rel="icon" type="image/x-icon" href="../image/favicon.ico">
    <style>
        #crime-map {
            height: 500px;
            width: 100%;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            margin: 20px 0;
        }
        
        .custom-marker {
            background: transparent;
            border: none;
        }
        
        .leaflet-control-layers {
            border-radius: 8px !important;
            overflow: hidden;
        }
        
        .gradient-legend {
            background: white;
            padding: 10px 15px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            font-size: 12px;
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
                    <!-- Crime Statistics -->
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
                                    <option value="heatmap">Heatmap</option>
                                    <option value="cluster">Clustered Markers</option>
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
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
    <script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>

    <script>
        // Wait for DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing map...');
            
            // Initialize map centered on Quezon City
            const mapContainer = document.getElementById('crime-map');
            if (!mapContainer) {
                console.error('Map container not found!');
                return;
            }
            
            const map = L.map('crime-map').setView([14.6760, 121.0437], 12);
            
            // Add OpenStreetMap tiles (more reliable)
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(map);

            // Crime type colors
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

            // Sample crime data
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
                { id: 10, type: 'burglary', lat: 14.6910, lng: 121.0260, date: '2025-12-13', time: '02:15', barangay: 'Novaliches', status: 'open', description: 'Business break-in' },
                { id: 11, type: 'theft', lat: 14.6800, lng: 121.0400, date: '2025-12-14', time: '09:15', barangay: 'Batasan Hills', status: 'open', description: 'Car break-in' },
                { id: 12, type: 'robbery', lat: 14.6400, lng: 121.0550, date: '2025-12-13', time: '19:30', barangay: 'Project 4', status: 'open', description: 'Street robbery' },
                { id: 13, type: 'drug', lat: 14.6850, lng: 121.0350, date: '2025-12-12', time: '15:45', barangay: 'Holy Spirit', status: 'closed', description: 'Drug possession arrest' },
                { id: 14, type: 'assault', lat: 14.6350, lng: 121.0650, date: '2025-12-14', time: '22:00', barangay: 'Kamuning', status: 'open', description: 'Domestic violence' },
                { id: 15, type: 'theft', lat: 14.6720, lng: 121.0380, date: '2025-12-13', time: '13:20', barangay: 'Payatas', status: 'closed', description: 'Bicycle theft' }
            ];

            let markers = [];
            let heatmapLayer = null;
            let clusterLayer = null;
            let currentMode = 'heatmap';
            let currentLegend = null;

            // Get crime icon
            function getCrimeIcon(type) {
                const icons = {
                    'theft': 'fa-shopping-bag',
                    'robbery': 'fa-user-secret',
                    'assault': 'fa-hand-fist',
                    'burglary': 'fa-house-damage',
                    'drug': 'fa-pills',
                    'vandalism': 'fa-spray-can',
                    'fraud': 'fa-file-invoice-dollar',
                    'other': 'fa-exclamation-triangle'
                };
                return icons[type] || 'fa-exclamation-triangle';
            }

            // Create custom marker icon
            function createCustomIcon(crime) {
                const color = crimeColors[crime.type] || crimeColors['other'];
                
                return L.divIcon({
                    className: 'custom-marker',
                    html: `
                        <div style="
                            background: ${color};
                            border: 3px solid white;
                            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
                            width: 28px;
                            height: 28px;
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            color: white;
                            font-size: 12px;
                            font-weight: bold;
                        ">
                            <i class="fas ${getCrimeIcon(crime.type)}"></i>
                        </div>
                    `,
                    iconSize: [28, 28],
                    iconAnchor: [14, 14],
                    popupAnchor: [0, -14]
                });
            }

            // Create popup content
            function createPopupContent(crime) {
                const color = crimeColors[crime.type] || crimeColors['other'];
                return `
                    <div style="min-width: 250px; font-family: sans-serif;">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                            <div style="
                                width: 12px;
                                height: 12px;
                                background: ${color};
                                border-radius: 50%;
                            "></div>
                            <h4 style="margin: 0; font-size: 16px; color: #333;">
                                ${crime.type.charAt(0).toUpperCase() + crime.type.slice(1)}
                            </h4>
                        </div>
                        <div style="display: grid; gap: 8px; font-size: 14px;">
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: #666;">
                                    <i class="fas fa-calendar"></i> Date:
                                </span>
                                <span style="font-weight: 500; color: #333;">${crime.date}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: #666;">
                                    <i class="fas fa-clock"></i> Time:
                                </span>
                                <span style="font-weight: 500; color: #333;">${crime.time}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: #666;">
                                    <i class="fas fa-map-marker-alt"></i> Barangay:
                                </span>
                                <span style="font-weight: 500; color: #333;">${crime.barangay}</span>
                            </div>
                            <div style="height: 1px; background: #eee; margin: 8px 0;"></div>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="color: #666;">
                                    <i class="fas fa-info-circle"></i> Status:
                                </span>
                                <span style="
                                    padding: 4px 12px;
                                    border-radius: 20px;
                                    font-size: 12px;
                                    font-weight: 600;
                                    background: ${crime.status === 'open' ? '#fef3c7' : '#d1fae5'};
                                    color: ${crime.status === 'open' ? '#92400e' : '#065f46'};
                                ">
                                    ${crime.status.toUpperCase()}
                                </span>
                            </div>
                            <div style="margin-top: 8px; padding: 8px; background: #f8f9fa; border-radius: 4px;">
                                <span style="color: #666; font-size: 13px;">
                                    <i class="fas fa-file-alt"></i> ${crime.description}
                                </span>
                            </div>
                        </div>
                    </div>
                `;
            }

            // Create heatmap layer
            function createHeatmap(data) {
                if (heatmapLayer) {
                    map.removeLayer(heatmapLayer);
                }
                
                if (data.length === 0) {
                    console.log('No data for heatmap');
                    return;
                }
                
                const points = data.map(crime => [crime.lat, crime.lng, 1]);
                
                heatmapLayer = L.heatLayer(points, {
                    radius: 25,
                    blur: 15,
                    maxZoom: 17,
                    minOpacity: 0.3,
                    gradient: {
                        0.1: 'rgba(0, 0, 255, 0.3)',
                        0.3: 'rgba(0, 255, 255, 0.5)',
                        0.5: 'rgba(0, 255, 0, 0.7)',
                        0.7: 'rgba(255, 255, 0, 0.8)',
                        1.0: 'rgba(255, 0, 0, 0.9)'
                    }
                }).addTo(map);
                
                // Add gradient legend
                addGradientLegend();
            }

            // Create cluster layer
            function createClusterLayer(data) {
                if (clusterLayer) {
                    map.removeLayer(clusterLayer);
                }
                
                const markerClusterGroup = L.markerClusterGroup({
                    chunkedLoading: true,
                    spiderfyOnMaxZoom: true,
                    showCoverageOnHover: false,
                    zoomToBoundsOnClick: true,
                    maxClusterRadius: 40,
                    iconCreateFunction: function (cluster) {
                        const count = cluster.getChildCount();
                        let color, size;
                        
                        if (count > 50) {
                            color = '#dc2626';
                            size = 50;
                        } else if (count > 20) {
                            color = '#f59e0b';
                            size = 45;
                        } else if (count > 10) {
                            color = '#3b82f6';
                            size = 40;
                        } else {
                            color = '#10b981';
                            size = 35;
                        }
                        
                        return L.divIcon({
                            html: `
                                <div style="
                                    background: ${color};
                                    color: white;
                                    width: ${size}px;
                                    height: ${size}px;
                                    border-radius: 50%;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    font-weight: bold;
                                    font-size: 14px;
                                    border: 3px solid white;
                                    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
                                ">
                                    ${count}
                                </div>
                            `,
                            className: 'cluster-marker',
                            iconSize: [size, size]
                        });
                    }
                });

                data.forEach(crime => {
                    const icon = createCustomIcon(crime);
                    const marker = L.marker([crime.lat, crime.lng], { icon: icon })
                        .bindPopup(createPopupContent(crime));
                    markerClusterGroup.addLayer(marker);
                });

                clusterLayer = markerClusterGroup;
                map.addLayer(clusterLayer);
            }

            // Create individual markers
            function createIndividualMarkers(data) {
                clearMarkers();
                
                data.forEach(crime => {
                    const icon = createCustomIcon(crime);
                    const marker = L.marker([crime.lat, crime.lng], { icon: icon })
                        .bindPopup(createPopupContent(crime))
                        .addTo(map);
                    markers.push({ marker, data: crime });
                });
            }

            // Clear all markers
            function clearMarkers() {
                markers.forEach(({ marker }) => map.removeLayer(marker));
                markers = [];
            }

            // Add gradient legend
            function addGradientLegend() {
                if (currentLegend) {
                    map.removeControl(currentLegend);
                }
                
                const legend = L.control({ position: 'bottomright' });
                
                legend.onAdd = function() {
                    const div = L.DomUtil.create('div', 'gradient-legend');
                    div.innerHTML = `
                        <div style="
                            background: white;
                            padding: 10px 15px;
                            border-radius: 8px;
                            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                            font-size: 12px;
                            font-family: sans-serif;
                        ">
                            <div style="margin-bottom: 5px; font-weight: 600; color: #333;">Crime Density</div>
                            <div style="
                                width: 150px;
                                height: 12px;
                                background: linear-gradient(to right, 
                                    rgba(0, 0, 255, 0.3),
                                    rgba(0, 255, 255, 0.5),
                                    rgba(0, 255, 0, 0.7),
                                    rgba(255, 255, 0, 0.8),
                                    rgba(255, 0, 0, 0.9)
                                );
                                margin: 5px 0;
                                border-radius: 2px;
                            "></div>
                            <div style="display: flex; justify-content: space-between; color: #666;">
                                <span>Low</span>
                                <span>High</span>
                            </div>
                        </div>
                    `;
                    return div;
                };
                
                legend.addTo(map);
                currentLegend = legend;
            }

            // Update visualization
            function updateVisualization() {
                const filteredData = getFilteredCrimes();
                currentMode = document.getElementById('visualization-mode').value;
                
                // Remove existing layers
                if (heatmapLayer) map.removeLayer(heatmapLayer);
                if (clusterLayer) map.removeLayer(clusterLayer);
                clearMarkers();
                
                if (currentLegend) {
                    map.removeControl(currentLegend);
                    currentLegend = null;
                }
                
                // Apply new visualization
                switch(currentMode) {
                    case 'heatmap':
                        createHeatmap(filteredData);
                        break;
                    case 'cluster':
                        createClusterLayer(filteredData);
                        break;
                    case 'markers':
                        createIndividualMarkers(filteredData);
                        break;
                }
                
                updateStats();
            }

            // Get filtered crimes
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

            // Update statistics
            function updateStats() {
                const filtered = getFilteredCrimes();
                const today = new Date().toISOString().split('T')[0];

                document.getElementById('total-crimes').textContent = filtered.length;
                document.getElementById('open-cases').textContent = filtered.filter(c => c.status === 'open').length;
                document.getElementById('closed-cases').textContent = filtered.filter(c => c.status === 'closed').length;
                document.getElementById('today-crimes').textContent = filtered.filter(c => c.date === today).length;
            }

            // Initialize with heatmap
            updateVisualization();

            // Event listeners
            document.getElementById('visualization-mode').addEventListener('change', updateVisualization);
            document.getElementById('crime-type-filter').addEventListener('change', updateVisualization);
            document.getElementById('status-filter').addEventListener('change', updateVisualization);
            document.getElementById('date-from').addEventListener('change', updateVisualization);
            document.getElementById('date-to').addEventListener('change', updateVisualization);

            // Add heatmap controls
            function addHeatmapControls() {
                const heatControl = L.control({ position: 'topright' });
                
                heatControl.onAdd = function() {
                    const div = L.DomUtil.create('div', 'heatmap-controls');
                    div.innerHTML = `
                        <div style="
                            background: white;
                            padding: 15px;
                            border-radius: 8px;
                            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                            width: 250px;
                            font-family: sans-serif;
                        ">
                            <div style="font-weight: 600; margin-bottom: 10px; color: #333; font-size: 14px;">
                                <i class="fas fa-sliders-h"></i> Heatmap Controls
                            </div>
                            <div style="margin-bottom: 10px;">
                                <label style="font-size: 12px; color: #666; display: block; margin-bottom: 5px;">
                                    Radius: <span id="radius-value">25</span>px
                                </label>
                                <input type="range" id="heat-radius" min="10" max="50" value="25" 
                                       style="width: 100%; margin: 5px 0;">
                            </div>
                            <div style="margin-bottom: 10px;">
                                <label style="font-size: 12px; color: #666; display: block; margin-bottom: 5px;">
                                    Blur: <span id="blur-value">15</span>px
                                </label>
                                <input type="range" id="heat-blur" min="5" max="30" value="15" 
                                       style="width: 100%; margin: 5px 0;">
                            </div>
                            <div>
                                <label style="font-size: 12px; color: #666; display: block; margin-bottom: 5px;">
                                    Intensity: <span id="intensity-value">1.0</span>
                                </label>
                                <input type="range" id="heat-intensity" min="0.1" max="2" step="0.1" value="1" 
                                       style="width: 100%; margin: 5px 0;">
                            </div>
                        </div>
                    `;
                    return div;
                };
                
                heatControl.addTo(map);
                
                // Event listeners for heatmap controls
                document.getElementById('heat-radius')?.addEventListener('input', function(e) {
                    document.getElementById('radius-value').textContent = e.target.value;
                    updateHeatmapConfig();
                });
                
                document.getElementById('heat-blur')?.addEventListener('input', function(e) {
                    document.getElementById('blur-value').textContent = e.target.value;
                    updateHeatmapConfig();
                });
                
                document.getElementById('heat-intensity')?.addEventListener('input', function(e) {
                    document.getElementById('intensity-value').textContent = e.target.value;
                    updateHeatmapConfig();
                });
            }

            // Update heatmap configuration
            function updateHeatmapConfig() {
                if (currentMode === 'heatmap' && heatmapLayer) {
                    const radius = parseInt(document.getElementById('heat-radius')?.value) || 25;
                    const blur = parseInt(document.getElementById('heat-blur')?.value) || 15;
                    const intensity = parseFloat(document.getElementById('heat-intensity')?.value) || 1;
                    
                    heatmapLayer.setOptions({
                        radius: radius,
                        blur: blur,
                        max: intensity
                    });
                }
            }

            // Initialize controls
            addHeatmapControls();

            // Set default dates
            const today = new Date().toISOString().split('T')[0];
            const lastWeek = new Date();
            lastWeek.setDate(lastWeek.getDate() - 7);
            const lastWeekStr = lastWeek.toISOString().split('T')[0];
            
            document.getElementById('date-from').value = lastWeekStr;
            document.getElementById('date-to').value = today;
            
            console.log('Map initialized successfully!');
        });
    </script>
</body>
</html>