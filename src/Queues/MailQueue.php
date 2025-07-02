<?php
namespace Src\Queues;
use Src\Database\DBConnector;
use Src\Validators\MailFormValidator;
use Src\Repositories\MailQueueRepository;

class MailQueue
{
    private MailQueueRepository $repository;

    public $subject;
    public $body;
    public $to;
    public $from;
    public $parameters;

    public function __construct(MailQueueRepository $repository)
    {
        $this->repository = $repository;
        // Load environment variables from .env file
        $dotenv = \Dotenv\Dotenv::createUnsafeImmutable(__DIR__.'/../');
        $dotenv->safeLoad();
    }
    
    public function fetchPendingJobs($limit): array {
        // $stmt = $this->db->prepare("SELECT * FROM mail_queue WHERE status = 'pending' AND attempts < ? LIMIT ?");
        // $stmt->execute([$_ENV['MAX_RETRIES'], $limit]);
        // return $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $this->repository->fetchPending($limit, $_ENV['MAX_RETRIES']);

    }

    public function updateEmailStatus($id, string $status): void {
        // $stmt = $this->db->prepare("UPDATE mail_queue SET status = ?, attempts = attempts + 1, sent_at = NOW() WHERE id = ?");
        // $stmt->execute([$status, $id]);
        $this->repository->updateStatus($id, $status);

    }

    public function renderTemplate(string $template, array $parameters): string {
        foreach($parameters as $key => $value) {
           $template = str_replace('{{'.$key.'}}',htmlspecialchars($value),$template); // Extract variables from the data array
        }
        return $template;                
    }

    public function addEmailToQueue(string $subject, string $body, string $to, string $from, array $parameters): bool {
        
        
        // Set the properties of the email
        $this->subject = $subject;
        $this->parameters = $parameters;
        $this->body = $body;
        $this->to = $to;
        $this->from = $from;

        $this->body = $this->renderTemplate($this->body, $this->parameters);


        // Prepare the data to be inserted into the database
        $data = [
            'subject' => $this->subject,
            'body' => $this->body,
            'to' => $this->to,
            'from' => $this->from,
            'status' => 'pending',
        ];

        MailFormValidator::validate($data);

        // Insert the email data into the database
        if (!$this->repository->add($data)) {
            throw new \Exception("Failed to insert email data into the database.");
        }

        return true;
        
    }
}