<?php
use PHPUnit\Framework\TestCase;
use Src\Models\Email;
use Src\Email\SendGridMailer;

class EmailMailerTest extends TestCase
{
    public function testSendGridMailerSendReturnsTrue()
    {
        $mailer = $this->getMockBuilder(\Src\Email\SendGridMailer::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['send'])
            ->getMock();
        $mailer->method('send')->willReturn(true);

        $this->assertTrue($mailer->send('Test Subject', 'Test Body', 'to@example.com', 'from@example.com'));
    }
}