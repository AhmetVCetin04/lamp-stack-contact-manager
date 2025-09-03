<?php

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

$currentUser = checkCookieAuth();

if ($currentUser) {
    returnWithSuccess(true, $currentUser);
} else {
    returnWithSuccess(false, null);
}

function checkCookieAuth()
{
    if (!isset($_COOKIE['stay_signed_in'])) {
        return null;
    }

    try {
        // Decrypt the cookie data
        $secretKey = 'contact_manager_secret_key_2024';
        $iv = substr(hash('sha256', $secretKey), 0, 16);
        $decryptedData = openssl_decrypt($_COOKIE['stay_signed_in'], 'AES-128-CBC', $secretKey, 0, $iv);

        if ($decryptedData === false) {
            return null;
        }

        $userData = json_decode($decryptedData, true);
        if (!$userData || !isset($userData['user_id'])) {
            return null;
        }

        // Verify user still exists in database
        $conn = new mysqli("127.0.0.1", "root", "gA5cGi6WndELh5Ky", "contact_manager");
        if ($conn->connect_error) {
            return null;
        }

        $stmt = $conn->prepare("SELECT id, first_name, last_name, user_name, email FROM users_table WHERE id = ?");
        $stmt->bind_param("i", $userData['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $stmt->close();
            $conn->close();
            return $row;
        }

        $stmt->close();
        $conn->close();
        return null;
    } catch (Exception $e) {
        return null;
    }
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
        $retValue = '{"authenticated": true, "user": {"id": ' . $user['id'] . ', "firstName": "' . $user['first_name'] . '", "lastName": "' . $user['last_name'] . '", "userName": "' . $user['user_name'] . '", "email": "' . $user['email'] . '"}}';
    } else {
        $retValue = '{"authenticated": false, "user": null}';
    }
    sendResultInfoAsJson($retValue);
}
