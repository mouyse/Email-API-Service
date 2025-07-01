<?php
namespace Src\Tests;
use PHPUnit\Framework\TestCase;
use Src\Queues\MailQueue;
use Src\Email\SendGridMailer;
use Src\Email\MailgunMailer;

class MailTest extends TestCase
{
    private $mail_queue;

    protected function setUp(): void
    {
        // Load environment variables from .env file
        $dotenv = \Dotenv\Dotenv::createUnsafeImmutable(__DIR__.'/../');
        $dotenv->safeLoad();

        // Mock the DBConnector and MailerInterface for testing
        $dbMock = $this->createMock(\Src\Database\DBConnector::getInstance());
        $dbMock->method('isConnected')->willReturn(true);
        $dbMock->method('insert')->willReturn(true);
        $mailers = [];
        if(isset($_ENV['ESP1']) && $_ENV['ESP1'] === 'SendGrid') $mailers[] = new SendGridMailer($_ENV['ESP1_API_KEY'], $_ENV['ESP1_DOMAIN']);
        if(isset($_ENV['ESP2']) && $_ENV['ESP2'] === 'Mailgun') $mailers[] = new MailgunMailer($_ENV['ESP2_API_KEY'], $_ENV['ESP2_DOMAIN']);
        
        // Inject the mocks into the Mailer class
        $this->mail_queue = new MailQueue($dbMock);
    }


    // Test valid email
    public function testValidEmailFormat()
    {
        $this->assertTrue(
            $this->mail_queue->addEmailToQueue(
                'Test Subject',
                'This is a test email body.',
                'jayy.shah16@gmail.com',
                'jayy.shah16@gmail.com',
                []
            )
        );
    }

    // Test invalid 'from' email
    public function testInvalidFromEmailFormat()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid 'from' email address.");
        // Attempt to send an email with an invalid email format
        // This will throw an exception due to the invalid email format
        $this->mail_queue->addEmailToQueue(
            'Test Subject',
            'This is a test email body.',
            'jayy.shah16@gmail.com',
            'invalid-email-format', // Invalid email format
            []
        );
    }


    // Test invalid 'to' email
    public function testInvalidToEmailFormat()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid 'to' email address.");
        // Attempt to send an email with an invalid email format
        // This will throw an exception due to the invalid email format
        $this->mail_queue->addEmailToQueue(
            'Test Subject',
            'This is a test email body.',
            'invalid-email-format', // Invalid email format
            'jayy.shah16@gmail.com',
            []
        );
    }

    // Test empty subject email
    public function testInvalidSubject()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Subject cannot be empty and must not exceed 255 characters.");
        // Attempt to send an email with an invalid email format
        // This will throw an exception due to the invalid email format
        $this->mail_queue->addEmailToQueue(
            '',
            'This is a test email body.',
            'jayy.shah16@gmail.com',
            'jayy.shah16@gmail.com',
            []
        );
    }

    // Test empty body email
    public function testEmptyBody()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Body cannot be empty.");
        // Attempt to send an email with an invalid email format
        // This will throw an exception due to the invalid email format
        $this->mail_queue->addEmailToQueue(
            'This is a test subject.',
            '',
            'jayy.shah16@gmail.com',
            'jayy.shah16@gmail.com',
            []
        );
    }

    // Test empty body email
    public function testValidBodyWithParameters()
    {
        // Attempt to send an email with an invalid email format
        // This will throw an exception due to the invalid email format
        $this->assertTrue(
            $this->mail_queue->addEmailToQueue(
                'Test Subject with Parameters',
                'Hi, {{first_name}} {{last_name}}, How are you doing today?',
                'jayy.shah16@gmail.com',
                'jayy.shah16@gmail.com',
                [
                    'first_name' => 'Jayy',
                    'last_name' => 'Shah'
                ]
            )
        );
    }

    // Test empty body email
    public function testValidBodyWithNoParameters()
    {
        // Attempt to send an email with an invalid email format
        // This will throw an exception due to the invalid email format
        $this->assertTrue(
            $this->mail_queue->addEmailToQueue(
                'Test Subject with Parameters',
                'Hi, {{first_name}} {{last_name}}, How are you doing today?',
                'jayy.shah16@gmail.com',
                'jayy.shah16@gmail.com',
                [
                ]
            )
        );
    }
}