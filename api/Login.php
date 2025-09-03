<?php

$inData = getRequestInfo();

$id = 0;
$firstName = "";
$lastName = "";
$conn = new mysqli("127.0.0.1", "root", "gA5cGi6WndELh5Ky", "contact_manager");

if ($conn->connect_error) {
	returnWithError($conn->connect_error);
} else {
	// Check if login is username or email
	$stmt = $conn->prepare("SELECT id, first_name, last_name, password FROM users_table WHERE user_name=? OR email=?");
	$stmt->bind_param("ss", $inData["login"], $inData["login"]);
	$stmt->execute();
	$result = $stmt->get_result();

	// If a matching user is found, verify password
	if ($row = $result->fetch_assoc()) {
		// Verify the hashed password
		if (password_verify($inData["password"], $row['password'])) {
			// Password matches, check if user wants to stay signed in
			$keepSignedIn = isset($inData["keep_signed_in"]) ? $inData["keep_signed_in"] : false;
			
			if ($keepSignedIn) {
				// Create secure cookie with user data
				$cookieData = json_encode([
					'user_id' => $row['id'],
					'user_name' => $inData["login"]
				]);
				
				// Encrypt the cookie data
				$secretKey = 'contact_manager_secret_key_2024';
				$iv = substr(hash('sha256', $secretKey), 0, 16);
				$encryptedData = openssl_encrypt($cookieData, 'AES-128-CBC', $secretKey, 0, $iv);
				
				// Set HTTP-only cookie (no expiration as requested)
				setcookie('stay_signed_in', $encryptedData, 0, '/', '', false, true);
			}
			
			// Return the user's info
			returnWithInfo($row['first_name'], $row['last_name'], $row['id'], $keepSignedIn);
		} else {
			// Password doesn't match
			returnWithError("Invalid Password");
		}
	} else {
		// If no user is found, return an error message
		returnWithError("No Records Found");
	}

	// Close the statement and database connection
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
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
	header('Access-Control-Allow-Headers: Content-Type');
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
function returnWithInfo($firstName, $lastName, $id, $keepSignedIn = false)
{
	// Build a JSON string with the user's info
	$retValue = '{"id":' . $id . ',"firstName":"' . $firstName . '","lastName":"' . $lastName . '","keepSignedIn":' . ($keepSignedIn ? 'true' : 'false') . ',"error":""}';
	sendResultInfoAsJson($retValue);
}
