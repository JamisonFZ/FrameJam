<?php

namespace FrameJam\Core;

use FrameJam\Core\View\Template;

abstract class Controller
{
    protected Request $request;
    protected Response $response;
    protected Container $container;

    public function __construct(Request $request, Response $response, Container $container)
    {
        $this->request = $request;
        $this->response = $response;
        $this->container = $container;
    }

    protected function view(string $view, array $data = []): Response
    {
        $viewPath = dirname(__DIR__, 2) . '/views/' . $view . '.php';
        
        if (!file_exists($viewPath)) {
            throw new \Exception("View {$view} not found");
        }

        $template = new Template($viewPath, $data);
        $content = $template->render();

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