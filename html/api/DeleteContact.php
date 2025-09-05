<?php

require_once 'auth_helper.php';

// Handle OPTIONS request for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Access-Control-Allow-Credentials: true');
    exit(0);
}

// Only allow DELETE method
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo '{"error": "Method not allowed"}';
    exit;
}

// Get parameters from query string
$inData = array();
if (isset($_GET['contact_id'])) {
    $inData['contact_id'] = $_GET['contact_id'];
}
if (isset($_GET['user_id'])) {
    $inData['user_id'] = $_GET['user_id'];
}

$conn = new mysqli("127.0.0.1", "admin", "gA5cGi6WndELh5Ky", "contact_manager");

if ($conn->connect_error) {
    returnWithError($conn->connect_error);
} else {
    // Get user ID from request or cookie
    $userId = getUserFromRequest($inData);
    
    // Validate required fields
    if (empty($inData["contact_id"]) || empty($userId)) {
        returnWithError("Missing required fields: contact_id and authentication");
    } else {
        // Delete contact only if it belongs to the user
        $stmt = $conn->prepare("DELETE FROM contact_table WHERE contact_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $inData["contact_id"], $userId);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            returnWithSuccess("Contact deleted successfully");
        } else {
            returnWithError("Contact not found or access denied");
        }

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

function returnWithSuccess($message)
{
    $retValue = '{"success": true, "message": "' . $message . '"}';
    sendResultInfoAsJson($retValue);
}
