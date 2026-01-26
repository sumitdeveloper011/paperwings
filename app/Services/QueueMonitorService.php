<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

class QueueMonitorService
{
    protected ?string $devEmail;

    public function __construct()
    {
        $this->devEmail = config('queue-monitor.dev_email');
    }

    public function shouldMonitor(): bool
    {
        return app()->environment('local', 'development') && !empty($this->devEmail);
    }

    public function sendQueueStartNotification(string $queue, int $jobCount = 0): void
    {
        if (!$this->shouldMonitor()) {
            return;
        }

        try {
            $subject = "[Queue Monitor] Queue Worker Started - {$queue}";
            $message = "Queue worker started processing queue: {$queue}\n";
            $message .= "Time: " . now()->toDateTimeString() . "\n";
            $message .= "Pending Jobs: {$jobCount}\n";
            $message .= "Environment: " . app()->environment() . "\n";

            Mail::raw($message, function ($mail) use ($subject) {
                $mail->to($this->devEmail)
                    ->subject($subject);
            });

            Log::info('Queue start notification sent', [
                'queue' => $queue,
                'email' => $this->devEmail
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to send queue start notification', [
                'queue' => $queue,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function sendQueueFinishNotification(string $queue, int $processedJobs = 0, int $failedJobs = 0, float $duration = 0): void
    {
        if (!$this->shouldMonitor()) {
            return;
        }

        try {
            $subject = "[Queue Monitor] Queue Worker Finished - {$queue}";
            $message = "Queue worker finished processing queue: {$queue}\n";
            $message .= "Time: " . now()->toDateTimeString() . "\n";
            $message .= "Processed Jobs: {$processedJobs}\n";
            $message .= "Failed Jobs: {$failedJobs}\n";
            $message .= "Duration: " . number_format($duration, 2) . " seconds\n";
            $message .= "Environment: " . app()->environment() . "\n";

            Mail::raw($message, function ($mail) use ($subject) {
                $mail->to($this->devEmail)
                    ->subject($subject);
            });

            Log::info('Queue finish notification sent', [
                'queue' => $queue,
                'processed' => $processedJobs,
                'failed' => $failedJobs,
                'email' => $this->devEmail
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to send queue finish notification', [
                'queue' => $queue,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function sendJobFailedNotification(string $queue, string $jobName, string $error, array $payload = []): void
    {
        if (!$this->shouldMonitor()) {
            return;
        }

        try {
            $subject = "[Queue Monitor] Job Failed - {$queue}";
            $message = "A job failed in queue: {$queue}\n";
            $message .= "Job: {$jobName}\n";
            $message .= "Time: " . now()->toDateTimeString() . "\n";
            $message .= "Error: {$error}\n";
            if (!empty($payload)) {
                $message .= "Payload: " . json_encode($payload, JSON_PRETTY_PRINT) . "\n";
            }
            $message .= "Environment: " . app()->environment() . "\n";

            Mail::raw($message, function ($mail) use ($subject) {
                $mail->to($this->devEmail)
                    ->subject($subject);
            });

            Log::info('Job failed notification sent', [
                'queue' => $queue,
                'job' => $jobName,
                'email' => $this->devEmail
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to send job failed notification', [
                'queue' => $queue,
                'error' => $e->getMessage()
            ]);
        }
    }
}
