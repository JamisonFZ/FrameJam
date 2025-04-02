<?php

require_once __DIR__ . '/../vendor/autoload.php';

use FrameJam\Core\Application;
use FrameJam\Jobs\SendEmailJob;

// Inicializa a aplicação
$app = Application::getInstance();

// Cria um job para enviar email
$job = new SendEmailJob([
    'to' => 'usuario@exemplo.com',
    'subject' => 'Teste de Email',
    'body' => 'Este é um teste de envio de email através do sistema de filas.',
    'options' => [
        'from' => 'noreply@framejam.com',
    ],
]);

// Adiciona o job à fila
$app->getContainer()->make('queue')->push('default', $job);

echo "Job adicionado à fila com sucesso!\n"; 