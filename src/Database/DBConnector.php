<?php
namespace Src\Database;

class DBConnector{
    private $dbConnection = null;

    public function __construct(){
        $host = getenv('DB_HOST') ?: 'localhost';
        $dbName = getenv('DB_NAME') ?: 'your_database_name';
        $username = getenv('DB_USER') ?: 'your_username';
        $password = getenv('DB_PASSWORD') ?: 'your_password';
        try {
            $this->dbConnection = new \PDO("mysql:host={$host};dbname={$dbName}", $username, $password);
            $this->dbConnection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }
    public function getConnection(){
        return $this->dbConnection;
    }
    public function closeConnection(){
        $this->dbConnection = null;
    }
    public function __destruct() {
        $this->closeConnection();
    }
    public function isConnected() {
        return $this->dbConnection !== null;
    }
    public function getLastInsertId() {
        return $this->dbConnection ? $this->dbConnection->lastInsertId() : null;
    }
}

// class Database {
//     private $host = 'localhost';
//     private $db_name = 'your_database_name';
//     private $username = 'your_username';
//     private $password = 'your_password';
//     public $conn;

//     public function getConnection() {
//         $this->conn = null;
//         try {
//             $this->conn = new PDO("mysql:host={$this->host};dbname={$this->db_name}", $this->username, $this->password);
//             $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//         } catch (PDOException $exception) {
//             echo "Connection error: " . $exception->getMessage();
//         }
//         return $this->conn;
//     }
// }
// Usage example
// $database = new Database();
// $db = $database->getConnection();
// if ($db) {
//     echo "Connected successfully!";
// } else {
//     echo "Connection failed!";
// }
// Note: Replace 'your_database_name', 'your_username', and 'your_password' with actual database credentials.
// Make sure to handle sensitive information like database credentials securely in production environments.
// You might want to use environment variables or a configuration file to store these credentials securely.
// Ensure that the PDO extension is enabled in your PHP installation.   