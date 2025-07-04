<?php
declare(strict_types=1);

namespace Src\Database;

use Src\Config\Config;
use Src\Database\PdoFactory;

/**
 * Class DBConnector
 * Singleton class for managing the database connection using PDO.
 */
class DBConnector
{
    /**
     * @var DBConnector|null
     */
    private static ?DBConnector $instance = null;
    /**
     * @var \PDO|null
     */
    private ?\PDO $dbConnection = null;

    /**
     * DBConnector constructor.
     * @param \PDO $pdo
     */
    private function __construct(\PDO $pdo)
    {
        $this->dbConnection = $pdo;
    }

    /**
     * Returns the singleton instance of DBConnector.
     *
     * @return DBConnector
     */
    public static function getInstance(): DBConnector
    {
        if (self::$instance === null) {
            $config = Config::getInstance()->getDatabaseConfig();
            $pdo = PdoFactory::create($config);
            self::$instance = new DBConnector($pdo);
        }
        return self::$instance;
    }

    /**
     * Returns the PDO database connection.
     *
     * @return \PDO|null
     */
    public function getConnection(): ?\PDO
    {
        return $this->dbConnection;
    }

    /**
     * Closes the database connection.
     *
     * @return void
     */
    public function closeConnection(): void
    {
        $this->dbConnection = null;
        self::$instance = null;
    }

    /**
     * Destructor to ensure the connection is closed.
     */
    public function __destruct()
    {
        $this->closeConnection();
    }

    /**
     * Checks if the database connection is established.
     *
     * @return bool
     */
    public function isConnected(): bool
    {
        return $this->dbConnection !== null;
    }

    /**
     * Returns the last inserted ID from the database.
     *
     * @return int|null
     */
    public function getLastInsertId(): ?int
    {
        return $this->dbConnection ? (int) $this->dbConnection->lastInsertId() : null;
    }

    /**
     * Inserts data into the mail_queue table.
     *
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    public function insert(array $data): bool
    {
        $query = "INSERT INTO mail_queue (subject, body, to_email, from_email, status) VALUES (:subject, :body, :to_email, :from_email, :status)";
        $stmt = $this->dbConnection->prepare($query);
        $stmt->bindParam(':subject', $data['subject']);
        $stmt->bindParam(':body', $data['body']);
        $stmt->bindParam(':to_email', $data['to']);
        $stmt->bindParam(':from_email', $data['from']);
        $stmt->bindParam(':status', $data['status']);
        if ($stmt->execute()) {
            return true;
        }
        throw new \Exception("Failed to insert data into table.");
    }
}