<?php

namespace FrameJam\Core;

class Router
{
    private array $routes = [];
    private array $middlewares = [];

    public function get(string $path, $callback): void
    {
        $this->addRoute('GET', $path, $callback);
    }

    public function post(string $path, $callback): void
    {
        $this->addRoute('POST', $path, $callback);
    }

    public function put(string $path, $callback): void
    {
        $this->addRoute('PUT', $path, $callback);
    }

    public function delete(string $path, $callback): void
    {
        $this->addRoute('DELETE', $path, $callback);
    }

    private function addRoute(string $method, string $path, $callback): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'callback' => $callback
        ];
    }

    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchPath($route['path'], $path)) {
                return $this->executeCallback($route['callback']);
            }
        }

        throw new \Exception('Rota não encontrada', 404);
    }

    private function matchPath(string $routePath, string $requestPath): bool
    {
        $routeParts = explode('/', trim($routePath, '/'));
        $requestParts = explode('/', trim($requestPath, '/'));

        if (count($routeParts) !== count($requestParts)) {
            return false;
        }

        for ($i = 0; $i < count($routeParts); $i++) {
            if (strpos($routeParts[$i], '{') === 0) {
                continue;
            }
            if ($routeParts[$i] !== $requestParts[$i]) {
                return false;
            }
        }

        return true;
    }

    private function executeCallback($callback)
    {
        if (is_callable($callback)) {
            return $callback();
        }

        if (is_string($callback)) {
            [$controller, $method] = explode('@', $callback);
            $controller = "FrameJam\\Controllers\\{$controller}";
            $instance = new $controller();
            return $instance->$method();
        }

        throw new \Exception('Callback inválido');
    }

    public function middleware(string $name): self
    {
        $this->middlewares[] = $name;
        return $this;
    }
} 