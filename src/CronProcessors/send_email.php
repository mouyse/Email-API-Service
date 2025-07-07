<?php
require_once __DIR__ . '/../Queues/MailQueue.php';
use Src\Factories\MailerFactory;

// Load environment variables from .env file
$dotenv = \Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->safeLoad();

$database = DBConnector::getInstance();

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

$mailQueue = new MailQueue($mailers);
$jobs = $queue->fetchPendingJobs($_ENV['BATCH_SIZE'] ?? 100);
$mail = new Mailer($database, $mailers);
foreach ($jobs as $job) {
    try {
        $response = $mail->send($job['subject'], $job['body'], $job['to'], $job['from'], $job['status']);
        if ($response) {
            $mailQueue->updateEmailStatus($job['id'], 'sent');
        } else {
            $mailQueue->updateEmailStatus($job['id'], 'failed');
        }
    } catch (Exception $e) {
        error_log("Failed to send email for job ID {$job['id']}: " . $e->getMessage());
        $mailQueue->updateEmailStatus($job['id'], 'failed');
    }
}
// If no jobs were fetched, log a message
if (empty($jobs)) {
    error_log("No pending email jobs found.");
} else {
    error_log("Processed " . count($jobs) . " email jobs.");
}

