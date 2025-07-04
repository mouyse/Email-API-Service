<?php
namespace Src\Models;

class Email
{
    public string $subject;
    public string $body;
    public string $to;
    public string $from;
    public array $parameters;
    public string $status;

    public function __construct(string $subject, string $body, string $to, string $from, array $parameters = [], string $status = 'pending')
    {
        $this->subject = $subject;
        $this->body = $body;
        $this->to = $to;
        $this->from = $from;
        $this->parameters = $parameters;
        $this->status = $status;
    }

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