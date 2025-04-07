<?php

namespace FrameJam\Core;

class View
{
    /**
     * Renderiza uma view com os dados fornecidos
     *
     * @param string $view Nome da view a ser renderizada
     * @param array $data Dados a serem passados para a view
     * @return string
     */
    public static function render(string $view, array $data = []): string
    {
        // Extrai os dados para variáveis
        extract($data);
        
        // Define o caminho base das views
        $viewPath = dirname(__DIR__) . '/views/';
        
        // Constrói o caminho completo da view
        $viewFile = $viewPath . str_replace('.', '/', $view) . '.php';
        
        // Verifica se o arquivo da view existe
        if (!file_exists($viewFile)) {
            throw new \Exception("View não encontrada: {$view}");
        }
        
        // Inicia o buffer de saída
        ob_start();
        
        // Inclui o arquivo da view
        include $viewFile;
        
        // Retorna o conteúdo do buffer
        return ob_get_clean();
    }
} 