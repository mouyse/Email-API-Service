<?php
require "vendor/autoload.php";

set_exception_handler(function ($exception){
    error_log($exception->getMessage());
    http_response_code(500);
    header("HTTP/1.1 500 Internval Server Error");
    header("Content-Type: application/json");
    echo json_encode(["message" => "Something went wrong.".$exception->getMessage()]);
    exit();
});
use Src\Database\DBConnector;
use Src\Email\SendGridMailer;
use Src\Email\MailgunMailer;

// Load environment variables from .env file
$dotenv = \Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->safeLoad();

$database = DBConnector::getInstance();