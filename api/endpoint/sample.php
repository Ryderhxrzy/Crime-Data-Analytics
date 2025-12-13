<?php
// Set response type to JSON
header('Content-Type: application/json');

// Allow cross-domain requests (optional but recommended)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Sample data (this can later come from a database)
$response = [
    "status" => "success",
    "message" => "Sample data retrieved successfully",
    "data" => [
        [
            "id" => 1,
            "incident_type" => "Flood",
            "location" => "Barangay San Juan",
            "severity" => "High",
            "reported_at" => "2025-12-13 14:30:00"
        ],
        [
            "id" => 2,
            "incident_type" => "Fire",
            "location" => "Barangay Mabini",
            "severity" => "Medium",
            "reported_at" => "2025-12-12 20:10:00"
        ],
        [
            "id" => 3,
            "incident_type" => "Earthquake",
            "location" => "City Proper",
            "severity" => "Low",
            "reported_at" => "2025-12-11 09:45:00"
        ]
    ]
];

// Return JSON response
echo json_encode($response, JSON_PRETTY_PRINT);
