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
        // Implementação padrão - pode ser sobrescrita
        error_log("Job failed: " . $exception->getMessage());
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

    public function hasExceededMaxAttempts(): bool
    {
        return $this->attempts >= $this->maxAttempts;
    }

    public function getMaxAttempts(): int
    {
        return $this->maxAttempts;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }
} 