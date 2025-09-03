<?php
require_once 'AuthService.php';

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

$currentUser = AuthService::checkAuthentication();

if ($currentUser) {
    returnWithSuccess(true, $currentUser);
} else {
    returnWithSuccess(false, null);
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

function returnWithSuccess($authenticated, $user)
{
    if ($authenticated && $user) {
        $retValue = '{"authenticated": true, "user": {"id": ' . $user['id'] . ', "firstName": "' . $user['firstName'] . '", "lastName": "' . $user['lastName'] . '", "userName": "' . $user['userName'] . '", "email": "' . $user['email'] . '"}}';
    } else {
        $retValue = '{"authenticated": false, "user": null}';
    }
    sendResultInfoAsJson($retValue);
}
