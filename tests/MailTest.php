<?php
namespace Src\Tests;
use PHPUnit\Framework\TestCase;
use Src\Queues\MailQueue;
use Src\Email\SendGridMailer;
use Src\Email\MailgunMailer;
use Src\Factories\MailerFactory;
use Src\Repositories\MailQueueRepository;
use Src\Models\Email;
use Src\Database\DBConnector;

/**
 * Class MailTest
 * Contains unit tests for the MailQueue functionality and email validation.
 */
class MailTest extends TestCase
{
    /**
     * @var MailQueue
     */
    private $mail_queue;

    /**
     * Sets up the test environment and initializes MailQueue with a mocked repository.
     * @return void
     */
    protected function setUp(): void
    {
        // Load environment variables from .env file
        $dotenv = \Dotenv\Dotenv::createUnsafeImmutable(__DIR__.'/../');
        $dotenv->safeLoad();

        // Mock the DBConnector and MailerInterface for testing
        $dbMock = $this->createMock(\Src\Database\DBConnector::class);
        $dbMock->method('isConnected')->willReturn(true);
        $dbMock->method('insert')->willReturn(true);

        // Create the repository
        $repository = new MailQueueRepository($dbMock);

        $mailers = [];
        if(isset($_ENV['ESP1']) && $_ENV['ESP1'] === 'SendGrid') {
            $mailers[] = MailerFactory::createMailer('sendgrid', [
                'api_key' => $_ENV['ESP1_API_KEY'],
                'domain' => $_ENV['ESP1_DOMAIN']
            ]);
        }
        if(isset($_ENV['ESP2']) && $_ENV['ESP2'] === 'Mailgun') {
            $mailers[] = MailerFactory::createMailer('mailgun', [
                'api_key' => $_ENV['ESP2_API_KEY'],
                'domain' => $_ENV['ESP2_DOMAIN']
            ]);
        }

        // Inject the repository into MailQueue
        $this->mail_queue = new MailQueue($repository);
    }

    /**
     * Tests that a valid email is successfully added to the queue.
     * @return void
     */
    public function testValidEmailFormat()
    {
        $email = new Email(
            'Test Subject',
            'This is a test email body.',
            'jayy.shah16@gmail.com',
            'jayy.shah16@gmail.com',
            []
        );
        $this->assertTrue(
            $this->mail_queue->addEmailToQueue($email)
        );
    }

    /**
     * Tests that an invalid 'from' email address throws an exception.
     * @return void
     */
    public function testInvalidFromEmailFormat()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid 'from' email address.");
        $email = new Email(
            'Test Subject',
            'This is a test email body.',
            'jayy.shah16@gmail.com',
            'invalid-email-format',
            []
        );
        $this->mail_queue->addEmailToQueue($email);
    }

    /**
     * Tests that an invalid 'to' email address throws an exception.
     * @return void
     */
    public function testInvalidToEmailFormat()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid 'to' email address.");
        $email = new Email(
            'Test Subject',
            'This is a test email body.',
            'invalid-email-format', // Invalid email format
            'jayy.shah16@gmail.com',
            []
        );
        // Attempt to send an email with an invalid email format
        // This will throw an exception due to the invalid email format
        $this->mail_queue->addEmailToQueue($email);
    }

    /**
     * Tests that an empty subject throws an exception.
     * @return void
     */
    public function testInvalidSubject()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Subject cannot be empty and must not exceed 255 characters.");
        // Attempt to send an email with an invalid email format
        // This will throw an exception due to the invalid email format
        $email = new Email(
            '',
            'This is a test email body.',
            'jayy.shah16@gmail.com',
            'jayy.shah16@gmail.com',
            []
        );
        $this->mail_queue->addEmailToQueue($email);
    }

    /**
     * Tests that an empty body throws an exception.
     * @return void
     */
    public function testEmptyBody()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Body cannot be empty.");
        // Attempt to send an email with an invalid email format
        // This will throw an exception due to the invalid email format
        $email = new Email(
            'This is a test subject.',
            '',
            'jayy.shah16@gmail.com',
            'jayy.shah16@gmail.com',
            []
        );
        $this->mail_queue->addEmailToQueue($email);
    }

    /**
     * Tests that a valid email with parameters is successfully added to the queue.
     * @return void
     */
    public function testValidBodyWithParameters()
    {
        // Attempt to send an email with an invalid email format
        // This will throw an exception due to the invalid email format
        $email = new Email(
            'Test Subject with Parameters',
            'Hi, {{first_name}} {{last_name}}, How are you doing today?',
            'jayy.shah16@gmail.com',
            'jayy.shah16@gmail.com',
            [
                'first_name' => 'Jayy',
                'last_name' => 'Shah'
            ]
        );
        $this->assertTrue(
            $this->mail_queue->addEmailToQueue($email)
        );
    }

    /**
     * Tests that a valid email with no parameters is successfully added to the queue.
     * @return void
     */
    public function testValidBodyWithNoParameters()
    {
        // Attempt to send an email with an invalid email format
        // This will throw an exception due to the invalid email format
        $email = new Email(
            'Test Subject with Parameters',
            'Hi, {{first_name}} {{last_name}}, How are you doing today?',
            'jayy.shah16@gmail.com',
            'jayy.shah16@gmail.com',
            [
            ]
        );
        $this->assertTrue(
            $this->mail_queue->addEmailToQueue($email)
        );
    }

    /**
     * Tests that an invalid status throws an exception.
     * @return void
     */
    public function testInvalidStatus()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid status. Allowed values are 'pending', 'sent', 'failed'.");
        $email = new Email(
            'Test Subject',
            'This is a test email body.',
            'jayy.shah16@gmail.com',
            'jayy.shah16@gmail.com',
            [],
            'not-a-valid-status'
        );
        $this->mail_queue->addEmailToQueue($email);
    }

    public function testConfigReturnsDatabaseConfig()
    {
        $config = \Src\Config\Config::getInstance();
        $dbConfig = $config->getDatabaseConfig();
        $this->assertIsArray($dbConfig);
        $this->assertArrayHasKey('host', $dbConfig);
        $this->assertArrayHasKey('name', $dbConfig);
        $this->assertArrayHasKey('user', $dbConfig);
        $this->assertArrayHasKey('password', $dbConfig);
    }

    public function testEmailControllerSendsSuccessResponse()
    {
        $mailQueueMock = $this->createMock(\Src\Queues\MailQueue::class);
        $mailQueueMock->method('addEmailToQueue')->willReturn(true);

        $controller = new \Src\Controllers\EmailController($mailQueueMock);

        // Capture output
        ob_start();
        $controller->sendEmail([
            'subject' => 'Test',
            'body' => 'Body',
            'to' => 'to@example.com',
            'from' => 'from@example.com',
            'parameters' => [],
            'status' => 'pending'
        ]);
        $output = ob_get_clean();

        $this->assertStringContainsString('Email queued successfully', $output);
    }

    public function testEmailControllerSendsFailureResponse()
    {
        $mailQueueMock = $this->createMock(\Src\Queues\MailQueue::class);
        $mailQueueMock->method('addEmailToQueue')->willReturn(false);

        $controller = new \Src\Controllers\EmailController($mailQueueMock);

        ob_start();
        $controller->sendEmail([
            'subject' => 'Test',
            'body' => 'Body',
            'to' => 'to@example.com',
            'from' => 'from@example.com',
            'parameters' => [],
            'status' => 'pending'
        ]);
        $output = ob_get_clean();

        $this->assertStringContainsString('Failed to queue email', $output);
    }

    /**
     * Clean up after each test.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        // Add cleanup code here if needed
    }
}