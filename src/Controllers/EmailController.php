<?php
namespace Src\Controllers;

use Src\Queues\MailQueue;
use Src\Database\DBConnector;
use Src\Repositories\MailQueueRepository;

class EmailController
{
    private MailQueue $mailQueue;

    public function __construct(MailQueue $mailQueue)
    {
        $this->mailQueue = $mailQueue;
    }

    public function sendEmail(array $data)
    {
        $response = $this->mailQueue->addEmailToQueue(
            $data['subject'],
            $data['body'],
            $data['to'],
            $data['from'],
            $data['parameters'] ?? []
        );

        if($response){
            $this->respond(200, ["message" => "Email queued successfully"]);
        }else{
            $this->respond(500, ["message" => "Failed to queue email"]);
        }
    }

    private function respond($status, $data)
    {
        http_response_code($status);
        header("Content-Type: application/json");
        echo json_encode($data);
    }
}