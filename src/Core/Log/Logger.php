<?php

namespace FrameJam\Core\Log;

class Logger
{
    private string $path;
    private array $levels = [
        'emergency' => 0,
        'alert'     => 1,
        'critical'  => 2,
        'error'     => 3,
        'warning'   => 4,
        'notice'    => 5,
        'info'      => 6,
        'debug'     => 7
    ];

    public function __construct(string $path)
    {
        $this->path = $path ?? __DIR__ . '/../../storage/logs';
        if (!is_dir($this->path)) {
            mkdir($this->path, 0777, true);
        }
    }

    public function emergency(string $message, array $context = []): void
    {
        $this->log('emergency', $message, $context);
    }

    public function alert(string $message, array $context = []): void
    {
        $this->log('alert', $message, $context);
    }

    public function critical(string $message, array $context = []): void
    {
        $this->log('critical', $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->log('error', $message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->log('warning', $message, $context);
    }

    public function notice(string $message, array $context = []): void
    {
        $this->log('notice', $message, $context);
    }

    public function info(string $message, array $context = []): void
    {
        $this->log('info', $message, $context);
    }

    public function debug(string $message, array $context = []): void
    {
        $this->log('debug', $message, $context);
    }

    private function log(string $level, string $message, array $context = []): void
    {
        $date = date('Y-m-d');
        $time = date('H:i:s');
        $logFile = $this->path . "/{$date}.log";

        $contextStr = empty($context) ? '' : json_encode($context);
        $logMessage = "[{$time}] {$level}: {$message} {$contextStr}\n";

        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }
} 