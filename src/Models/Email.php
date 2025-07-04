<?php
declare(strict_types=1);

namespace Src\Models;

/**
 * Class Email
 * Represents an email entity with all necessary properties and logic.
 */
class Email
{
    /** @var string */
    public string $subject;
    /** @var string */
    public string $body;
    /** @var string */
    public string $to;
    /** @var string */
    public string $from;
    /** @var array */
    public array $parameters;
    /** @var string */
    public string $status;

    /**
     * Email constructor.
     * @param string $subject
     * @param string $body
     * @param string $to
     * @param string $from
     * @param array $parameters
     * @param string $status
     */
    public function __construct(string $subject, string $body, string $to, string $from, array $parameters = [], string $status = 'pending')
    {
        $this->subject = $subject;
        $this->body = $body;
        $this->to = $to;
        $this->from = $from;
        $this->parameters = $parameters;
        $this->status = $status;
    }

    /**
     * Converts the email object to an associative array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'subject' => $this->subject,
            'body' => $this->body,
            'to' => $this->to,
            'from' => $this->from,
            'status' => $this->status,
        ];
    }
} 