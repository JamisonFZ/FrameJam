<?php

namespace FrameJam\Core;

class Router
{
    private array $routes = [];
    private array $middlewares = [];
    private array $groups = [];

    public function get(string $path, $callback): self
    {
        $this->addRoute('GET', $path, $callback);
        return $this;
    }

    public function post(string $path, $callback): self
    {
        $this->addRoute('POST', $path, $callback);
        return $this;
    }

    public function put(string $path, $callback): self
    {
        $this->addRoute('PUT', $path, $callback);
        return $this;
    }

    public function delete(string $path, $callback): self
    {
        $this->addRoute('DELETE', $path, $callback);
        return $this;
    }

    private function addRoute(string $method, string $path, $callback): void
    {
        $route = [
            'method' => $method,
            'path' => $path,
            'callback' => $callback,
            'middleware' => []
        ];

        // Adiciona middlewares do grupo atual, se existir
        if (!empty($this->groups)) {
            $currentGroup = end($this->groups);
            if (isset($currentGroup['middleware'])) {
                $route['middleware'] = $currentGroup['middleware'];
            }
        }

        $this->routes[] = $route;
    }

    public function group(array $attributes, callable $callback): void
    {
        // Adiciona o grupo atual à pilha de grupos
        $this->groups[] = $attributes;
        
        // Executa o callback para adicionar as rotas do grupo
        $callback($this);
        
        // Remove o grupo atual da pilha
        array_pop($this->groups);
    }

    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchPath($route['path'], $path)) {
                // Executa os middlewares da rota
                if (!empty($route['middleware'])) {
                    foreach ($route['middleware'] as $middleware) {
                        $middlewareInstance = new $middleware();
                        $response = $middlewareInstance->handle(new Request(), function($request) use ($route) {
                            return $this->executeCallback($route['callback']);
                        });
                        
                        if ($response !== null) {
                            return $response;
                        }
                    }
                }
                
                return $this->executeCallback($route['callback']);
            }
        }

        throw new \Exception('Rota não encontrada', 404);
    }

    private function matchPath(string $routePath, string $requestPath): bool
    {
        // Converte a rota em um padrão regex
        $pattern = preg_replace('/\{([a-zA-Z]+)\}/', '(?P<$1>[^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $requestPath, $matches)) {
            // Armazena os parâmetros da rota
            foreach ($matches as $key => $value) {
                if (is_string($key)) {
                    $_GET[$key] = $value;
                }
            }
            return true;
        }

        return false;
    }

    private function executeCallback($callback)
    {
        if (is_callable($callback)) {
            return $callback();
        }

        if (is_string($callback)) {
            list($controller, $method) = explode('@', $callback);
            $controllerClass = "FrameJam\\Controllers\\{$controller}";
            
            if (!class_exists($controllerClass)) {
                throw new \Exception("Controller não encontrado: {$controllerClass}");
            }
            
            $controllerInstance = new $controllerClass();
            
            if (!method_exists($controllerInstance, $method)) {
                throw new \Exception("Método não encontrado: {$method}");
            }
            
            return $controllerInstance->$method();
        }

        throw new \Exception('Callback inválido');
    }

    public function middleware(string $name): self
    {
        $lastRoute = end($this->routes);
        if ($lastRoute) {
            $lastRoute['middleware'][] = $name;
        }
        return $this;
    }
} 