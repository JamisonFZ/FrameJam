<?php

namespace FrameJam\Core;

class Session
{
    private static bool $started = false;

    public static function start(): void
    {
        if (!self::$started) {
            session_start();
            self::$started = true;
        }
    }

    public static function set(string $key, $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, $default = null)
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function clear(): void
    {
        self::start();
        session_destroy();
        self::$started = false;
    }

    public static function flash(string $key, $value): void
    {
        self::set('_flash_' . $key, $value);
    }

    public static function getFlash(string $key, $default = null)
    {
        $value = self::get('_flash_' . $key, $default);
        self::remove('_flash_' . $key);
        return $value;
    }

    public static function regenerate(): void
    {
        self::start();
        session_regenerate_id(true);
    }
} 