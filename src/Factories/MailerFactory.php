<?php
namespace Src\Factories;

use Src\Email\SendGridMailer;
use Src\Email\MailgunMailer;
use Src\Email\MailerInterface;

class MailerFactory {
    public static function createMailer(string $type, array $config): MailerInterface {
        switch (strtolower($type)) {
            case 'sendgrid':
                return new SendGridMailer($config['api_key'], $config['domain']);
            case 'mailgun':
                return new MailgunMailer($config['api_key'], $config['domain']);
            default:
                throw new \InvalidArgumentException("Unknown mailer type: $type");
        }
    }
} 