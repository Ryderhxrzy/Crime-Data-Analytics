<?php
/**
 * Crime Incidents API
 * Retrieves crime incidents with filtering and pagination
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once __DIR__ . '/../config.php';

// Get parameters
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = min(100, max(1, (int)($_GET['limit'] ?? 10)));
$offset = ($page - 1) * $limit;

// Filters
$period = $_GET['period'] ?? null;
$barangay_id = $_GET['barangay_id'] ?? null;
$category_id = $_GET['category_id'] ?? null;
$status = $_GET['status'] ?? null;
$clearance = $_GET['clearance'] ?? null;
$date_from = $_GET['date_from'] ?? null;
$date_to = $_GET['date_to'] ?? null;
$search = $_GET['search'] ?? null;

// Build conditions
$conditions = ['1=1'];
$params = [];
$types = '';

// Period filter
if ($period) {
    $today = date('Y-m-d');
    switch ($period) {
        case 'today':
            $conditions[] = "ci.incident_date = ?";
            $params[] = $today;
            $types .= 's';
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
    }
}

// Other filters
if ($barangay_id) {
    $conditions[] = "ci.barangay_id = ?";
    $params[] = (int)$barangay_id;
    $types .= 'i';
}

if ($category_id) {
    $conditions[] = "ci.crime_category_id = ?";
    $params[] = (int)$category_id;
    $types .= 'i';
}

if ($status) {
    $conditions[] = "ci.status = ?";
    $params[] = $status;
    $types .= 's';
}

if ($clearance) {
    $conditions[] = "ci.clearance_status = ?";
    $params[] = $clearance;
    $types .= 's';
}

if ($date_from) {
    $conditions[] = "ci.incident_date >= ?";
    $params[] = $date_from;
    $types .= 's';
}

if ($date_to) {
    $conditions[] = "ci.incident_date <= ?";
    $params[] = $date_to;
    $types .= 's';
}

if ($search) {
    $conditions[] = "(ci.incident_title LIKE ? OR ci.incident_description LIKE ? OR ci.incident_code LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= 'sss';
}

$whereClause = implode(' AND ', $conditions);

try {
    // Get total count
    $countQuery = "SELECT COUNT(*) as total FROM crime_department_crime_incidents ci WHERE $whereClause";

    if (!empty($params)) {
        $countStmt = $mysqli->prepare($countQuery);
        $countStmt->bind_param($types, ...$params);
        $countStmt->execute();
        $countResult = $countStmt->get_result();
    } else {
        $countResult = $mysqli->query($countQuery);
    }
    $totalRecords = $countResult->fetch_assoc()['total'];

    // Get incidents with related data
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
            ci.modus_operandi,
            ci.weather_condition,
            ci.assigned_officer,
            ci.created_at,
            cc.category_name,
            cc.category_code,
            cc.icon as category_icon,
            cc.color as category_color,
            cc.severity_level,
            b.barangay_name,
            b.district
        FROM crime_department_crime_incidents ci
        LEFT JOIN crime_department_crime_categories cc ON ci.crime_category_id = cc.id
        LEFT JOIN crime_department_barangays b ON ci.barangay_id = b.id
        WHERE $whereClause
        ORDER BY ci.incident_date DESC, ci.incident_time DESC
        LIMIT $limit OFFSET $offset
    ";

    if (!empty($params)) {
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $mysqli->query($query);
    }

    $incidents = [];
    while ($row = $result->fetch_assoc()) {
        // Calculate time ago
        $incidentDateTime = $row['incident_date'] . ' ' . $row['incident_time'];
        $row['time_ago'] = timeAgo($incidentDateTime);
        $incidents[] = $row;
    }

    // Calculate pagination info
    $totalPages = ceil($totalRecords / $limit);

    echo json_encode([
        'success' => true,
        'data' => $incidents,
        'pagination' => [
            'current_page' => $page,
            'per_page' => $limit,
            'total_records' => (int)$totalRecords,
            'total_pages' => $totalPages,
            'has_next' => $page < $totalPages,
            'has_prev' => $page > 1
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}

/**
 * Calculate human-readable time ago
 */
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;

    if ($diff < 60) {
        return 'just now';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . ' min' . ($mins > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return date('M d, Y', $timestamp);
    }
}
