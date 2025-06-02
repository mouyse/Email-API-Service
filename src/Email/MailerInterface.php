<?php
namespace Src\Email;

interface MailerInterface
{
    /**
     * @param string $to
     * @param string $subject
     * @param string $body
     * @return bool
     */
    public function send(string $subject,string $body,string $to,string $from,string $status = 'pending'): bool;
}