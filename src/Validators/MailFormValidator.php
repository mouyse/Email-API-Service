<?php
declare(strict_types=1);

namespace Src\Validators;

/**
 * Class MailFormValidator
 * Provides static methods for validating email data fields.
 */
class MailFormValidator
{
    /**
     * Validates the provided email data array.
     *
     * @param array $data
     * @return void
     * @throws \InvalidArgumentException
     */
    public static function validate(array $data): void
    {
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

    /**
     * Validates an email address.
     *
     * @param string $email
     * @return bool
     */
    public static function validateEmail(string $email): bool
    {
        // Check if the email is valid using PHP's filter_var function
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validates the subject field.
     *
     * @param string $subject
     * @return bool
     */
    public static function validateSubject(string $subject): bool
    {
        // Check if the subject is not empty and does not exceed a certain length
        return !empty($subject) && strlen($subject) <= 255;
    }

    /**
     * Validates the body field.
     *
     * @param string $body
     * @return bool
     */
    public static function validateBody(string $body): bool
    {
        // Check if the body is not empty
        return !empty($body);
    }

    /**
     * Validates the 'from' email address.
     *
     * @param string $from
     * @return bool
     */
    public static function validateFrom(string $from): bool
    {
        // Check if the 'from' email is valid
        return self::validateEmail($from);
    }

    /**
     * Validates the 'to' email address.
     *
     * @param string $to
     * @return bool
     */
    public static function validateTo(string $to): bool
    {
        // Check if the 'to' email is valid
        return self::validateEmail($to);
    }

    /**
     * Validates the status field.
     *
     * @param string $status
     * @return bool
     */
    public static function validateStatus(string $status): bool
    {
        // Check if the status is one of the allowed values
        $allowedStatuses = ['pending', 'sent', 'failed'];
        return in_array($status, $allowedStatuses, true);
    }
}