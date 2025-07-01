<?php
namespace Src\Database;

class DBConnector{
    private static ?DBConnector $instance = null;
    private $dbConnection = null;

    private function __construct(){
        $host = getenv('DB_HOST') ?: 'localhost';
        $dbName = getenv('DB_NAME') ?: 'your_database_name';
        $username = getenv('DB_USER') ?: 'your_username';
        $password = getenv('DB_PASSWORD') ?: 'your_password';
        try {
            $this->dbConnection = new \PDO("mysql:host={$host};dbname={$dbName}", $username, $password);
            $this->dbConnection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            throw new \Exception("Connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance(): DBConnector {
        if (self::$instance === null) {
            self::$instance = new DBConnector();
        }
        return self::$instance;
    }

    public function getConnection() : ?\PDO {
        // Return the database connection
        return $this->dbConnection;
    }
    public function closeConnection() : void {
        // Close the database connection
        $this->dbConnection = null;
        self::$instance = null;
    }
    public function __destruct() {
        $this->closeConnection();
    }
    public function isConnected() : bool {
        // Check if the database connection is established
        return $this->dbConnection !== null;
    }
    public function getLastInsertId() : ?int {
        return $this->dbConnection ? $this->dbConnection->lastInsertId() : null;
    }
    public function insert(array $data): bool{
        $query = "INSERT INTO mail_queue (subject, body, to_email, from_email, status) VALUES (:subject, :body, :to_email, :from_email, :status)";
        $stmt = $this->dbConnection->prepare($query);
        $stmt->bindParam(':subject', $data['subject']);
        $stmt->bindParam(':body', $data['body']);
        $stmt->bindParam(':to_email', $data['to']);
        $stmt->bindParam(':from_email', $data['from']);
        $stmt->bindParam(':status', $data['status']);
        if ($stmt->execute()) {
            return true;
        } else {
            throw new \Exception("Failed to insert data into table.");
        }
    }
}