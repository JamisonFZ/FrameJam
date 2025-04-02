<?php

namespace FrameJam\Core\Queue;

use Predis\Client;
use FrameJam\Core\Application;

class Queue
{
    private Client $redis;
    private string $prefix;

    public function __construct()
    {
        $config = Application::getInstance()->getConfig()->get('queue');
        
        $this->redis = new Client([
            'host' => $config['host'],
            'port' => $config['port'],
            'password' => $config['password'],
            'database' => $config['database'],
        ]);
        
        $this->prefix = $config['prefix'];
    }

    public function push(string $queue, $job, array $data = [], int $delay = 0): void
    {
        $payload = [
            'job' => $job,
            'data' => $data,
            'attempts' => 0,
            'created_at' => time(),
        ];

        if ($delay > 0) {
            $this->redis->zadd(
                $this->prefix . 'delayed:' . $queue,
                time() + $delay,
                json_encode($payload)
            );
        } else {
            $this->redis->lpush(
                $this->prefix . 'queues:' . $queue,
                json_encode($payload)
            );
        }
    }

    public function pop(string $queue)
    {
        $payload = $this->redis->rpop($this->prefix . 'queues:' . $queue);
        
        if (!$payload) {
            return null;
        }

        return json_decode($payload, true);
    }

    public function size(string $queue): int
    {
        return $this->redis->llen($this->prefix . 'queues:' . $queue);
    }

    public function later(string $queue, $job, array $data = [], int $delay = 0): void
    {
        $this->push($queue, $job, $data, $delay);
    }

    public function release(string $queue, array $payload, int $delay = 0): void
    {
        $payload['attempts']++;
        
        if ($delay > 0) {
            $this->redis->zadd(
                $this->prefix . 'delayed:' . $queue,
                time() + $delay,
                json_encode($payload)
            );
        } else {
            $this->redis->lpush(
                $this->prefix . 'queues:' . $queue,
                json_encode($payload)
            );
        }
    }

    public function delete(string $queue, array $payload): void
    {
        $this->redis->lrem(
            $this->prefix . 'queues:' . $queue,
            1,
            json_encode($payload)
        );
    }
} 