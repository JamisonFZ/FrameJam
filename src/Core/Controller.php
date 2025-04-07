<?php

namespace FrameJam\Core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

abstract class Controller
{
    protected Environment $view;
    protected array $data = [];

    public function __construct()
    {
        $loader = new FilesystemLoader(__DIR__ . '/../../resources/views');
        $this->view = new Environment($loader, [
            'cache' => __DIR__ . '/../../storage/cache/views',
            'auto_reload' => true
        ]);
    }

    protected function view(string $template, array $data = []): string
    {
        return $this->view->render($template, array_merge($this->data, $data));
    }

    protected function json($data, int $status = 200): array
    {
        http_response_code($status);
        return $data;
    }

    protected function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }

    protected function input(string $key = null, $default = null)
    {
        if ($key === null) {
            return $_REQUEST;
        }

        return $_REQUEST[$key] ?? $default;
    }

    protected function file(string $key = null)
    {
        if ($key === null) {
            return $_FILES;
        }

        return $_FILES[$key] ?? null;
    }
} 