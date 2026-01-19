<?php
/**
 * Crime Categories API
 * Retrieves crime categories with optional statistics
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once __DIR__ . '/../config.php';

// Get parameters
$include_stats = isset($_GET['include_stats']) && $_GET['include_stats'] === 'true';
$source_system = $_GET['source_system'] ?? null;
$period = $_GET['period'] ?? 'all';

try {
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

    $sourceCondition = $source_system ? "AND cc.source_system = '" . $mysqli->real_escape_string($source_system) . "'" : '';

    if ($include_stats) {
        $query = "
            SELECT
                cc.id,
                cc.category_code,
                cc.category_name,
                cc.description,
                cc.icon,
                cc.color_code as color,
                cc.severity_level,
                cc.source_system,
                COUNT(ci.id) as total_incidents,
                SUM(CASE WHEN ci.clearance_status = 'cleared' THEN 1 ELSE 0 END) as cleared_cases,
                SUM(CASE WHEN ci.status = 'under_investigation' THEN 1 ELSE 0 END) as active_cases
            FROM crime_department_crime_categories cc
            LEFT JOIN crime_department_crime_incidents ci ON cc.id = ci.crime_category_id $dateCondition
            WHERE cc.is_active = 1 $sourceCondition
            GROUP BY cc.id
            ORDER BY total_incidents DESC, cc.category_name ASC
        ";
    } else {
        $query = "
            SELECT
                id,
                category_code,
                category_name,
                description,
                icon,
                color_code as color,
                severity_level,
                source_system
            FROM crime_department_crime_categories
            WHERE is_active = 1 $sourceCondition
            ORDER BY source_system, category_name
        ";
    }

    $result = $mysqli->query($query);
    $categories = [];

    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }

    // Get source system summary
    $sourceSummary = [];
    $sourceQuery = "
        SELECT
            cc.source_system,
            COUNT(DISTINCT cc.id) as category_count,
            COUNT(ci.id) as total_incidents
        FROM crime_department_crime_categories cc
        LEFT JOIN crime_department_crime_incidents ci ON cc.id = ci.crime_category_id $dateCondition
        WHERE cc.is_active = 1
        GROUP BY cc.source_system
        ORDER BY cc.source_system
    ";
    $sourceResult = $mysqli->query($sourceQuery);
    while ($row = $sourceResult->fetch_assoc()) {
        $sourceSummary[] = $row;
    }

    echo json_encode([
        'success' => true,
        'data' => $categories,
        'source_summary' => $sourceSummary,
        'total' => count($categories)
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
