<?php

namespace FrameJam\Core;

use FrameJam\Core\Router;
use FrameJam\Core\Container;
use FrameJam\Core\Exceptions\ApplicationException;
use FrameJam\Core\Config\Config;

class Application
{
    private static $instance = null;
    private Container $container;
    private Router $router;
    private array $config = [];
    private $booted = false;

    private function __construct()
    {
        $this->container = new Container();
        $this->registerBaseBindings();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function registerBaseBindings(): void
    {
        $this->container->singleton('app', function () {
            return $this;
        });

        $this->container->singleton('config', function () {
            return Config::class;
        });
    }

    public function boot(): void
    {
        if ($this->booted) {
            return;
        }

        // Carrega as configurações
        Config::load();

        // Configura o timezone
        date_default_timezone_set(Config::get('app.timezone', 'America/Sao_Paulo'));

        // Configura o locale
        setlocale(LC_ALL, Config::get('app.locale', 'pt_BR'));

        // Registra os serviços base
        $this->registerBaseServices();

        $this->booted = true;
    }

    private function registerBaseServices(): void
    {
        // Registra o Router
        $this->container->singleton('router', function () {
            return new Router();
        });

        // Registra o Controller base
        $this->container->singleton('controller', function () {
            return Controller::class;
        });

        // Registra o Model base
        $this->container->singleton('model', function () {
            return Model::class;
        });

        // Registra o Session
        $this->container->singleton('session', function () {
            return new Session();
        });

        // Registra o Cache
        $this->container->singleton('cache', function () {
            $driver = Config::get('cache.driver', 'file');
            $class = "FrameJam\\Core\\Cache\\Drivers\\" . ucfirst($driver) . "Driver";
            return new $class();
        });

        // Registra o Log
        $this->container->singleton('log', function () {
            $channel = Config::get('log.channel', 'stack');
            $class = "FrameJam\\Core\\Log\\Channels\\" . ucfirst($channel) . "Channel";
            return new $class();
        });
    }

    public function run(): void
    {
        try {
            $response = $this->router->dispatch();
            $this->sendResponse($response);
        } catch (\Throwable $e) {
            $this->handleException($e);
        }
    }

    private function sendResponse($response): void
    {
        if (is_string($response)) {
            echo $response;
            return;
        }

        if (is_array($response)) {
            header('Content-Type: application/json');
            echo json_encode($response);
            return;
        }

        if (method_exists($response, 'render')) {
            echo $response->render();
            return;
        }

        throw new ApplicationException('Tipo de resposta não suportado');
    }

    private function handleException(\Throwable $e): void
    {
        // TODO: Implementar sistema de logs
        http_response_code(500);
        echo "Erro: " . $e->getMessage();
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

    public function getRouter(): Router
    {
        return $this->router;
    }

    public function setConfig(string $key, $value): void
    {
        $this->config[$key] = $value;
    }

    public function getConfig(string $key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    public function get($abstract)
    {
        return $this->container->get($abstract);
    }

    public function make($abstract, array $parameters = [])
    {
        return $this->container->make($abstract, $parameters);
    }

    public function singleton($abstract, $concrete = null)
    {
        return $this->container->singleton($abstract, $concrete);
    }

    public function bind($abstract, $concrete = null, $shared = false)
    {
        return $this->container->bind($abstract, $concrete, $shared);
    }
} 