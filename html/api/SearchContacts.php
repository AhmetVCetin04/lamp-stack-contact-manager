<?php

require_once 'auth_helper.php';

// Handle OPTIONS request for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: http://localhost:3000');
    header('Access-Control-Allow-Methods: GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Access-Control-Allow-Credentials: true');
    exit(0);
}

// Only allow GET method
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo '{"error": "Method not allowed"}';
    exit;
}

// Get parameters from query string
$inData = array();
if (isset($_GET['user_id'])) {
    $inData['user_id'] = $_GET['user_id'];
}
if (isset($_GET['search_term'])) {
    $inData['search_term'] = $_GET['search_term'];
}

$conn = new mysqli("127.0.0.1", "admin", "gA5cGi6WndELh5Ky", "contact_manager");

if ($conn->connect_error) {
    returnWithError($conn->connect_error);
} else {
    // Get user ID from request or cookie
    $userId = getUserFromRequest($inData);
    
    // Validate required fields
    if (empty($userId) || empty($inData["search_term"])) {
        returnWithError("Missing required fields: authentication and search_term");
    } else {
        // Search contacts with partial matching on first_name, last_name, email, and phone_number
        $searchTerm = "%" . $inData["search_term"] . "%";
        $stmt = $conn->prepare("SELECT contact_id, first_name, last_name, email, phone_number, record_created FROM contact_table WHERE user_id = ? AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR phone_number LIKE ?) ORDER BY last_name, first_name");
        $stmt->bind_param("issss", $userId, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();

        $contacts = array();
        while ($row = $result->fetch_assoc()) {
            $contacts[] = $row;
        }

        returnWithSearchResults($contacts, $inData["search_term"]);
        $stmt->close();
    }
    $conn->close();
}


function sendResultInfoAsJson($obj)
{
    header('Content-type: application/json');
    header('Access-Control-Allow-Origin: http://localhost:3000');
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Access-Control-Allow-Credentials: true');
    echo $obj;
}

function returnWithError($err)
{
    $retValue = '{"success": false, "error": "' . $err . '"}';
    sendResultInfoAsJson($retValue);
}

function returnWithSearchResults($contacts, $searchTerm)
{
    $retValue = '{"success": true, "search_term": "' . $searchTerm . '", "results_count": ' . count($contacts) . ', "contacts": ' . json_encode($contacts) . '}';
    sendResultInfoAsJson($retValue);
}
