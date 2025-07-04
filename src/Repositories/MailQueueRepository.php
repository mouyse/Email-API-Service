<?php
declare(strict_types=1);

namespace Src\Repositories;

use Src\Database\DBConnector;

/**
 * Class MailQueueRepository
 * Handles database operations for the mail queue.
 */
class MailQueueRepository {
    /**
     * @var DBConnector
     */
    private DBConnector $db;

    /**
     * MailQueueRepository constructor.
     * @param DBConnector $db
     */
    public function __construct(DBConnector $db) {
        $this->db = $db;
    }

    /**
     * Adds an email record to the mail queue table.
     *
     * @param array $data
     * @return bool
     */
    public function add(array $data): bool {
        return $this->db->insert($data);
    }

    /**
     * Fetches pending email jobs from the mail queue.
     *
     * @param int $limit
     * @param int $maxRetries
     * @return array
     */
    public function fetchPending(int $limit, int $maxRetries): array {
        $stmt = $this->db->getConnection()->prepare(
            "SELECT * FROM mail_queue WHERE status = 'pending' AND attempts < ? LIMIT ?"
        );
        $stmt->execute([$maxRetries, $limit]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Updates the status of an email in the mail queue.
     *
     * @param int $id
     * @param string $status
     * @return void
     */
    public function updateStatus(int $id, string $status): void {
        $stmt = $this->db->getConnection()->prepare(
            "UPDATE mail_queue SET status = ?, attempts = attempts + 1, sent_at = NOW() WHERE id = ?"
        );
        $stmt->execute([$status, $id]);
    }
}
