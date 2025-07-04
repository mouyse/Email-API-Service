<?php
declare(strict_types=1);

namespace Src\Factories;

use Src\Email\SendGridMailer;
use Src\Email\MailgunMailer;
use Src\Email\MailerInterface;

/**
 * Class MailerFactory
 * Factory for creating mailer instances (SendGrid, Mailgun, etc.) based on type and configuration.
 */
class MailerFactory {
    /**
     * Creates a mailer instance based on the given type and configuration.
     *
     * @param string $type
     * @param array $config
     * @return MailerInterface
     * @throws \InvalidArgumentException
     */
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