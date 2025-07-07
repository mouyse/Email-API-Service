<?php
require_once __DIR__ . '/../Queues/MailQueue.php';
use Src\Factories\MailerFactory;
use Src\Config\Config;

// Load environment variables from .env file
$dotenv = \Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->safeLoad();

$database = DBConnector::getInstance();

$config = Config::getInstance();
$mailers = [];
if($config->get('ESP1') === 'SendGrid') {
    $mailers[] = MailerFactory::createMailer('sendgrid', [
        'api_key' => $config->get('ESP1_API_KEY'),
        'domain' => $config->get('ESP1_DOMAIN')
    ]);
}
if($config->get('ESP2') === 'Mailgun') {
    $mailers[] = MailerFactory::createMailer('mailgun', [
        'api_key' => $config->get('ESP2_API_KEY'),
        'domain' => $config->get('ESP2_DOMAIN')
    ]);
}

$mailQueue = new MailQueue($mailers);
$jobs = $queue->fetchPendingJobs($config->get('BATCH_SIZE', 100));
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

