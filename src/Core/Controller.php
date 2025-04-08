<?php

namespace FrameJam\Core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

abstract class Controller
{
    protected Environment $view;
    protected array $data = [];

    public function __construct()
    {
        $loader = new FilesystemLoader(__DIR__ . '/../../resources/views');
        $this->view = new Environment($loader, [
            'cache' => __DIR__ . '/../../storage/cache/views',
            'auto_reload' => true,
            'debug' => true
        ]);
    }

    /**
     * Renderiza uma view usando o Twig
     * 
     * @param string $template Nome do template
     * @param array $data Dados para passar para o template
     * @return string HTML renderizado
     * @throws LoaderError|RuntimeError|SyntaxError
     */
    protected function view(string $template, array $data = []): string
    {
        return $this->view->render($template . '.twig', array_merge($this->data, $data));
    }

    /**
     * Retorna uma resposta JSON
     * 
     * @param mixed $data Dados para retornar
     * @param int $status Código HTTP
     * @return array
     */
    protected function json($data, int $status = 200): array
    {
        http_response_code($status);
        return $data;
    }

    /**
     * Redireciona para uma URL
     * 
     * @param string $url URL para redirecionar
     * @return void
     */
    protected function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }

    /**
     * Obtém um valor de input
     * 
     * @param string|null $key Chave do input
     * @param mixed $default Valor padrão
     * @return mixed
     */
    protected function input(string $key, $default = null)
    {
        if ($key === null) {
            return $_REQUEST;
        }

        return $_REQUEST[$key] ?? $default;
    }

    /**
     * Obtém um arquivo enviado
     * 
     * @param string|null $key Chave do arquivo
     * @return mixed
     */
    protected function file(string $key)
    {
        if ($key === null) {
            return $_FILES;
        }

        return $_FILES[$key] ?? null;
    }
} 