<?php
namespace Src\Controllers;

use Src\Queues\MailQueue;
use Src\Database\DBConnector;
use Src\Repositories\MailQueueRepository;

class EmailController
{
    public function sendEmail()
    {
        // All your logic from index.php goes here, but inside this method
        $db = DBConnector::getInstance();
        $repository = new MailQueueRepository($db);
        $mailQueue = new MailQueue($repository);

        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        if(json_last_error() !== JSON_ERROR_NONE) {
            $this->respond(400, ["message" => "Invalid JSON"]);
            return;
        }
        if(!isset($data['subject']) || !isset($data['body']) || !isset($data['to']) || !isset($data['from'])) {
            $this->respond(400, ["message" => "Subject, body, to, and from fields cannot be empty."]);
            return;
        }
        if (!filter_var($data['to'], FILTER_VALIDATE_EMAIL) || !filter_var($data['from'], FILTER_VALIDATE_EMAIL)) {
            $this->respond(400, ["message" => "Invalid email format for 'to' or 'from' address."]);
            return;
        }
        $data['status'] = 'pending';

        $response = $mailQueue->addEmailToQueue(
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