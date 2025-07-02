<?php
namespace Src\Repositories;

use Src\Database\DBConnector;

class MailQueueRepository {
    private $db;

    public function __construct(DBConnector $db) {
        $this->db = $db;
    }

    public function add(array $data): bool {
        return $this->db->insert($data);
    }

    public function fetchPending($limit, $maxRetries): array {
        $stmt = $this->db->getConnection()->prepare(
            "SELECT * FROM mail_queue WHERE status = 'pending' AND attempts < ? LIMIT ?"
        );
        $stmt->execute([$maxRetries, $limit]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function updateStatus($id, string $status): void {
        $stmt = $this->db->getConnection()->prepare(
            "UPDATE mail_queue SET status = ?, attempts = attempts + 1, sent_at = NOW() WHERE id = ?"
        );
        $stmt->execute([$status, $id]);
    }
}
