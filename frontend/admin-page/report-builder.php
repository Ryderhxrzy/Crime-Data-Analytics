<?php
// Authentication check - must be at the top of every admin page
require_once '../../api/middleware/auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Builder | Crime Dep.</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/admin-header.css">
    <link rel="stylesheet" href="../css/hero.css">
    <link rel="stylesheet" href="../css/sidebar-footer.css">
    <link rel="stylesheet" href="../css/report-builder.css">
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
                                <span>Admin Dashboard</span>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="report-builder.php" class="breadcrumb-link">
                                <span>Reports & Alerts</span>
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <span>Report Builder</span>
                        </li>
                    </ol>
                </nav>
                <h1>Report Builder</h1>
                <p>Create custom crime reports with flexible data sources, filters, and export options. Design report templates and schedule automated generation.</p>
            </div>
            
            <div class="sub-container">
                <div class="page-content" style="margin-bottom: 1.5rem;">
                    <!-- Report Builder Toolbar -->
                    <div class="report-toolbar">
                        <div class="toolbar-section">
                            <button class="btn btn-primary" id="newReportBtn">
                                <i class="fas fa-plus"></i> New Report
                            </button>
                            <button class="btn btn-secondary" id="saveTemplateBtn">
                                <i class="fas fa-save"></i> Save Template
                            </button>
                            <button class="btn btn-outline" id="loadTemplateBtn">
                                <i class="fas fa-folder-open"></i> Load Template
                            </button>
                        </div>
                        <div class="toolbar-section">
                            <button class="btn btn-success" id="generatePdfBtn">
                                <i class="fas fa-file-pdf"></i> Generate PDF
                            </button>
                            <button class="btn btn-success" id="generateExcelBtn">
                                <i class="fas fa-file-excel"></i> Generate Excel
                            </button>
                            <button class="btn btn-info" id="previewBtn">
                                <i class="fas fa-eye"></i> Preview
                            </button>
                        </div>
                    </div>

                    <!-- Report Builder Interface -->
                    <div class="report-builder-grid">
                        <!-- Left Panel - Data Sources & Components -->
                        <div class="builder-panel left-panel">
                            <div class="panel-section">
                                <h3 class="panel-title">
                                    <i class="fas fa-database"></i> Data Sources
                                </h3>
                                <div class="data-sources">
                                    <div class="data-source-item" draggable="true" data-source="incidents">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <span>Crime Incidents</span>
                                    </div>
                                    <div class="data-source-item" draggable="true" data-source="locations">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>Location Data</span>
                                    </div>
                                    <div class="data-source-item" draggable="true" data-source="crimetypes">
                                        <i class="fas fa-tag"></i>
                                        <span>Crime Types</span>
                                    </div>
                                    <div class="data-source-item" draggable="true" data-source="timeperiods">
                                        <i class="fas fa-calendar"></i>
                                        <span>Time Periods</span>
                                    </div>
                                    <div class="data-source-item" draggable="true" data-source="clearance">
                                        <i class="fas fa-check-circle"></i>
                                        <span>Clearance Data</span>
                                    </div>
                                    <div class="data-source-item" draggable="true" data-source="predictions">
                                        <i class="fas fa-brain"></i>
                                        <span>Predictions</span>
                                    </div>
                                </div>
                            </div>

                            <div class="panel-section">
                                <h3 class="panel-title">
                                    <i class="fas fa-shapes"></i> Report Components
                                </h3>
                                <div class="report-components">
                                    <div class="component-item" draggable="true" data-component="table">
                                        <i class="fas fa-table"></i>
                                        <span>Data Table</span>
                                    </div>
                                    <div class="component-item" draggable="true" data-component="chart">
                                        <i class="fas fa-chart-bar"></i>
                                        <span>Chart</span>
                                    </div>
                                    <div class="component-item" draggable="true" data-component="summary">
                                        <i class="fas fa-clipboard-list"></i>
                                        <span>Summary Cards</span>
                                    </div>
                                    <div class="component-item" draggable="true" data-component="map">
                                        <i class="fas fa-map"></i>
                                        <span>Map View</span>
                                    </div>
                                    <div class="component-item" draggable="true" data-component="text">
                                        <i class="fas fa-font"></i>
                                        <span>Text Block</span>
                                    </div>
                                    <div class="component-item" draggable="true" data-component="image">
                                        <i class="fas fa-image"></i>
                                        <span>Image</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Center Panel - Report Canvas -->
                        <div class="builder-panel center-panel">
                            <div class="canvas-header">
                                <h3>Report Canvas</h3>
                                <div class="canvas-tools">
                                    <button class="tool-btn" id="clearCanvasBtn" title="Clear Canvas">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <button class="tool-btn" id="gridToggleBtn" title="Toggle Grid">
                                        <i class="fas fa-th"></i>
                                    </button>
                                    <button class="tool-btn" id="zoomInBtn" title="Zoom In">
                                        <i class="fas fa-search-plus"></i>
                                    </button>
                                    <button class="tool-btn" id="zoomOutBtn" title="Zoom Out">
                                        <i class="fas fa-search-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="report-canvas" id="reportCanvas">
                                <div class="canvas-placeholder">
                                    <i class="fas fa-mouse-pointer"></i>
                                    <p>Drag and drop components here to build your report</p>
                                </div>
                            </div>
                        </div>

                        <!-- Right Panel - Properties & Filters -->
                        <div class="builder-panel right-panel">
                            <div class="panel-section">
                                <h3 class="panel-title">
                                    <i class="fas fa-filter"></i> Data Filters
                                </h3>
                                <div class="filter-section">
                                    <div class="filter-group">
                                        <label for="dateRange">Date Range</label>
                                        <select id="dateRange" class="form-control">
                                            <option value="today">Today</option>
                                            <option value="week">This Week</option>
                                            <option value="month">This Month</option>
                                            <option value="quarter">This Quarter</option>
                                            <option value="year">This Year</option>
                                            <option value="custom">Custom Range</option>
                                        </select>
                                    </div>
                                    <div class="filter-group">
                                        <label for="crimeType">Crime Type</label>
                                        <select id="crimeType" class="form-control">
                                            <option value="all">All Types</option>
                                            <option value="violent">Violent Crimes</option>
                                            <option value="property">Property Crimes</option>
                                            <option value="white-collar">White Collar</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                    <div class="filter-group">
                                        <label for="location">Location</label>
                                        <select id="location" class="form-control">
                                            <option value="all">All Locations</option>
                                            <option value="downtown">Downtown</option>
                                            <option value="suburbs">Suburbs</option>
                                            <option value="industrial">Industrial</option>
                                            <option value="residential">Residential</option>
                                        </select>
                                    </div>
                                    <div class="filter-group">
                                        <label for="severity">Severity</label>
                                        <select id="severity" class="form-control">
                                            <option value="all">All Severities</option>
                                            <option value="low">Low</option>
                                            <option value="medium">Medium</option>
                                            <option value="high">High</option>
                                            <option value="critical">Critical</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="panel-section">
                                <h3 class="panel-title">
                                    <i class="fas fa-cog"></i> Component Properties
                                </h3>
                                <div class="properties-section" id="componentProperties">
                                    <div class="no-selection">
                                        <i class="fas fa-mouse-pointer"></i>
                                        <p>Select a component to edit its properties</p>
                                    </div>
                                </div>
                            </div>

                            <div class="panel-section">
                                <h3 class="panel-title">
                                    <i class="fas fa-palette"></i> Report Settings
                                </h3>
                                <div class="report-settings">
                                    <div class="setting-group">
                                        <label for="reportTitle">Report Title</label>
                                        <input type="text" id="reportTitle" class="form-control" placeholder="Enter report title">
                                    </div>
                                    <div class="setting-group">
                                        <label for="reportDescription">Description</label>
                                        <textarea id="reportDescription" class="form-control" rows="3" placeholder="Enter report description"></textarea>
                                    </div>
                                    <div class="setting-group">
                                        <label for="reportTheme">Theme</label>
                                        <select id="reportTheme" class="form-control">
                                            <option value="default">Default</option>
                                            <option value="dark">Dark</option>
                                            <option value="light">Light</option>
                                            <option value="minimal">Minimal</option>
                                        </select>
                                    </div>
                                    <div class="setting-group">
                                        <label for="pageSize">Page Size</label>
                                        <select id="pageSize" class="form-control">
                                            <option value="a4">A4</option>
                                            <option value="letter">Letter</option>
                                            <option value="legal">Legal</option>
                                            <option value="a3">A3</option>
                                        </select>
                                    </div>
                                    <div class="setting-group">
                                        <label for="orientation">Orientation</label>
                                        <select id="orientation" class="form-control">
                                            <option value="portrait">Portrait</option>
                                            <option value="landscape">Landscape</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Template Gallery Modal -->
                    <div class="modal" id="templateModal">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3>Report Templates</h3>
                                <button class="modal-close" id="closeTemplateModal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <div class="template-grid">
                                    <div class="template-card">
                                        <div class="template-preview">
                                            <i class="fas fa-chart-line"></i>
                                        </div>
                                        <h4>Crime Trends Report</h4>
                                        <p>Monthly crime trends with charts and statistics</p>
                                        <button class="btn btn-primary btn-sm">Use Template</button>
                                    </div>
                                    <div class="template-card">
                                        <div class="template-preview">
                                            <i class="fas fa-map-marked-alt"></i>
                                        </div>
                                        <h4>Location Analysis</h4>
                                        <p>Geographic distribution of crimes with maps</p>
                                        <button class="btn btn-primary btn-sm">Use Template</button>
                                    </div>
                                    <div class="template-card">
                                        <div class="template-preview">
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </div>
                                        <h4>Incident Summary</h4>
                                        <p>Detailed incident breakdown by type and severity</p>
                                        <button class="btn btn-primary btn-sm">Use Template</button>
                                    </div>
                                    <div class="template-card">
                                        <div class="template-preview">
                                            <i class="fas fa-tachometer-alt"></i>
                                        </div>
                                        <h4>Performance Metrics</h4>
                                        <p>Clearance rates and response time analysis</p>
                                        <button class="btn btn-primary btn-sm">Use Template</button>
                                    </div>
                                    <div class="template-card">
                                        <div class="template-preview">
                                            <i class="fas fa-brain"></i>
                                        </div>
                                        <h4>Predictive Analysis</h4>
                                        <p>Crime predictions and hotspot forecasts</p>
                                        <button class="btn btn-primary btn-sm">Use Template</button>
                                    </div>
                                    <div class="template-card">
                                        <div class="template-preview">
                                            <i class="fas fa-calendar-alt"></i>
                                        </div>
                                        <h4>Weekly Briefing</h4>
                                        <p>Weekly crime summary for management briefings</p>
                                        <button class="btn btn-primary btn-sm">Use Template</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Preview Modal -->
                    <div class="modal" id="previewModal">
                        <div class="modal-content large">
                            <div class="modal-header">
                                <h3>Report Preview</h3>
                                <button class="modal-close" id="closePreviewModal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <div class="preview-container">
                                    <div class="preview-header">
                                        <h2 id="previewTitle">Crime Analytics Report</h2>
                                        <p id="previewDescription">Generated on: <?php echo date('F j, Y'); ?></p>
                                    </div>
                                    <div class="preview-content" id="previewContent">
                                        <div class="preview-placeholder">
                                            <i class="fas fa-file-alt"></i>
                                            <p>Build your report to see the preview here</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary" id="closePreview">Close</button>
                                <button class="btn btn-primary" id="exportFromPreview">Export Report</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include('../includes/admin-footer.php') ?>
    </div>

    <script>
        // Report Builder JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize drag and drop
            initializeDragAndDrop();
            
            // Modal handlers
            initializeModals();
            
            // Canvas interactions
            initializeCanvas();
            
            // Filter handlers
            initializeFilters();
            
            // Export handlers
            initializeExport();
        });

        function initializeDragAndDrop() {
            const dataSources = document.querySelectorAll('.data-source-item');
            const components = document.querySelectorAll('.component-item');
            const canvas = document.getElementById('reportCanvas');
            
            // Make all draggable items
            [...dataSources, ...components].forEach(item => {
                item.addEventListener('dragstart', handleDragStart);
                item.addEventListener('dragend', handleDragEnd);
            });
            
            canvas.addEventListener('dragover', handleDragOver);
            canvas.addEventListener('drop', handleDrop);
        }

        function handleDragStart(e) {
            e.target.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'copy';
            e.dataTransfer.setData('text/plain', JSON.stringify({
                type: e.target.classList.contains('data-source-item') ? 'data-source' : 'component',
                value: e.target.dataset.source || e.target.dataset.component
            }));
        }

        function handleDragEnd(e) {
            e.target.classList.remove('dragging');
        }

        function handleDragOver(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'copy';
        }

        function handleDrop(e) {
            e.preventDefault();
            const data = JSON.parse(e.dataTransfer.getData('text/plain'));
            const canvas = document.getElementById('reportCanvas');
            
            // Remove placeholder if exists
            const placeholder = canvas.querySelector('.canvas-placeholder');
            if (placeholder) {
                placeholder.remove();
            }
            
            // Create new element based on dropped item
            const element = createCanvasElement(data);
            if (element) {
                canvas.appendChild(element);
            }
        }

        function createCanvasElement(data) {
            const element = document.createElement('div');
            element.className = 'canvas-element';
            element.draggable = true;
            
            if (data.type === 'component') {
                switch(data.value) {
                    case 'table':
                        element.innerHTML = `
                            <div class="element-header">
                                <span><i class="fas fa-table"></i> Data Table</span>
                                <button class="remove-btn"><i class="fas fa-times"></i></button>
                            </div>
                            <div class="element-content">
                                <table class="preview-table">
                                    <thead>
                                        <tr><th>Date</th><th>Type</th><th>Location</th><th>Status</th></tr>
                                    </thead>
                                    <tbody>
                                        <tr><td>2024-01-15</td><td>Theft</td><td>Downtown</td><td>Open</td></tr>
                                        <tr><td>2024-01-14</td><td>Assault</td><td>Suburbs</td><td>Closed</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        `;
                        break;
                    case 'chart':
                        element.innerHTML = `
                            <div class="element-header">
                                <span><i class="fas fa-chart-bar"></i> Chart</span>
                                <button class="remove-btn"><i class="fas fa-times"></i></button>
                            </div>
                            <div class="element-content">
                                <div class="preview-chart">
                                    <div class="chart-bar" style="height: 60%"></div>
                                    <div class="chart-bar" style="height: 80%"></div>
                                    <div class="chart-bar" style="height: 40%"></div>
                                    <div class="chart-bar" style="height: 90%"></div>
                                </div>
                            </div>
                        `;
                        break;
                    case 'summary':
                        element.innerHTML = `
                            <div class="element-header">
                                <span><i class="fas fa-clipboard-list"></i> Summary Cards</span>
                                <button class="remove-btn"><i class="fas fa-times"></i></button>
                            </div>
                            <div class="element-content">
                                <div class="summary-cards">
                                    <div class="summary-card">
                                        <h4>Total Crimes</h4>
                                        <p class="summary-value">1,247</p>
                                    </div>
                                    <div class="summary-card">
                                        <h4>Clearance Rate</h4>
                                        <p class="summary-value">68%</p>
                                    </div>
                                </div>
                            </div>
                        `;
                        break;
                    case 'map':
                        element.innerHTML = `
                            <div class="element-header">
                                <span><i class="fas fa-map"></i> Map View</span>
                                <button class="remove-btn"><i class="fas fa-times"></i></button>
                            </div>
                            <div class="element-content">
                                <div class="preview-map">
                                    <i class="fas fa-map-marked-alt"></i>
                                    <p>Map visualization area</p>
                                </div>
                            </div>
                        `;
                        break;
                    case 'text':
                        element.innerHTML = `
                            <div class="element-header">
                                <span><i class="fas fa-font"></i> Text Block</span>
                                <button class="remove-btn"><i class="fas fa-times"></i></button>
                            </div>
                            <div class="element-content">
                                <div class="text-block" contenteditable="true">
                                    Enter your text here...
                                </div>
                            </div>
                        `;
                        break;
                    case 'image':
                        element.innerHTML = `
                            <div class="element-header">
                                <span><i class="fas fa-image"></i> Image</span>
                                <button class="remove-btn"><i class="fas fa-times"></i></button>
                            </div>
                            <div class="element-content">
                                <div class="image-placeholder">
                                    <i class="fas fa-image"></i>
                                    <p>Click to add image</p>
                                </div>
                            </div>
                        `;
                        break;
                }
            }
            
            // Add remove functionality
            const removeBtn = element.querySelector('.remove-btn');
            if (removeBtn) {
                removeBtn.addEventListener('click', function() {
                    element.remove();
                    checkCanvasEmpty();
                });
            }
            
            return element;
        }

        function checkCanvasEmpty() {
            const canvas = document.getElementById('reportCanvas');
            if (canvas.children.length === 0) {
                canvas.innerHTML = `
                    <div class="canvas-placeholder">
                        <i class="fas fa-mouse-pointer"></i>
                        <p>Drag and drop components here to build your report</p>
                    </div>
                `;
            }
        }

        function initializeModals() {
            // Template modal
            const templateModal = document.getElementById('templateModal');
            const loadTemplateBtn = document.getElementById('loadTemplateBtn');
            const closeTemplateModal = document.getElementById('closeTemplateModal');
            
            loadTemplateBtn.addEventListener('click', () => {
                templateModal.style.display = 'block';
            });
            
            closeTemplateModal.addEventListener('click', () => {
                templateModal.style.display = 'none';
            });
            
            // Preview modal
            const previewModal = document.getElementById('previewModal');
            const previewBtn = document.getElementById('previewBtn');
            const closePreviewModal = document.getElementById('closePreviewModal');
            const closePreview = document.getElementById('closePreview');
            
            previewBtn.addEventListener('click', () => {
                updatePreview();
                previewModal.style.display = 'block';
            });
            
            closePreviewModal.addEventListener('click', () => {
                previewModal.style.display = 'none';
            });
            
            closePreview.addEventListener('click', () => {
                previewModal.style.display = 'none';
            });
            
            // Close modals on outside click
            window.addEventListener('click', (e) => {
                if (e.target === templateModal) {
                    templateModal.style.display = 'none';
                }
                if (e.target === previewModal) {
                    previewModal.style.display = 'none';
                }
            });
        }

        function initializeCanvas() {
            const clearCanvasBtn = document.getElementById('clearCanvasBtn');
            const gridToggleBtn = document.getElementById('gridToggleBtn');
            const canvas = document.getElementById('reportCanvas');
            
            clearCanvasBtn.addEventListener('click', () => {
                if (confirm('Are you sure you want to clear the canvas?')) {
                    canvas.innerHTML = `
                        <div class="canvas-placeholder">
                            <i class="fas fa-mouse-pointer"></i>
                            <p>Drag and drop components here to build your report</p>
                        </div>
                    `;
                }
            });
            
            let gridEnabled = false;
            gridToggleBtn.addEventListener('click', () => {
                gridEnabled = !gridEnabled;
                canvas.classList.toggle('grid-enabled', gridEnabled);
                gridToggleBtn.classList.toggle('active', gridEnabled);
            });
        }

        function initializeFilters() {
            // Filter change handlers
            const filters = ['dateRange', 'crimeType', 'location', 'severity'];
            filters.forEach(filterId => {
                const element = document.getElementById(filterId);
                element.addEventListener('change', () => {
                    console.log(`Filter ${filterId} changed to: ${element.value}`);
                    // Here you would typically refresh the data
                });
            });
        }

        function initializeExport() {
            const generatePdfBtn = document.getElementById('generatePdfBtn');
            const generateExcelBtn = document.getElementById('generateExcelBtn');
            
            generatePdfBtn.addEventListener('click', () => {
                alert('PDF generation will be implemented with TCPDF library');
            });
            
            generateExcelBtn.addEventListener('click', () => {
                alert('Excel generation will be implemented with PhpSpreadsheet library');
            });
        }

        function updatePreview() {
            const canvas = document.getElementById('reportCanvas');
            const previewContent = document.getElementById('previewContent');
            const reportTitle = document.getElementById('reportTitle').value || 'Crime Analytics Report';
            
            document.getElementById('previewTitle').textContent = reportTitle;
            
            if (canvas.querySelector('.canvas-placeholder')) {
                previewContent.innerHTML = `
                    <div class="preview-placeholder">
                        <i class="fas fa-file-alt"></i>
                        <p>Build your report to see the preview here</p>
                    </div>
                `;
            } else {
                previewContent.innerHTML = canvas.innerHTML;
            }
        }
    </script>
</body>
</html>