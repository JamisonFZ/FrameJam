<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Carrega as configurações
$config = require_once __DIR__ . '/../config/app.php';

// Inicializa a aplicação
$app = new FrameJam\Core\Application($config);

// Carrega as rotas
require_once __DIR__ . '/../routes/web.php';
require_once __DIR__ . '/../routes/api.php';

// Processa a requisição
$app->run(); 