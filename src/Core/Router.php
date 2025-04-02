<?php

namespace FrameJam\Core;

class Router
{
    private array $routes = [];
    private array $middlewares = [];

    public function get(string $path, $handler): self
    {
        return $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, $handler): self
    {
        return $this->addRoute('POST', $path, $handler);
    }

    public function put(string $path, $handler): self
    {
        return $this->addRoute('PUT', $path, $handler);
    }

    public function delete(string $path, $handler): self
    {
        return $this->addRoute('DELETE', $path, $handler);
    }

    private function addRoute(string $method, string $path, $handler): self
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'middlewares' => $this->middlewares
        ];
        return $this;
    }

    public function middleware(string $middleware): self
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    public function dispatch(Request $request): Response
    {
        $path = $request->getPath();
        $method = $request->getMethod();

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchPath($route['path'], $path, $params)) {
                $request->setRouteParams($params);
                
                // Aplicar middlewares
                foreach ($route['middlewares'] as $middleware) {
                    $middlewareInstance = new $middleware();
                    $response = $middlewareInstance->handle($request);
                    if ($response instanceof Response) {
                        return $response;
                    }
                }

                return $this->handle($route['handler'], $request);
            }
        }

        return new Response('Not Found', 404);
    }

    private function matchPath(string $routePath, string $requestPath, &$params): bool
    {
        $routeParts = explode('/', trim($routePath, '/'));
        $requestParts = explode('/', trim($requestPath, '/'));

        if (count($routeParts) !== count($requestParts)) {
            return false;
        }

        $params = [];
        for ($i = 0; $i < count($routeParts); $i++) {
            if (preg_match('/^{([^}]+)}$/', $routeParts[$i], $matches)) {
                $params[$matches[1]] = $requestParts[$i];
            } elseif ($routeParts[$i] !== $requestParts[$i]) {
                return false;
            }
        }

        return true;
    }

    private function handle($handler, Request $request): Response
    {
        if (is_callable($handler)) {
            return $handler($request);
        }

        if (is_string($handler)) {
            [$controller, $method] = explode('@', $handler);
            $controllerInstance = new $controller();
            return $controllerInstance->$method($request);
        }

        throw new \RuntimeException('Invalid route handler');
    }
} 