<?php

namespace FrameJam\Core\Events;

class EventDispatcher
{
    private array $listeners = [];

    public function dispatch(string $event, array $payload = []): void
    {
        if (!isset($this->listeners[$event])) {
            return;
        }

        foreach ($this->listeners[$event] as $listener) {
            if (is_callable($listener)) {
                $listener($payload);
            } else {
                $instance = new $listener();
                $instance->handle($payload);
            }
        }
    }

    public function listen(string $event, $listener): void
    {
        if (!isset($this->listeners[$event])) {
            $this->listeners[$event] = [];
        }

        $this->listeners[$event][] = $listener;
    }

    public function hasListeners(string $event): bool
    {
        return isset($this->listeners[$event]) && !empty($this->listeners[$event]);
    }

    public function getListeners(string $event): array
    {
        return $this->listeners[$event] ?? [];
    }

    public function removeListener(string $event, $listener): void
    {
        if (!isset($this->listeners[$event])) {
            return;
        }

        $this->listeners[$event] = array_filter(
            $this->listeners[$event],
            fn($l) => $l !== $listener
        );
    }

    public function removeAllListeners(string $event): void
    {
        unset($this->listeners[$event]);
    }
} 