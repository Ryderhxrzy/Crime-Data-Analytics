<?php
/**
 * Crime Statistics API
 * Retrieves aggregated crime statistics from the database
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once __DIR__ . '/../config.php';

// Get time period filter
$period = $_GET['period'] ?? 'all';
$barangay_id = $_GET['barangay_id'] ?? null;
$category_id = $_GET['category_id'] ?? null;

// Calculate date range based on period
$today = date('Y-m-d');
$dateCondition = '';

switch ($period) {
    case 'today':
        $dateCondition = "AND incident_date = '$today'";
        break;
    case 'week':
        $weekAgo = date('Y-m-d', strtotime('-7 days'));
        $dateCondition = "AND incident_date >= '$weekAgo'";
        break;
    case 'month':
        $monthAgo = date('Y-m-d', strtotime('-30 days'));
        $dateCondition = "AND incident_date >= '$monthAgo'";
        break;
    case 'year':
        $yearAgo = date('Y-m-d', strtotime('-1 year'));
        $dateCondition = "AND incident_date >= '$yearAgo'";
        break;
    default:
        $dateCondition = '';
}

// Add filters
$filterConditions = '';
if ($barangay_id) {
    $barangay_id = (int)$barangay_id;
    $filterConditions .= " AND ci.barangay_id = $barangay_id";
}
if ($category_id) {
    $category_id = (int)$category_id;
    $filterConditions .= " AND ci.crime_category_id = $category_id";
}

try {
    // Total crimes count
    $totalQuery = "SELECT COUNT(*) as total FROM crime_department_crime_incidents ci WHERE 1=1 $dateCondition $filterConditions";
    $totalResult = $mysqli->query($totalQuery);
    $totalCrimes = $totalResult->fetch_assoc()['total'];

    // Today's crimes (always current date)
    $todayQuery = "SELECT COUNT(*) as total FROM crime_department_crime_incidents WHERE incident_date = '$today'";
    $todayResult = $mysqli->query($todayQuery);
    $todayCrimes = $todayResult->fetch_assoc()['total'];

    // Yesterday's crimes for comparison
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    $yesterdayQuery = "SELECT COUNT(*) as total FROM crime_department_crime_incidents WHERE incident_date = '$yesterday'";
    $yesterdayResult = $mysqli->query($yesterdayQuery);
    $yesterdayCrimes = $yesterdayResult->fetch_assoc()['total'];

    // This month's crimes
    $monthStart = date('Y-m-01');
    $thisMonthQuery = "SELECT COUNT(*) as total FROM crime_department_crime_incidents WHERE incident_date >= '$monthStart'";
    $thisMonthResult = $mysqli->query($thisMonthQuery);
    $thisMonthCrimes = $thisMonthResult->fetch_assoc()['total'];

    // Last month's crimes for comparison
    $lastMonthStart = date('Y-m-01', strtotime('-1 month'));
    $lastMonthEnd = date('Y-m-t', strtotime('-1 month'));
    $lastMonthQuery = "SELECT COUNT(*) as total FROM crime_department_crime_incidents WHERE incident_date BETWEEN '$lastMonthStart' AND '$lastMonthEnd'";
    $lastMonthResult = $mysqli->query($lastMonthQuery);
    $lastMonthCrimes = $lastMonthResult->fetch_assoc()['total'];

    // Resolved/Cleared cases
    $resolvedQuery = "SELECT COUNT(*) as total FROM crime_department_crime_incidents ci WHERE clearance_status = 'cleared' $dateCondition $filterConditions";
    $resolvedResult = $mysqli->query($resolvedQuery);
    $resolvedCases = $resolvedResult->fetch_assoc()['total'];

    // Under investigation
    $investigationQuery = "SELECT COUNT(*) as total FROM crime_department_crime_incidents ci WHERE status = 'under_investigation' $dateCondition $filterConditions";
    $investigationResult = $mysqli->query($investigationQuery);
    $underInvestigation = $investigationResult->fetch_assoc()['total'];

    // Crime by category
    $categoryQuery = "
        SELECT
            cc.category_name,
            cc.category_code,
            cc.icon,
            cc.color,
            COUNT(ci.id) as count
        FROM crime_department_crime_categories cc
        LEFT JOIN crime_department_crime_incidents ci ON cc.id = ci.crime_category_id $dateCondition $filterConditions
        GROUP BY cc.id
        ORDER BY count DESC
    ";
    $categoryResult = $mysqli->query($categoryQuery);
    $crimesByCategory = [];
    while ($row = $categoryResult->fetch_assoc()) {
        $crimesByCategory[] = $row;
    }

    // Crime by status
    $statusQuery = "
        SELECT
            status,
            COUNT(*) as count
        FROM crime_department_crime_incidents ci
        WHERE 1=1 $dateCondition $filterConditions
        GROUP BY status
    ";
    $statusResult = $mysqli->query($statusQuery);
    $crimesByStatus = [];
    while ($row = $statusResult->fetch_assoc()) {
        $crimesByStatus[$row['status']] = (int)$row['count'];
    }

    // Crime by clearance status
    $clearanceQuery = "
        SELECT
            clearance_status,
            COUNT(*) as count
        FROM crime_department_crime_incidents ci
        WHERE 1=1 $dateCondition $filterConditions
        GROUP BY clearance_status
    ";
    $clearanceResult = $mysqli->query($clearanceQuery);
    $crimesByClearance = [];
    while ($row = $clearanceResult->fetch_assoc()) {
        $crimesByClearance[$row['clearance_status']] = (int)$row['count'];
    }

    // Calculate trends
    $monthlyTrend = $lastMonthCrimes > 0 ? round((($thisMonthCrimes - $lastMonthCrimes) / $lastMonthCrimes) * 100, 1) : 0;
    $dailyTrend = $yesterdayCrimes > 0 ? round((($todayCrimes - $yesterdayCrimes) / $yesterdayCrimes) * 100, 1) : 0;
    $resolutionRate = $totalCrimes > 0 ? round(($resolvedCases / $totalCrimes) * 100, 1) : 0;

    // Return response
    echo json_encode([
        'success' => true,
        'data' => [
            'overview' => [
                'total_crimes' => (int)$totalCrimes,
                'today_crimes' => (int)$todayCrimes,
                'resolved_cases' => (int)$resolvedCases,
                'under_investigation' => (int)$underInvestigation,
                'resolution_rate' => $resolutionRate,
                'monthly_trend' => $monthlyTrend,
                'daily_trend' => $dailyTrend
            ],
            'by_category' => $crimesByCategory,
            'by_status' => $crimesByStatus,
            'by_clearance' => $crimesByClearance,
            'period' => $period,
            'generated_at' => date('Y-m-d H:i:s')
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
