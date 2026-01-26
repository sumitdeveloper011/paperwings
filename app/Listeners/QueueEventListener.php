<?php

namespace App\Listeners;

use App\Services\QueueMonitorService;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Log;

class QueueEventListener
{
    protected QueueMonitorService $monitor;
    protected array $queueStats = [];

    public function __construct(QueueMonitorService $monitor)
    {
        $this->monitor = $monitor;
    }

    public function handleJobProcessing(JobProcessing $event): void
    {
        if (!$this->monitor->shouldMonitor()) {
            return;
        }

        $queue = $event->job->getQueue();
        
        if (!isset($this->queueStats[$queue])) {
            $this->queueStats[$queue] = [
                'start_time' => microtime(true),
                'processed' => 0,
                'failed' => 0,
            ];
            
            $pendingJobs = \Illuminate\Support\Facades\Queue::size($queue);
            $this->monitor->sendQueueStartNotification($queue, $pendingJobs);
        }
    }

    public function handleJobProcessed(JobProcessed $event): void
    {
        if (!$this->monitor->shouldMonitor()) {
            return;
        }

        $queue = $event->job->getQueue();
        
        if (isset($this->queueStats[$queue])) {
            $this->queueStats[$queue]['processed']++;
        }
    }

    public function handleJobFailed(JobFailed $event): void
    {
        if (!$this->monitor->shouldMonitor()) {
            return;
        }

        $queue = $event->job->getQueue();
        $jobName = get_class($event->job);
        $error = $event->exception->getMessage();
        
        if (isset($this->queueStats[$queue])) {
            $this->queueStats[$queue]['failed']++;
        }

        $this->monitor->sendJobFailedNotification(
            $queue,
            $jobName,
            $error,
            $event->job->payload() ?? []
        );
    }

    public function subscribe($events): void
    {
        if (!$this->monitor->shouldMonitor()) {
            return;
        }

        $events->listen(
            JobProcessing::class,
            [QueueEventListener::class, 'handleJobProcessing']
        );

        $events->listen(
            JobProcessed::class,
            [QueueEventListener::class, 'handleJobProcessed']
        );

        $events->listen(
            JobFailed::class,
            [QueueEventListener::class, 'handleJobFailed']
        );
    }
}
