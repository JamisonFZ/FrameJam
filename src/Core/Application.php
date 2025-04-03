<?php

namespace FrameJam\Core;

use FrameJam\Core\Router;
use FrameJam\Core\Request;
use FrameJam\Core\Response;
use FrameJam\Core\Container;
use FrameJam\Core\Config;

class Application
{
    private static ?Application $instance = null;
    private Container $container;
    private Router $router;
    private Config $config;

    private function __construct()
    {
        $this->container = new Container();
        $this->router = new Router($this->container);
        $this->config = new Config();

        // Registra as classes base
        $this->container->singleton(Request::class);
        $this->container->singleton(Response::class);
        $this->container->singleton(Container::class, function() {
            return $this->container;
        });
    }

    public static function getInstance(): Application
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function run(): void
    {
        $request = Request::createFromGlobals();
        $response = $this->router->dispatch($request);
        $response->send();
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

    public function getRouter(): Router
    {
        return $this->router;
    }

    public function getConfig(): Config
    {
        return $this->config;
    }
} 