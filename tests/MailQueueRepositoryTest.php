<?php
use PHPUnit\Framework\TestCase;
use Src\Repositories\MailQueueRepository;

class MailQueueRepositoryTest extends TestCase
{
    public function testAddEmailReturnsTrue()
    {
        $dbMock = $this->createMock(\Src\Database\DBConnector::class);
        $dbMock->method('insert')->willReturn(true);
        $repo = new \Src\Repositories\MailQueueRepository($dbMock);
        $emailData = [
            'subject' => 'Test',
            'body' => 'Body',
            'to' => 'to@example.com',
            'from' => 'from@example.com',
            'parameters' => [],
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $this->assertTrue($repo->add($emailData));
    }
}