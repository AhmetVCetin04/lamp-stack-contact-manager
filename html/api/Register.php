<?php

$inData = getRequestInfo();

// Initialize response variables
$id = 0;
$conn = new mysqli("127.0.0.1", "admin", "gA5cGi6WndELh5Ky", "contact_manager");

if ($conn->connect_error) {
    returnWithError($conn->connect_error);
} else {
    // Check if username or email already exists
    $stmt = $conn->prepare("SELECT * FROM users_table WHERE user_name = ? OR email = ?");
    $stmt->bind_param("ss", $inData["user_name"], $inData["email"]);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Username or email already exists
        returnWithError("Username or email already taken");
    } else {
        // Hash the password
        $hashedPassword = password_hash($inData["password"], PASSWORD_DEFAULT);

        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users_table (user_name, first_name, last_name, email, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $inData["user_name"], $inData["first_name"], $inData["last_name"], $inData["email"], $hashedPassword);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            // Registration successful, get the new user ID
            $newUserId = $conn->insert_id;
            returnWithInfo($inData["first_name"], $inData["last_name"], $newUserId);
        } else {
            // Registration failed
            returnWithError("Registration failed");
        }
    }

    $stmt->close();
    $conn->close();
}

// Function to get and decode the JSON request body
function getRequestInfo()
{
    // Reads the raw POST data and decodes it from JSON into a PHP array
    return json_decode(file_get_contents('php://input'), true);
}

// Function to send a JSON response back to the client
function sendResultInfoAsJson($obj)
{
    // Set the response header to indicate JSON content
    header('Content-type: application/json');
    // Output the JSON string
    echo $obj;
}

// Function to return an error message as JSON
function returnWithError($err)
{
    // Build a JSON string with empty user info and the error message
    $retValue = '{"id":0,"firstName":"","lastName":"","error":"' . $err . '"}';
    sendResultInfoAsJson($retValue);
}

// Function to return user info as JSON
function returnWithInfo($firstName, $lastName, $id)
{
    // Build a JSON string with the user's info and no error
    $retValue = '{"id":' . $id . ',"firstName":"' . $firstName . '","lastName":"' . $lastName . '","error":""}';
    sendResultInfoAsJson($retValue);
}
