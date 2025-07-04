<?php
declare(strict_types=1);

namespace Src\Email;
use Src\Database\DBConnector;

/**
 * Class Mailer
 * Implements MailerInterface and delegates sending to multiple mailer services.
 */
class Mailer implements MailerInterface{
    /**
     * @var DBConnector
     */
    private DBConnector $db;
    /**
     * @var array
     */
    private array $mailers;

    public string $subject;
    public string $body;
    public string $to;
    public string $from;
    public string $status;

    /**
     * Mailer constructor.
     *
     * @param DBConnector $db
     * @param array $mailers
     */
    public function __construct(DBConnector $db, array $mailers){
        $this->db = $db;
        $this->mailers = $mailers;
    }

    /**
     * Sends an email using the first available mailer service.
     *
     * @param string $subject
     * @param string $body
     * @param string $to
     * @param string $from
     * @param string $status
     * @return bool
     */
    public function send(string $subject,string $body,string $to,string $from,string $status): bool{
        
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

        foreach($this->mailers as $mailer){

            if($mailer->send($data['subject'], $data['body'], $data['to'], $data['from'], $data['status'])){
                
                // Return the last inserted ID
                // return $this->db->getLastInsertId();
                return true;

            }

        }

        return false;
        
        
    }
}