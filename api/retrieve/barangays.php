<?php
/**
 * Barangays API
 * Retrieves barangay data with optional crime statistics
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once __DIR__ . '/../config.php';

// Get parameters
$include_stats = isset($_GET['include_stats']) && $_GET['include_stats'] === 'true';
$district = $_GET['district'] ?? null;
$period = $_GET['period'] ?? 'all';

try {
    if ($include_stats) {
        // Calculate date range
        $dateCondition = '';
        $today = date('Y-m-d');

        switch ($period) {
            case 'today':
                $dateCondition = "AND ci.incident_date = '$today'";
                break;
            case 'week':
                $dateCondition = "AND ci.incident_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                break;
            case 'month':
                $dateCondition = "AND ci.incident_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
                break;
            case 'year':
                $dateCondition = "AND ci.incident_date >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
                break;
        }

        $districtCondition = $district ? "AND b.district = '" . $mysqli->real_escape_string($district) . "'" : '';

        $query = "
            SELECT
                b.id,
                b.barangay_name,
                b.district,
                b.latitude,
                b.longitude,
                b.population,
                COUNT(ci.id) as total_incidents,
                SUM(CASE WHEN ci.clearance_status = 'cleared' THEN 1 ELSE 0 END) as cleared_cases,
                SUM(CASE WHEN ci.status = 'under_investigation' THEN 1 ELSE 0 END) as active_cases
            FROM crime_department_barangays b
            LEFT JOIN crime_department_crime_incidents ci ON b.id = ci.barangay_id $dateCondition
            WHERE b.is_active = 1 $districtCondition
            GROUP BY b.id
            ORDER BY total_incidents DESC, b.barangay_name ASC
        ";
    } else {
        $districtCondition = $district ? "AND district = '" . $mysqli->real_escape_string($district) . "'" : '';

        $query = "
            SELECT
                id,
                barangay_name,
                district,
                latitude,
                longitude,
                population
            FROM crime_department_barangays
            WHERE is_active = 1 $districtCondition
            ORDER BY district, barangay_name
        ";
    }

    $result = $mysqli->query($query);
    $barangays = [];

    while ($row = $result->fetch_assoc()) {
        $barangays[] = $row;
    }

    // Get district summary if stats included
    $districtSummary = [];
    if ($include_stats) {
        $districtQuery = "
            SELECT
                b.district,
                COUNT(DISTINCT b.id) as barangay_count,
                COUNT(ci.id) as total_incidents
            FROM crime_department_barangays b
            LEFT JOIN crime_department_crime_incidents ci ON b.id = ci.barangay_id $dateCondition
            WHERE b.is_active = 1
            GROUP BY b.district
            ORDER BY b.district
        ";
        $districtResult = $mysqli->query($districtQuery);
        while ($row = $districtResult->fetch_assoc()) {
            $districtSummary[] = $row;
        }
    }

    echo json_encode([
        'success' => true,
        'data' => $barangays,
        'district_summary' => $districtSummary,
        'total' => count($barangays)
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
