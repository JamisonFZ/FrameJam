<?php

namespace FrameJam\Core;

abstract class Controller
{
    protected Request $request;
    protected Response $response;
    protected Container $container;

    public function __construct()
    {
        $this->request = Application::getInstance()->getContainer()->make(Request::class);
        $this->response = Application::getInstance()->getContainer()->make(Response::class);
        $this->container = Application::getInstance()->getContainer();
    }

    protected function view(string $view, array $data = []): Response
    {
        $viewPath = dirname(__DIR__, 2) . '/views/' . $view . '.php';
        
        if (!file_exists($viewPath)) {
            throw new \Exception("View {$view} not found");
        }

        extract($data);
        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        return $this->response->setContent($content);
    }

    protected function json(array $data, int $statusCode = 200): Response
    {
        return $this->response->json($data, $statusCode);
    }

    protected function redirect(string $url, int $statusCode = 302): Response
    {
        return $this->response->redirect($url, $statusCode);
    }
} 