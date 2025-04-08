<?php

namespace FrameJam\Core\Queue;

use FrameJam\Core\Log\Logger;

class Worker
{
    private QueueManager $queue;
    private Logger $logger;
    private bool $running = false;
    private int $sleep = 3;
    private int $maxJobs = 1000;
    private int $maxTime = 3600;
    private array $queues = ['default'];

    public function __construct(QueueManager $queue, Logger $logger)
    {
        $this->queue = $queue;
        $this->logger = $logger;
    }

    public function work(array $queues): void
    {
        $this->queues = $queues ?? $this->queues;
        $this->running = true;
        $startTime = time();
        $jobsProcessed = 0;

        $this->logger->info('Worker started', ['queues' => $this->queues]);

        while ($this->running) {
            if ($this->shouldQuit($startTime, $jobsProcessed)) {
                break;
            }

            foreach ($this->queues as $queue) {
                $job = $this->queue->pop($queue);
                
                if ($job) {
                    $this->process($job);
                    $jobsProcessed++;
                }
            }

            sleep($this->sleep);
        }

        $this->logger->info('Worker stopped', [
            'jobs_processed' => $jobsProcessed,
            'runtime' => time() - $startTime
        ]);
    }

    private function process(Job $job): void
    {
        try {
            $this->logger->info('Processing job', ['job' => get_class($job)]);
            
            $job->handle();
            
            $this->logger->info('Job completed successfully', ['job' => get_class($job)]);
        } catch (\Exception $e) {
            $this->logger->error('Job failed', [
                'job' => get_class($job),
                'error' => $e->getMessage()
            ]);

            $job->failed($e);
        }
    }

    private function shouldQuit(int $startTime, int $jobsProcessed): bool
    {
        if ($jobsProcessed >= $this->maxJobs) {
            $this->logger->info('Max jobs reached');
            return true;
        }

        if (time() - $startTime >= $this->maxTime) {
            $this->logger->info('Max time reached');
            return true;
        }

        return false;
    }

    public function stop(): void
    {
        $this->running = false;
    }

    public function setSleep(int $seconds): void
    {
        $this->sleep = $seconds;
    }

    public function setMaxJobs(int $jobs): void
    {
        $this->maxJobs = $jobs;
    }

    public function setMaxTime(int $seconds): void
    {
        $this->maxTime = $seconds;
    }
} 