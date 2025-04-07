<?php

namespace FrameJam\Core\Queue;

use FrameJam\Core\Cache\CacheInterface;
use FrameJam\Core\Cache\FileCache;

class QueueManager
{
    private CacheInterface $cache;
    private string $queuePrefix = 'queue_';
    private string $defaultQueue = 'default';

    public function __construct(CacheInterface $cache = null)
    {
        $this->cache = $cache ?? new FileCache();
    }

    public function push(Job $job, string $queue = null): string
    {
        $queue = $queue ?? $this->defaultQueue;
        $id = uniqid('job_', true);
        
        $jobData = [
            'id' => $id,
            'job' => get_class($job),
            'data' => $job->getData(),
            'attempts' => 0,
            'created_at' => time(),
            'queue' => $queue
        ];
        
        $this->cache->set($this->queuePrefix . $id, $jobData);
        
        // Adicionar à lista de jobs da fila
        $queueList = $this->cache->get($this->queuePrefix . 'list_' . $queue, []);
        $queueList[] = $id;
        $this->cache->set($this->queuePrefix . 'list_' . $queue, $queueList);
        
        return $id;
    }

    public function pop(string $queue = null): ?Job
    {
        $queue = $queue ?? $this->defaultQueue;
        
        // Obter a lista de jobs da fila
        $queueList = $this->cache->get($this->queuePrefix . 'list_' . $queue, []);
        
        if (empty($queueList)) {
            return null;
        }
        
        // Remover o primeiro job da lista
        $jobId = array_shift($queueList);
        $this->cache->set($this->queuePrefix . 'list_' . $queue, $queueList);
        
        // Obter os dados do job
        $jobData = $this->cache->get($this->queuePrefix . $jobId);
        
        if (!$jobData) {
            return null;
        }
        
        // Criar uma instância do job
        $jobClass = $jobData['job'];
        $job = new $jobClass($jobData['data']);
        
        // Atualizar o número de tentativas
        $jobData['attempts']++;
        $this->cache->set($this->queuePrefix . $jobId, $jobData);
        
        return $job;
    }

    public function delete(string $jobId): bool
    {
        return $this->cache->delete($this->queuePrefix . $jobId);
    }

    public function release(string $jobId, string $queue = null): bool
    {
        $queue = $queue ?? $this->defaultQueue;
        
        // Obter os dados do job
        $jobData = $this->cache->get($this->queuePrefix . $jobId);
        
        if (!$jobData) {
            return false;
        }
        
        // Adicionar à lista de jobs da fila
        $queueList = $this->cache->get($this->queuePrefix . 'list_' . $queue, []);
        $queueList[] = $jobId;
        $this->cache->set($this->queuePrefix . 'list_' . $queue, $queueList);
        
        return true;
    }

    public function size(string $queue = null): int
    {
        $queue = $queue ?? $this->defaultQueue;
        $queueList = $this->cache->get($this->queuePrefix . 'list_' . $queue, []);
        
        return count($queueList);
    }
} 