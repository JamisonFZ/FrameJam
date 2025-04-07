<?php

namespace FrameJam\Core\Console\Commands;

use FrameJam\Core\Queue\QueueManager;
use FrameJam\Core\Queue\Worker;
use FrameJam\Core\Log\Logger;

class QueueWorkCommand extends Command
{
    private QueueManager $queue;
    private Logger $logger;

    public function __construct(QueueManager $queue, Logger $logger)
    {
        $this->queue = $queue;
        $this->logger = $logger;
    }

    public function execute(array $args = []): void
    {
        $queues = $args['queues'] ?? ['default'];
        $sleep = (int)($args['sleep'] ?? 3);
        $maxJobs = (int)($args['max-jobs'] ?? 1000);
        $maxTime = (int)($args['max-time'] ?? 3600);

        $worker = new Worker($this->queue, $this->logger);
        $worker->setSleep($sleep);
        $worker->setMaxJobs($maxJobs);
        $worker->setMaxTime($maxTime);

        $this->logger->info('Starting queue worker', [
            'queues' => $queues,
            'sleep' => $sleep,
            'max_jobs' => $maxJobs,
            'max_time' => $maxTime
        ]);

        $worker->work($queues);
    }

    public function getDescription(): string
    {
        return 'Start the queue worker';
    }

    public function getUsage(): string
    {
        return 'queue:work [queues] [--sleep=3] [--max-jobs=1000] [--max-time=3600]';
    }
} 