<?php
require "bootstrap.php";

// Set the content type to JSON
header("Content-Type: application/json");
// Enable CORS for all origins
header("Access-Control-Allow-Origin: *");
// Allow specific methods and headers for CORS
header("Access-Control-Allow-Methods: GET, POST, PUT");
// Allow specific headers for CORS
header("Access-Control-Allow-Headers: Content-Type, Authorization");
// Allow preflight requests to be cached for 1 hour
header("Access-Control-Max-Age: 3600");
// Allow credentials to be included in CORS requests
header("Access-Control-Allow-Credentials: true");

// Specify one of PHP_URL_SCHEME, PHP_URL_HOST, PHP_URL_PORT, 
// PHP_URL_USER, PHP_URL_PASS, PHP_URL_PATH, PHP_URL_QUERY or PHP_URL_FRAGMENT 
// to retrieve just a specific URL component as a string (except when PHP_URL_PORT is given, 
// in which case the return value will be an int).
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);
if($uri[1] !== 'email-api-service') {
    header("HTTP/1.1 404 Not Found");
    echo json_encode(["message" => "API Not Found"]);
    exit();
}
// Check if the second segment of the URI is 'emails'
if($uri[2] !== 'api') {
    header("HTTP/1.1 404 Not Found");
    echo json_encode(["message" => "API Not Found in URL"]);
    exit();
}
// Check if the third segment of the URI is 'send'
if($uri[3] !== 'email') {
    header("HTTP/1.1 404 Not Found");
    echo json_encode(["message" => "Email Not Found in URL"]);
    exit();
}
// Check if the request method is POST
if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode(["message" => "Method Not Allowed"]);
    exit();
}
// Include the Email class
use Src\Email\Mailer;
use Src\Database\DBConnector;
// Create a new instance of the Email class
$email = new Mailer(new DBConnector());
// Get the JSON input from the request body
$input = file_get_contents("php://input");
// Decode the JSON input
$data = json_decode($input, true);
// Check if the input is valid JSON
if(json_last_error() !== JSON_ERROR_NONE) {
    header("HTTP/1.1 400 Bad Request");
    echo json_encode(["message" => "Invalid JSON"]);
    exit();
}
// Check if the required fields are present in the input
if(!isset($data['subject']) || !isset($data['body']) || !isset($data['to']) || !isset($data['from'])) {
    header("HTTP/1.1 400 Bad Request");
    echo json_encode(["message" => "Subject, body, to, and from fields cannot be empty."]);
    exit();
}

// Validate email format
if (!filter_var($data['to'], FILTER_VALIDATE_EMAIL) || !filter_var($data['from'], FILTER_VALIDATE_EMAIL)) {
    header("HTTP/1.1 400 Bad Request");
    echo json_encode(["message" => "Invalid email format for 'to' or 'from' address."]);
    exit();
}
$data['status'] = 'pending'; // Default to 'pending' if invalid status

$response = $email->send($data['subject'], $data['body'], $data['to'], $data['from'], $data['status']);

if($response){
    header("HTTP/1.1 200 OK");
    echo json_encode(["message" => "Email queued successfully", "id" => $response]);
}else{
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(["message" => "Failed to queue email"]);
}