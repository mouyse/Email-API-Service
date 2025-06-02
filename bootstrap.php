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

$database = (new DBConnector())->getConnection();

$mailers = [];
if(isset($_ENV['ESP1']) && $_ENV['ESP1'] === 'SendGrid') $mailers[] = new SendGridMailer($_ENV['ESP1_API_KEY'], $_ENV['ESP1_DOMAIN']);
if(isset($_ENV['ESP2']) && $_ENV['ESP2'] === 'Mailgun') $mailers[] = new MailgunMailer($_ENV['ESP2_API_KEY'], $_ENV['ESP2_DOMAIN']);

if(empty($mailers)){
    http_response_code(500);
    header("HTTP/1.1 500 Internal Server Error");
    header("Content-Type: application/json");
    echo json_encode(["message" => "No email service provider configured."]);
    exit();
}