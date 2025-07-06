<?php
declare(strict_types=1);

namespace Src\Email;
use Src\Database\DBConnector;
use Src\Config\Config;

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

    private int $maxTries;

    private static array $providerFailures = [];

    /**
     * Mailer constructor.
     *
     * @param DBConnector $db
     * @param array $mailers
     */
    public function __construct(DBConnector $db, array $mailers){
        $this->db = $db;
        $this->mailers = $mailers;
        $this->maxTries = (int) Config::getInstance()->get('FAILOVER_THRESHOLD', 3); // Use Config singleton
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

        foreach($this->mailers as $providerKey => $mailer){
            // Skip providers that have reached the failover threshold
            if ((self::$providerFailures[$providerKey] ?? 0) >= $this->maxTries) {
                continue;
            }

            if($mailer->send($data['subject'], $data['body'], $data['to'], $data['from'], $data['status'])){
                // Reset failure count on success
                self::$providerFailures[$providerKey] = 0;
                // return $this->db->getLastInsertId();
                return true;
            } else {
                // Increment failure count
                self::$providerFailures[$providerKey] = (self::$providerFailures[$providerKey] ?? 0) + 1;
            }
        }

        return false;
        
        
    }
}