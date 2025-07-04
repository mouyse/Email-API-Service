<?php
declare(strict_types=1);

namespace Src\Controllers;

use Src\Queues\MailQueue;
use Src\Database\DBConnector;
use Src\Repositories\MailQueueRepository;
use Src\Models\Email;

/**
 * Class EmailController
 * Handles HTTP requests related to sending emails.
 */
class EmailController
{
    /**
     * @var MailQueue
     */
    private MailQueue $mailQueue;

    /**
     * EmailController constructor.
     * @param MailQueue $mailQueue
     */
    public function __construct(MailQueue $mailQueue)
    {
        $this->mailQueue = $mailQueue;
    }

    /**
     * Handles the request to send an email.
     *
     * @param array $data
     * @return void
     */
    public function sendEmail(array $data): void
    {
        $email = new Email(
            $data['subject'],
            $data['body'],
            $data['to'],
            $data['from'],
            $data['parameters'] ?? [],
            $data['status'] ?? 'pending'
        );
        $response = $this->mailQueue->addEmailToQueue($email);

        if ($response) {
            $this->respond(200, ["message" => "Email queued successfully"]);
        } else {
            $this->respond(500, ["message" => "Failed to queue email"]);
        }
    }

    /**
     * Sends a JSON response with the given status and data.
     *
     * @param int $status
     * @param array $data
     * @return void
     */
    private function respond(int $status, array $data): void
    {
        // http_response_code($status);
        // header("Content-Type: application/json");
        echo json_encode($data);
    }
}