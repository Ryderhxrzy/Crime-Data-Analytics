<?php
/**
 * Crime Mapping API
 * Retrieves crime incidents with coordinates for map visualization
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once __DIR__ . '/../config.php';

// Get parameters
$period = $_GET['period'] ?? 'month';
$barangay_id = $_GET['barangay_id'] ?? null;
$category_id = $_GET['category_id'] ?? null;
$status = $_GET['status'] ?? null;
$bounds = $_GET['bounds'] ?? null; // lat1,lng1,lat2,lng2
$limit = min(500, max(1, (int)($_GET['limit'] ?? 100)));

// Build conditions
$conditions = ['ci.latitude IS NOT NULL', 'ci.longitude IS NOT NULL'];

// Period filter
switch ($period) {
    case 'today':
        $conditions[] = "ci.incident_date = CURDATE()";
        break;
    case 'week':
        $conditions[] = "ci.incident_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        break;
    case 'month':
        $conditions[] = "ci.incident_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        break;
    case 'year':
        $conditions[] = "ci.incident_date >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
        break;
    case 'all':
        // No date filter for 'all'
        break;
}

if ($barangay_id) {
    $conditions[] = "ci.barangay_id = " . (int)$barangay_id;
}

if ($category_id) {
    $conditions[] = "ci.crime_category_id = " . (int)$category_id;
}

if ($status) {
    $conditions[] = "ci.status = '" . $mysqli->real_escape_string($status) . "'";
}

// Geographic bounds filter
if ($bounds) {
    $coords = explode(',', $bounds);
    if (count($coords) === 4) {
        $lat1 = (float)$coords[0];
        $lng1 = (float)$coords[1];
        $lat2 = (float)$coords[2];
        $lng2 = (float)$coords[3];
        $conditions[] = "ci.latitude BETWEEN $lat1 AND $lat2";
        $conditions[] = "ci.longitude BETWEEN $lng1 AND $lng2";
    }
}

$whereClause = implode(' AND ', $conditions);

try {
    // Get incidents for mapping
    $query = "
        SELECT
            ci.id,
            ci.incident_code,
            ci.incident_title,
            ci.incident_description,
            ci.incident_date,
            ci.incident_time,
            ci.latitude,
            ci.longitude,
            ci.address_details,
            ci.victim_count,
            ci.suspect_count,
            ci.status,
            ci.clearance_status,
            cc.category_name,
            cc.category_code,
            cc.icon as category_icon,
            cc.color_code as category_color,
            cc.severity_level,
            b.barangay_name,
            b.city_municipality as district
        FROM crime_department_crime_incidents ci
        LEFT JOIN crime_department_crime_categories cc ON ci.crime_category_id = cc.id
        LEFT JOIN crime_department_barangays b ON ci.barangay_id = b.id
        WHERE $whereClause
        ORDER BY ci.incident_date DESC, ci.incident_time DESC
        LIMIT $limit
    ";

    $result = $mysqli->query($query);
    $incidents = [];

    while ($row = $result->fetch_assoc()) {
        $incidents[] = [
            'id' => (int)$row['id'],
            'code' => $row['incident_code'],
            'title' => $row['incident_title'],
            'description' => $row['incident_description'],
            'date' => $row['incident_date'],
            'time' => $row['incident_time'],
            'lat' => (float)$row['latitude'],
            'lng' => (float)$row['longitude'],
            'address' => $row['address_details'],
            'victims' => (int)$row['victim_count'],
            'suspects' => (int)$row['suspect_count'],
            'status' => $row['status'],
            'clearance' => $row['clearance_status'],
            'category' => $row['category_name'],
            'category_code' => $row['category_code'],
            'icon' => $row['category_icon'],
            'color' => $row['category_color'] ?? '#e74c3c',
            'severity' => $row['severity_level'],
            'barangay' => $row['barangay_name'],
            'district' => $row['district']
        ];
    }

    // Get heatmap data (aggregated by location)
    $heatmapQuery = "
        SELECT
            ci.latitude as lat,
            ci.longitude as lng,
            COUNT(*) as weight
        FROM crime_department_crime_incidents ci
        WHERE $whereClause
        GROUP BY ROUND(ci.latitude, 4), ROUND(ci.longitude, 4)
    ";

    $heatmapResult = $mysqli->query($heatmapQuery);
    $heatmapData = [];

    while ($row = $heatmapResult->fetch_assoc()) {
        $heatmapData[] = [
            (float)$row['lat'],
            (float)$row['lng'],
            (int)$row['weight']
        ];
    }

    // Get cluster summary by barangay
    $clusterQuery = "
        SELECT
            b.id,
            b.barangay_name,
            b.latitude,
            b.longitude,
            COUNT(ci.id) as incident_count
        FROM crime_department_barangays b
        LEFT JOIN crime_department_crime_incidents ci ON b.id = ci.barangay_id AND $whereClause
        WHERE b.is_active = 1
        GROUP BY b.id
        HAVING incident_count > 0
        ORDER BY incident_count DESC
    ";

    $clusterResult = $mysqli->query($clusterQuery);
    $clusters = [];

    while ($row = $clusterResult->fetch_assoc()) {
        $clusters[] = [
            'barangay_id' => (int)$row['id'],
            'barangay_name' => $row['barangay_name'],
            'lat' => (float)$row['latitude'],
            'lng' => (float)$row['longitude'],
            'count' => (int)$row['incident_count']
        ];
    }

    echo json_encode([
        'success' => true,
        'data' => [
            'incidents' => $incidents,
            'heatmap' => $heatmapData,
            'clusters' => $clusters
        ],
        'total_incidents' => count($incidents),
        'period' => $period
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
