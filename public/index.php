<?php

require_once __DIR__ . '/../vendor/autoload.php';

use FrameJam\Core\Application;

// Inicializa a aplicação
$app = Application::getInstance();

// Carrega as configurações e inicializa os serviços
$app->boot();

// Obtém o router
$router = $app->get('router');

// Carrega as rotas
require_once __DIR__ . '/../routes/web.php';

// Processa a requisição
$router->dispatch(); 