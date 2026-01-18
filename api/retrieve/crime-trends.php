<?php
/**
 * Crime Trends API
 * Retrieves time-based, location-based, and category-based crime trends
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once __DIR__ . '/../config.php';

// Get parameters
$trend_type = $_GET['type'] ?? 'time'; // time, location, category
$period = $_GET['period'] ?? 'year';
$granularity = $_GET['granularity'] ?? 'month'; // day, week, month, year
$barangay_id = $_GET['barangay_id'] ?? null;
$category_id = $_GET['category_id'] ?? null;
$limit = min(50, max(1, (int)($_GET['limit'] ?? 12)));

try {
    $results = [];

    switch ($trend_type) {
        case 'time':
            // Time-based trends
            $dateFormat = '';
            $groupBy = '';

            switch ($granularity) {
                case 'day':
                    $dateFormat = '%Y-%m-%d';
                    $groupBy = 'DATE(incident_date)';
                    break;
                case 'week':
                    $dateFormat = '%Y-W%u';
                    $groupBy = 'YEARWEEK(incident_date, 1)';
                    break;
                case 'month':
                default:
                    $dateFormat = '%Y-%m';
                    $groupBy = 'DATE_FORMAT(incident_date, "%Y-%m")';
                    break;
                case 'year':
                    $dateFormat = '%Y';
                    $groupBy = 'YEAR(incident_date)';
                    break;
            }

            $filterConditions = '';
            if ($barangay_id) {
                $filterConditions .= " AND barangay_id = " . (int)$barangay_id;
            }
            if ($category_id) {
                $filterConditions .= " AND crime_category_id = " . (int)$category_id;
            }

            $query = "
                SELECT
                    DATE_FORMAT(incident_date, '$dateFormat') as period_label,
                    COUNT(*) as total_incidents,
                    SUM(CASE WHEN clearance_status = 'cleared' THEN 1 ELSE 0 END) as cleared,
                    SUM(CASE WHEN status = 'under_investigation' THEN 1 ELSE 0 END) as investigating,
                    SUM(victim_count) as total_victims,
                    SUM(suspect_count) as total_suspects
                FROM crime_department_crime_incidents
                WHERE incident_date IS NOT NULL $filterConditions
                GROUP BY $groupBy
                ORDER BY period_label DESC
                LIMIT $limit
            ";

            $result = $mysqli->query($query);
            while ($row = $result->fetch_assoc()) {
                $results[] = $row;
            }
            $results = array_reverse($results); // Chronological order
            break;

        case 'location':
            // Location-based trends (by barangay/district)
            $filterConditions = '';
            if ($category_id) {
                $filterConditions .= " AND ci.crime_category_id = " . (int)$category_id;
            }

            // Add period filter
            switch ($period) {
                case 'week':
                    $filterConditions .= " AND ci.incident_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                    break;
                case 'month':
                    $filterConditions .= " AND ci.incident_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
                    break;
                case 'year':
                    $filterConditions .= " AND ci.incident_date >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
                    break;
            }

            $query = "
                SELECT
                    b.id as barangay_id,
                    b.barangay_name,
                    b.district,
                    b.latitude,
                    b.longitude,
                    COUNT(ci.id) as total_incidents,
                    SUM(CASE WHEN ci.clearance_status = 'cleared' THEN 1 ELSE 0 END) as cleared,
                    SUM(ci.victim_count) as total_victims,
                    ROUND(COUNT(ci.id) * 100.0 / NULLIF(b.population, 0) * 1000, 2) as crime_rate_per_1000
                FROM crime_department_barangays b
                LEFT JOIN crime_department_crime_incidents ci ON b.id = ci.barangay_id $filterConditions
                WHERE b.is_active = 1
                GROUP BY b.id
                ORDER BY total_incidents DESC
                LIMIT $limit
            ";

            $result = $mysqli->query($query);
            while ($row = $result->fetch_assoc()) {
                $results[] = $row;
            }
            break;

        case 'category':
            // Category-based trends
            $filterConditions = '';
            if ($barangay_id) {
                $filterConditions .= " AND ci.barangay_id = " . (int)$barangay_id;
            }

            // Add period filter
            switch ($period) {
                case 'week':
                    $filterConditions .= " AND ci.incident_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                    break;
                case 'month':
                    $filterConditions .= " AND ci.incident_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
                    break;
                case 'year':
                    $filterConditions .= " AND ci.incident_date >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
                    break;
            }

            $query = "
                SELECT
                    cc.id as category_id,
                    cc.category_code,
                    cc.category_name,
                    cc.icon,
                    cc.color,
                    cc.severity_level,
                    COUNT(ci.id) as total_incidents,
                    SUM(CASE WHEN ci.clearance_status = 'cleared' THEN 1 ELSE 0 END) as cleared,
                    SUM(ci.victim_count) as total_victims,
                    ROUND(COUNT(ci.id) * 100.0 / (SELECT COUNT(*) FROM crime_department_crime_incidents WHERE 1=1 $filterConditions), 1) as percentage
                FROM crime_department_crime_categories cc
                LEFT JOIN crime_department_crime_incidents ci ON cc.id = ci.crime_category_id $filterConditions
                WHERE cc.is_active = 1
                GROUP BY cc.id
                ORDER BY total_incidents DESC
                LIMIT $limit
            ";

            $result = $mysqli->query($query);
            while ($row = $result->fetch_assoc()) {
                $results[] = $row;
            }
            break;

        case 'hourly':
            // Hourly distribution
            $filterConditions = '';
            if ($barangay_id) {
                $filterConditions .= " AND barangay_id = " . (int)$barangay_id;
            }
            if ($category_id) {
                $filterConditions .= " AND crime_category_id = " . (int)$category_id;
            }

            switch ($period) {
                case 'week':
                    $filterConditions .= " AND incident_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                    break;
                case 'month':
                    $filterConditions .= " AND incident_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
                    break;
                case 'year':
                    $filterConditions .= " AND incident_date >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
                    break;
            }

            $query = "
                SELECT
                    HOUR(incident_time) as hour,
                    COUNT(*) as total_incidents
                FROM crime_department_crime_incidents
                WHERE incident_time IS NOT NULL $filterConditions
                GROUP BY HOUR(incident_time)
                ORDER BY hour
            ";

            $result = $mysqli->query($query);
            // Initialize all hours with 0
            $hourlyData = array_fill(0, 24, 0);
            while ($row = $result->fetch_assoc()) {
                $hourlyData[(int)$row['hour']] = (int)$row['total_incidents'];
            }

            for ($i = 0; $i < 24; $i++) {
                $results[] = [
                    'hour' => $i,
                    'hour_label' => sprintf('%02d:00', $i),
                    'total_incidents' => $hourlyData[$i]
                ];
            }
            break;

        case 'daily':
            // Day of week distribution
            $filterConditions = '';
            if ($barangay_id) {
                $filterConditions .= " AND barangay_id = " . (int)$barangay_id;
            }
            if ($category_id) {
                $filterConditions .= " AND crime_category_id = " . (int)$category_id;
            }

            switch ($period) {
                case 'month':
                    $filterConditions .= " AND incident_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
                    break;
                case 'year':
                    $filterConditions .= " AND incident_date >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
                    break;
            }

            $query = "
                SELECT
                    DAYOFWEEK(incident_date) as day_num,
                    DAYNAME(incident_date) as day_name,
                    COUNT(*) as total_incidents
                FROM crime_department_crime_incidents
                WHERE incident_date IS NOT NULL $filterConditions
                GROUP BY DAYOFWEEK(incident_date), DAYNAME(incident_date)
                ORDER BY day_num
            ";

            $result = $mysqli->query($query);
            while ($row = $result->fetch_assoc()) {
                $results[] = $row;
            }
            break;
    }

    echo json_encode([
        'success' => true,
        'trend_type' => $trend_type,
        'period' => $period,
        'granularity' => $granularity,
        'data' => $results,
        'total' => count($results)
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
