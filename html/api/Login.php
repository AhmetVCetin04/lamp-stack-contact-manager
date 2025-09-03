<?php
require_once 'AuthService.php';

$inData = getRequestInfo();

$result = AuthService::authenticate($inData["login"], $inData["password"]);

if (isset($result['error'])) {
	returnWithError($result['error']);
} else {
	$user = $result['user'];
	returnWithInfo($user['firstName'], $user['lastName'], $user['id']);
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
function returnWithInfo($firstName, $lastName, $id)
{
	// Build a JSON string with the user's info
	$retValue = '{"id":' . $id . ',"firstName":"' . $firstName . '","lastName":"' . $lastName . '","error":""}';
	sendResultInfoAsJson($retValue);
}
