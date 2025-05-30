<?php
require "vendor/autoload.php";
use Src\Database\DBConnector;

// Load environment variables from .env file
$dotenv = \Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->safeLoad();

$database = (new DBConnector())->getConnection();