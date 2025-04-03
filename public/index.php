<?php

require_once __DIR__ . '/../vendor/autoload.php';

use FrameJam\Core\Application;

// Inicializa a aplicação
$app = Application::getInstance();

// Carrega as rotas
require_once __DIR__ . '/../routes/web.php';
require_once __DIR__ . '/../routes/api.php';

// Processa a requisição
$app->run(); 