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
if($uri[1] !== 'api') {
    header("HTTP/1.1 404 Not Found");
    echo json_encode(["message" => "Not Found"]);
    exit();
}
// Check if the second segment of the URI is 'emails'
if($uri[2] !== 'emails') {
    header("HTTP/1.1 404 Not Found");
    echo json_encode(["message" => "Not Found"]);
    exit();
}
// Check if the third segment of the URI is 'send'
if($uri[3] !== 'send') {
    header("HTTP/1.1 404 Not Found");
    echo json_encode(["message" => "Not Found"]);
    exit();
}
// Check if the request method is POST
if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode(["message" => "Method Not Allowed"]);
    exit();
}
// Include the Email class
use Src\Email;
use Src\Database\DBConnector;
// Create a new instance of the Email class
$email = new Email(new DBConnector());
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
    echo json_encode(["message" => "Missing required fields"]);
    exit();
}
// Set the properties of the Email object
$email->subject = $data['subject'];
$email->body = $data['body'];
$email->to = $data['to'];
$email->from = $data['from'];
// Call the send method to send the email
if($email->send()) {
    // If the email was sent successfully, return a success response
    header("HTTP/1.1 200 OK");
    echo json_encode(["message" => "Email sent successfully"]);
} else {
    // If there was an error sending the email, return an error response
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(["message" => "Failed to send email"]);
}



/**
 * TO BE DELETED
 */

// $url = 'http://username:password@hostname:9090/path?arg=value#anchor';

// var_dump(parse_url($url));
// var_dump(parse_url($url, PHP_URL_SCHEME));
// var_dump(parse_url($url, PHP_URL_USER));
// var_dump(parse_url($url, PHP_URL_PASS));
// var_dump(parse_url($url, PHP_URL_HOST));
// var_dump(parse_url($url, PHP_URL_PORT));
// var_dump(parse_url($url, PHP_URL_PATH));
// var_dump(parse_url($url, PHP_URL_QUERY));
// var_dump(parse_url($url, PHP_URL_FRAGMENT));

/* array(8) {
  ["scheme"]=>
  string(4) "http"
  ["host"]=>
  string(8) "hostname"
  ["port"]=>
  int(9090)
  ["user"]=>
  string(8) "username"
  ["pass"]=>
  string(8) "password"
  ["path"]=>
  string(5) "/path"
  ["query"]=>
  string(9) "arg=value"
  ["fragment"]=>
  string(6) "anchor"
}
string(4) "http"
string(8) "username"
string(8) "password"
string(8) "hostname"
int(9090)
string(5) "/path"
string(9) "arg=value"
string(6) "anchor"
*/

/**
 * END OF TO BE DELETED
 */