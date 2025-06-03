<?php
namespace Src\Email;
use Src\Database\DBConnector;
use Src\Validators\MailFormValidator;

class Mailer implements MailerInterface{
    private $db;
    private $mailers;

    public $subject;
    public $body;
    public $to;
    public $from;
    public $status;

    // Applying Dependency Injection for the database connection
    // This allows for easier testing and flexibility in changing the database connection
    public function __construct(DBConnector $db, array $mailers){
        $this->db = $db;
        $this->mailers = $mailers;
    }

    public function send(string $subject,string $body,string $to,string $from,string $status = 'pending'): bool{
        
        // Set the properties of the email
        $this->subject = $subject;
        $this->body = $body;
        $this->to = $to;
        $this->from = $from;
        $this->status = $status;

        // Prepare the data to be inserted into the database
        $data = [
            'subject' => $this->subject,
            'body' => $this->body,
            'to' => $this->to,
            'from' => $this->from,
            'status' => $this->status
        ];

        MailFormValidator::validate($data);

        foreach($this->mailers as $mailer){

            if($mailer->send($data['subject'], $data['body'], $data['to'], $data['from'], $data['status'])){
                
                // Check if the database connection is established
                if (!$this->db->isConnected()) {
                    throw new \Exception("Database connection is not established.");
                }
                // Insert the email data into the database
                if (!$this->db->insert($data)) {
                    throw new \Exception("Failed to insert email data into the database.");
                }
                
                // Return the last inserted ID
                // return $this->db->getLastInsertId();
                return true;

            }

        }

        return false;
        
        
    }
}