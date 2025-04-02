<?php

namespace FrameJam\Core;

class Config
{
    private array $config = [];

    public function __construct()
    {
        $this->loadConfig();
    }

    private function loadConfig(): void
    {
        $configPath = dirname(__DIR__, 2) . '/config';
        
        if (is_dir($configPath)) {
            $files = glob($configPath . '/*.php');
            
            foreach ($files as $file) {
                $key = basename($file, '.php');
                $this->config[$key] = require $file;
            }
        }
    }

    public function get(string $key, $default = null)
    {
        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }

    public function set(string $key, $value): void
    {
        $keys = explode('.', $key);
        $config = &$this->config;

        foreach ($keys as $k) {
            if (!isset($config[$k]) || !is_array($config[$k])) {
                $config[$k] = [];
            }
            $config = &$config[$k];
        }

        $config = $value;
    }

    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }
} 