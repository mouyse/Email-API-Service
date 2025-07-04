<?php
declare(strict_types=1);

namespace Src\Email;

/**
 * Interface MailerInterface
 * Defines the contract for mailer classes to send emails.
 */
interface MailerInterface
{
    /**
     * Sends an email using the implemented mailer service.
     *
     * @param string $subject
     * @param string $body
     * @param string $to
     * @param string $from
     * @param string $status
     * @return bool
     */
    public function send(string $subject, string $body, string $to, string $from, string $status = 'pending'): bool;
}