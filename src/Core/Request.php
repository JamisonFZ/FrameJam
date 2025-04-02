<?php

namespace FrameJam\Core;

class Request
{
    private array $params = [];
    private array $query = [];
    private array $post = [];
    private array $server = [];
    private array $files = [];
    private array $cookies = [];
    private array $headers = [];

    public static function createFromGlobals(): self
    {
        $request = new self();
        $request->query = $_GET;
        $request->post = $_POST;
        $request->server = $_SERVER;
        $request->files = $_FILES;
        $request->cookies = $_COOKIE;
        $request->headers = getallheaders();
        return $request;
    }

    public function getPath(): string
    {
        return parse_url($this->server['REQUEST_URI'], PHP_URL_PATH);
    }

    public function getMethod(): string
    {
        return $this->server['REQUEST_METHOD'];
    }

    public function getQuery(string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->query;
        }
        return $this->query[$key] ?? $default;
    }

    public function getPost(string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->post;
        }
        return $this->post[$key] ?? $default;
    }

    public function getFile(string $key = null)
    {
        if ($key === null) {
            return $this->files;
        }
        return $this->files[$key] ?? null;
    }

    public function getHeader(string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->headers;
        }
        return $this->headers[$key] ?? $default;
    }

    public function getRouteParams(): array
    {
        return $this->params;
    }

    public function setRouteParams(array $params): void
    {
        $this->params = $params;
    }

    public function getParam(string $key, $default = null)
    {
        return $this->params[$key] ?? $default;
    }
} 