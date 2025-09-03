<?php

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

// Clear the stay signed in cookie
if (isset($_COOKIE['stay_signed_in'])) {
    // Set cookie with past expiration to delete it
    setcookie('stay_signed_in', '', time() - 3600, '/', '', false, true);
    returnWithSuccess("Logged out successfully");
} else {
    returnWithSuccess("No active session found");
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

function returnWithSuccess($message)
{
    $retValue = '{"success": true, "message": "' . $message . '"}';
    sendResultInfoAsJson($retValue);
}