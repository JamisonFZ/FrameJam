<?php

namespace FrameJam\Core\Queue;

use FrameJam\Core\Application;
use FrameJam\Core\Log\Logger;

class Worker
{
    private Queue $queue;
    private Logger $logger;
    private bool $running = false;
    private array $options = [];

    public function __construct(array $options = [])
    {
        $this->queue = new Queue();
        $this->logger = new Logger();
        $this->options = array_merge([
            'queue' => 'default',
            'memory' => 128,
            'timeout' => 60,
            'sleep' => 3,
            'tries' => 3,
            'max_jobs' => null,
        ], $options);
    }

    public function start(): void
    {
        $this->running = true;
        $jobsProcessed = 0;

        while ($this->running) {
            if ($this->shouldStop()) {
                break;
            }

            if ($this->options['max_jobs'] && $jobsProcessed >= $this->options['max_jobs']) {
                break;
            }

            $payload = $this->queue->pop($this->options['queue']);

            if (!$payload) {
                if ($this->options['sleep'] > 0) {
                    sleep($this->options['sleep']);
                }
                continue;
            }

            try {
                $this->processJob($payload);
                $jobsProcessed++;
            } catch (\Throwable $e) {
                $this->logger->error('Job failed: ' . $e->getMessage());
                $this->handleFailedJob($payload, $e);
            }

            if ($this->memoryExceeded($this->options['memory'])) {
                $this->stop();
            }
        }
    }

    public function stop(): void
    {
        $this->running = false;
    }

    private function processJob(array $payload): void
    {
        $job = $this->resolveJob($payload['job']);
        $job->incrementAttempts();

        try {
            $job->handle();
            $this->queue->delete($this->options['queue'], $payload);
        } catch (\Throwable $e) {
            if ($job->getAttempts() >= $this->options['tries']) {
                $this->queue->delete($this->options['queue'], $payload);
                $job->failed($e);
            } else {
                $this->queue->release($this->options['queue'], $payload);
            }
            throw $e;
        }
    }

    private function resolveJob(string $jobClass): Job
    {
        return new $jobClass($this->data ?? []);
    }

    private function handleFailedJob(array $payload, \Throwable $e): void
    {
        $this->queue->delete($this->options['queue'], $payload);
        
        $this->logger->error('Job failed: ' . $e->getMessage(), [
            'job' => $payload['job'],
            'data' => $payload['data'],
            'exception' => $e,
        ]);
    }

    private function shouldStop(): bool
    {
        return !$this->running;
    }

    private function memoryExceeded(int $memoryLimit): bool
    {
        return (memory_get_usage(true) / 1024 / 1024) >= $memoryLimit;
    }
} 