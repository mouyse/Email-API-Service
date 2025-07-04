<?php
declare(strict_types=1);

namespace Src\Email;

/**
 * Class MailgunMailer
 * Implements MailerInterface to send emails using Mailgun's API.
 */
class MailgunMailer implements MailerInterface
{
    /**
     * @var string
     */
    private string $apiKey;
    /**
     * @var string
     */
    private string $domain;

    /**
     * MailgunMailer constructor.
     * @param string $apiKey
     * @param string $domain
     */
    public function __construct(string $apiKey, string $domain)
    {
        $this->apiKey = $apiKey;
        $this->domain = $domain;
    }

    /**
     * Sends an email using Mailgun's API.
     *
     * @param string $subject
     * @param string $body
     * @param string $to
     * @param string $from
     * @param string $status
     * @return bool
     */
    public function send(string $subject, string $body, string $to, string $from, string $status = 'pending'): bool
    {
        // Here you would implement the logic to send an email using Mailgun's API.
        // This is a placeholder implementation.
        
        // Example: Using Mailgun's PHP library to send an email
        // $mgClient = new \Mailgun\Mailgun($this->apiKey);
        // $result = $mgClient->messages()->send($this->domain, [
        //     'from' => $from,
        //     'to' => $to,
        //     'subject' => $subject,
        //     'text' => $body
        // ]);
        
        // return true; // Return true if the email was sent successfully

        return true; // Placeholder return value
    }
}