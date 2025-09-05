<?php

require_once 'auth_helper.php';

$inData = getRequestInfo();

$conn = new mysqli("127.0.0.1", "admin", "gA5cGi6WndELh5Ky", "contact_manager");

if ($conn->connect_error) {
    returnWithError($conn->connect_error);
} else {
    // Get user ID from request or cookie
    $userId = getUserFromRequest($inData);
    
    // Validate required fields
    if (empty($userId) || empty($inData["first_name"]) || empty($inData["last_name"])) {
        returnWithError("Missing required fields: authentication and contact details (first_name, last_name)");
    } else {
        // Insert new contact
        $stmt = $conn->prepare("INSERT INTO contact_table (first_name, last_name, email, phone_number, user_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $inData["first_name"], $inData["last_name"], $inData["email"], $inData["phone_number"], $userId);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $contactId = $conn->insert_id;
            returnWithSuccess("Contact created successfully", $contactId);
        } else {
            returnWithError("Failed to create contact");
        }

        $stmt->close();
    }
    $conn->close();
}

function getRequestInfo()
{
    return json_decode(file_get_contents('php://input'), true);
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

function returnWithSuccess($message, $contactId = null)
{
    $retValue = '{"success": true, "message": "' . $message . '"';
    if ($contactId) {
        $retValue .= ', "contact_id": ' . $contactId;
    }
    $retValue .= '}';
    sendResultInfoAsJson($retValue);
}
