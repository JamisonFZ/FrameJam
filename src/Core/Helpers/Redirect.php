<?php

namespace FrameJam\Core\Helpers;

class Redirect
{
    /**
     * Redireciona para uma URL específica
     *
     * @param string $url
     * @param int $status
     * @return void
     */
    public static function to(string $url, int $status = 302): void
    {
        header('Location: ' . $url, true, $status);
        exit;
    }

    /**
     * Redireciona para a URL anterior
     *
     * @param int $status
     * @return void
     */
    public static function back(int $status = 302): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        self::to($referer, $status);
    }

    /**
     * Redireciona para a URL armazenada na sessão
     *
     * @param string $default
     * @param int $status
     * @return void
     */
    public static function intended(string $default = '/', int $status = 302): void
    {
        $url = \FrameJam\Core\Session::get('intended_url', $default);
        \FrameJam\Core\Session::remove('intended_url');
        self::to($url, $status);
    }
} 