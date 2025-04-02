<?php

namespace FrameJam\Core\Cache;

use Predis\Client;
use FrameJam\Core\Application;

class Cache
{
    private Client $redis;
    private string $prefix = 'framejam:';

    public function __construct()
    {
        $config = Application::getInstance()->getConfig()->get('cache');
        $this->redis = new Client([
            'scheme' => 'tcp',
            'host' => $config['host'],
            'port' => $config['port'],
            'password' => $config['password'] ?? null,
        ]);
    }

    public function get(string $key, $default = null)
    {
        $value = $this->redis->get($this->prefix . $key);
        return $value !== null ? unserialize($value) : $default;
    }

    public function set(string $key, $value, int $ttl = 3600): bool
    {
        return $this->redis->setex(
            $this->prefix . $key,
            $ttl,
            serialize($value)
        );
    }

    public function has(string $key): bool
    {
        return $this->redis->exists($this->prefix . $key);
    }

    public function delete(string $key): bool
    {
        return $this->redis->del($this->prefix . $key) > 0;
    }

    public function flush(): bool
    {
        return $this->redis->flushdb();
    }

    public function remember(string $key, int $ttl, callable $callback)
    {
        if ($this->has($key)) {
            return $this->get($key);
        }

        $value = $callback();
        $this->set($key, $value, $ttl);

        return $value;
    }

    public function increment(string $key, int $value = 1): int
    {
        return $this->redis->incrby($this->prefix . $key, $value);
    }

    public function decrement(string $key, int $value = 1): int
    {
        return $this->redis->decrby($this->prefix . $key, $value);
    }

    public function setPrefix(string $prefix): self
    {
        $this->prefix = $prefix;
        return $this;
    }
} 