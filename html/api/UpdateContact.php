<?php

require_once 'auth_helper.php';

// Handle OPTIONS request for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: http://localhost:3000');
    header('Access-Control-Allow-Methods: PUT, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Access-Control-Allow-Credentials: true');
    exit(0);
}

// Only allow PUT method
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo '{"error": "Method not allowed"}';
    exit;
}

$inData = getRequestInfo();

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
        // First verify the contact belongs to the user
        $stmt = $conn->prepare("SELECT contact_id FROM contact_table WHERE contact_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $inData["contact_id"], $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            returnWithError("Contact not found or access denied");
        } else {
            // Build dynamic update query based on provided fields
            $updateFields = array();
            $types = "";
            $values = array();

            if (!empty($inData["first_name"])) {
                $updateFields[] = "first_name = ?";
                $types .= "s";
                $values[] = $inData["first_name"];
            }
            if (!empty($inData["last_name"])) {
                $updateFields[] = "last_name = ?";
                $types .= "s";
                $values[] = $inData["last_name"];
            }
            if (isset($inData["email"])) {
                $updateFields[] = "email = ?";
                $types .= "s";
                $values[] = $inData["email"];
            }
            if (isset($inData["phone_number"])) {
                $updateFields[] = "phone_number = ?";
                $types .= "s";
                $values[] = $inData["phone_number"];
            }

            if (empty($updateFields)) {
                returnWithError("No fields to update");
            } else {
                // Add contact_id and user_id for WHERE clause
                $types .= "ii";
                $values[] = $inData["contact_id"];
                $values[] = $userId;

                $sql = "UPDATE contact_table SET " . implode(", ", $updateFields) . " WHERE contact_id = ? AND user_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param($types, ...$values);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    returnWithSuccess("Contact updated successfully");
                } else {
                    returnWithError("No changes made or contact not found");
                }
            }
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

function returnWithSuccess($message)
{
    $retValue = '{"success": true, "message": "' . $message . '"}';
    sendResultInfoAsJson($retValue);
}
