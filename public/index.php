<?php

require_once __DIR__ . '/../vendor/autoload.php';

use FrameJam\Core\Application;

// Inicializa a aplicação
$app = Application::getInstance();

// Define as rotas
$router = $app->getRouter();

// Exemplo de rotas
$router->get('/', function($request) {
    return new \FrameJam\Core\Response('Bem-vindo ao FrameJam!');
});

// Executa a aplicação
$app->run(); 