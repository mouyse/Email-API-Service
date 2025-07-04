<?php
declare(strict_types=1);

namespace Src\Queues;
use Src\Database\DBConnector;
use Src\Validators\MailFormValidator;
use Src\Repositories\MailQueueRepository;
use Src\Models\Email;

/**
 * Class MailQueue
 * Handles email queue operations such as adding emails to the queue, fetching pending jobs, and updating email status.
 */
class MailQueue
{
    /**
     * @var MailQueueRepository
     */
    private MailQueueRepository $repository;

    public string $subject;
    public string $body;
    public string $to;
    public string $from;
    public array $parameters;

    /**
     * MailQueue constructor.
     * @param MailQueueRepository $repository
     */
    public function __construct(MailQueueRepository $repository)
    {
        $this->repository = $repository;
        // Load environment variables from .env file
        $dotenv = \Dotenv\Dotenv::createUnsafeImmutable(__DIR__ . '/../');
        $dotenv->safeLoad();
    }
    
    /**
     * Fetches pending email jobs from the queue.
     *
     * @param int $limit
     * @return array
     */
    public function fetchPendingJobs(int $limit): array 
    {
        return $this->repository->fetchPending($limit, (int) ($_ENV['MAX_RETRIES'] ?? 3));
    }

    /**
     * Updates the status of an email in the queue.
     *
     * @param int $id
     * @param string $status
     * @return void
     */
    public function updateEmailStatus(int $id, string $status): void 
    {
        $this->repository->updateStatus($id, $status);
    }

    /**
     * Renders an email template by replacing placeholders with parameters.
     *
     * @param string $template
     * @param array $parameters
     * @return string
     */
    public function renderTemplate(string $template, array $parameters): string {
        foreach($parameters as $key => $value) {
           $template = str_replace('{{'.$key.'}}',htmlspecialchars($value),$template); // Extract variables from the data array
        }
        return $template;                
    }

    /**
     * Adds an email to the queue.
     *
     * @param Email $email
     * @return bool
     * @throws \Exception
     */
    public function addEmailToQueue(Email $email): bool 
    {
        // Render template with parameters
        $email->body = $this->renderTemplate($email->body, $email->parameters);

        // Prepare the data to be inserted into the database
        $data = $email->toArray();

        \Src\Validators\MailFormValidator::validate($data);

        // Insert the email data into the database
        if (!$this->repository->add($data)) {
            throw new \Exception("Failed to insert email data into the database.");
        }

        return true;
    }
}