<?php

namespace FrameJam\Core\Queue;

abstract class Job
{
    protected array $data = [];
    protected int $attempts = 0;
    protected int $maxAttempts = 3;
    protected int $timeout = 60;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    abstract public function handle(): void;

    public function failed(\Throwable $exception): void
    {
        // Implementar lógica de falha se necessário
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getAttempts(): int
    {
        return $this->attempts;
    }

    public function incrementAttempts(): void
    {
        $this->attempts++;
    }

    public function getMaxAttempts(): int
    {
        return $this->maxAttempts;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public function setMaxAttempts(int $maxAttempts): void
    {
        $this->maxAttempts = $maxAttempts;
    }

    public function setTimeout(int $timeout): void
    {
        $this->timeout = $timeout;
    }
} 