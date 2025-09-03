<?php
require_once 'AuthService.php';

function getUserFromRequest($inData)
{
    // First check if user_id is provided in request
    if (!empty($inData["user_id"])) {
        return $inData["user_id"];
    }
    
    // If no user_id, try to get from authentication (session or cookie)
    $currentUser = AuthService::checkAuthentication();
    if ($currentUser) {
        return $currentUser['id'];
    }
    
    return null;
}

?>