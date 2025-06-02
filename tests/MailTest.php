<?php
namespace Src\Tests;
use PHPUnit\Framework\TestCase;
use Src\Email\Mailer;
use Src\Email\SendGridMailer;
use Src\Email\MailgunMailer;

class MailTest extends TestCase
{
    private $mailer;

    protected function setUp(): void
    {
        // Load environment variables from .env file
        $dotenv = \Dotenv\Dotenv::createUnsafeImmutable(__DIR__.'/../');
        $dotenv->safeLoad();

        // Mock the DBConnector and MailerInterface for testing
        $dbMock = $this->createMock(\Src\Database\DBConnector::class);
        $dbMock->method('isConnected')->willReturn(true);
        $dbMock->method('insert')->willReturn(true);
        $mailers = [];
        if(isset($_ENV['ESP1']) && $_ENV['ESP1'] === 'SendGrid') $mailers[] = new SendGridMailer($_ENV['ESP1_API_KEY'], $_ENV['ESP1_DOMAIN']);
        if(isset($_ENV['ESP2']) && $_ENV['ESP2'] === 'Mailgun') $mailers[] = new MailgunMailer($_ENV['ESP2_API_KEY'], $_ENV['ESP2_DOMAIN']);
        
        // Inject the mocks into the Mailer class
        $this->mailer = new Mailer($dbMock, $mailers);

    }


    // Test valid email
    public function testValidEmailFormat()
    {
        $this->assertTrue(
            $this->mailer->send(
                'Test Subject',
                'This is a test email body.',
                'jayy.shah16@gmail.com',
                'jayy.shah16@gmail.com'
            )
        );
    }

    // Test invalid email
    public function testInvalidEmailFormat()
    {
        $this->assertTrue(
            $this->mailer->send(
                'Test Subject',
                'This is a test email body.',
                'jayy.shah16@gmail.com',
                'invalid-email-format' // Invalid email format
            )
        );
    }
}