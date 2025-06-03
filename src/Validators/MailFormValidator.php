<?php
namespace Src\Validators;

class MailFormValidator{

    public static function validate(array $data): void {
        // Validate each field in the data array
        if (!self::validateEmail($data['to'])) {
            throw new \InvalidArgumentException("Invalid 'to' email address.");
        }
        if (!self::validateEmail($data['from'])) {
            throw new \InvalidArgumentException("Invalid 'from' email address.");
        }
        if (!self::validateSubject($data['subject'])) {
            throw new \InvalidArgumentException("Subject cannot be empty and must not exceed 255 characters.");
        }
        if (!self::validateBody($data['body'])) {
            throw new \InvalidArgumentException("Body cannot be empty.");
        }
        if (!self::validateStatus($data['status'])) {
            throw new \InvalidArgumentException("Invalid status. Allowed values are 'pending', 'sent', 'failed'.");
        }
    }
    public static function validateEmail(string $email): bool {
        // Check if the email is valid using PHP's filter_var function
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    public static function validateSubject(string $subject): bool {
        // Check if the subject is not empty and does not exceed a certain length
        return !empty($subject) && strlen($subject) <= 255;
    }
    public static function validateBody(string $body): bool {
        // Check if the body is not empty
        return !empty($body);
    }
    public static function validateFrom(string $from): bool {
        // Check if the 'from' email is valid
        return self::validateEmail($from);
    }
    public static function validateTo(string $to): bool {
        // Check if the 'to' email is valid
        return self::validateEmail($to);
    }
    public static function validateStatus(string $status): bool {
        // Check if the status is one of the allowed values
        $allowedStatuses = ['pending', 'sent', 'failed'];
        return in_array($status, $allowedStatuses);
    }
}