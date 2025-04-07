<?php

namespace FrameJam\Core;

class Request
{
    private array $get;
    private array $post;
    private array $server;
    private array $files;
    private array $cookies;
    private array $headers;

    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->server = $_SERVER;
        $this->files = $_FILES;
        $this->cookies = $_COOKIE;
        $this->headers = $this->parseHeaders();
    }

    /**
     * Obtém o método HTTP da requisição
     */
    public function getMethod(): string
    {
        return $this->server['REQUEST_METHOD'] ?? 'GET';
    }

    /**
     * Obtém a URI da requisição
     */
    public function getUri(): string
    {
        return $this->server['REQUEST_URI'] ?? '/';
    }

    /**
     * Obtém um parâmetro GET
     */
    public function get(string $key, $default = null)
    {
        return $this->get[$key] ?? $default;
    }

    /**
     * Obtém um parâmetro POST
     */
    public function post(string $key, $default = null)
    {
        return $this->post[$key] ?? $default;
    }

    /**
     * Obtém um arquivo enviado
     */
    public function file(string $key)
    {
        return $this->files[$key] ?? null;
    }

    /**
     * Obtém um cookie
     */
    public function cookie(string $key, $default = null)
    {
        return $this->cookies[$key] ?? $default;
    }

    /**
     * Obtém um header
     */
    public function header(string $key, $default = null)
    {
        return $this->headers[strtolower($key)] ?? $default;
    }

    /**
     * Verifica se a requisição é AJAX
     */
    public function isAjax(): bool
    {
        return isset($this->headers['x-requested-with']) && 
               strtolower($this->headers['x-requested-with']) === 'xmlhttprequest';
    }

    /**
     * Verifica se a requisição é JSON
     */
    public function isJson(): bool
    {
        return isset($this->headers['content-type']) && 
               strpos($this->headers['content-type'], 'application/json') !== false;
    }

    /**
     * Obtém o corpo da requisição como JSON
     */
    public function json()
    {
        if ($this->isJson()) {
            $content = file_get_contents('php://input');
            return json_decode($content, true);
        }
        return null;
    }

    /**
     * Parse os headers da requisição
     */
    private function parseHeaders(): array
    {
        $headers = [];
        
        foreach ($this->server as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
                $headers[strtolower($header)] = $value;
            }
        }
        
        return $headers;
    }
} 