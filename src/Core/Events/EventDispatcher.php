<?php

namespace FrameJam\Core\Events;

class EventDispatcher
{
    private static array $listeners = [];

    public static function listen(string $event, callable $listener): void
    {
        if (!isset(self::$listeners[$event])) {
            self::$listeners[$event] = [];
        }
        self::$listeners[$event][] = $listener;
    }

    public static function dispatch(string $event, array $data = []): void
    {
        if (!isset(self::$listeners[$event])) {
            return;
        }

        foreach (self::$listeners[$event] as $listener) {
            $listener($data);
        }
    }

    public static function hasListeners(string $event): bool
    {
        return isset(self::$listeners[$event]) && !empty(self::$listeners[$event]);
    }

    public static function getListeners(string $event): array
    {
        return self::$listeners[$event] ?? [];
    }

    public static function removeListener(string $event, callable $listener): void
    {
        if (!isset(self::$listeners[$event])) {
            return;
        }

        foreach (self::$listeners[$event] as $key => $value) {
            if ($value === $listener) {
                unset(self::$listeners[$event][$key]);
            }
        }
    }
} 