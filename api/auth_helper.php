<?php

function getUserFromRequest($inData)
{
    // First check if user_id is provided in request
    if (!empty($inData["user_id"])) {
        return $inData["user_id"];
    }
    
    // If no user_id, try to get from cookie
    $cookieUser = checkCookieAuth();
    if ($cookieUser) {
        return $cookieUser['id'];
    }
    
    return null;
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

?>