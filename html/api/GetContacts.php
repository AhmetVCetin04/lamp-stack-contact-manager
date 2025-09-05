<?php

require_once 'auth_helper.php';

// Handle OPTIONS request for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
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

// Get user_id from query parameter instead of JSON body
$inData = array();
if (isset($_GET['user_id'])) {
    $inData['user_id'] = $_GET['user_id'];
}

$conn = new mysqli("127.0.0.1", "admin", "gA5cGi6WndELh5Ky", "contact_manager");

if ($conn->connect_error) {
    returnWithError($conn->connect_error);
} else {
    // Get user ID from request or cookie
    $userId = getUserFromRequest($inData);
    
    if (empty($userId)) {
        returnWithError("Authentication required");
    } else {
        // Get all contacts for the user
        $stmt = $conn->prepare("SELECT contact_id, first_name, last_name, email, phone_number, record_created FROM contact_table WHERE user_id = ? ORDER BY last_name, first_name");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $contacts = array();
        while ($row = $result->fetch_assoc()) {
            $contacts[] = $row;
        }

        returnWithContacts($contacts);
        $stmt->close();
    }
    $conn->close();
}


function sendResultInfoAsJson($obj)
{
    header('Content-type: application/json');
    header('Access-Control-Allow-Origin: *');
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

function returnWithContacts($contacts)
{
    $retValue = '{"success": true, "contacts": ' . json_encode($contacts) . '}';
    sendResultInfoAsJson($retValue);
}
