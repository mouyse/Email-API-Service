<?php
use PHPUnit\Framework\TestCase;
use Src\Factories\MailerFactory;
use Src\Email\SendGridMailer;
use Src\Email\MailgunMailer;

class MailerFactoryTest extends TestCase
{
    public function testCreateSendGridMailer()
    {
        $mailer = MailerFactory::createMailer('sendgrid', ['api_key' => 'key', 'domain' => 'domain']);
        $this->assertInstanceOf(SendGridMailer::class, $mailer);
    }

    public function testCreateMailgunMailer()
    {
        $mailer = MailerFactory::createMailer('mailgun', ['api_key' => 'key', 'domain' => 'domain']);
        $this->assertInstanceOf(MailgunMailer::class, $mailer);
    }

    public function testCreateMailerThrowsOnUnknownType()
    {
        $this->expectException(\InvalidArgumentException::class);
        MailerFactory::createMailer('unknown', []);
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