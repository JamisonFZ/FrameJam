<?php

namespace App\Controllers;

use FrameJam\Core\Controller;
use FrameJam\Core\Queue\QueueManager;
use FrameJam\Core\Http\Request;
use FrameJam\Core\Traits\JsonResponse;
use App\Jobs\SendEmailJob;

class EmailController extends Controller
{
    use JsonResponse;

    private QueueManager $queue;
    protected Request $request;

    public function __construct(QueueManager $queue, Request $request)
    {
        $this->queue = $queue;
        $this->request = $request;
    }

    public function send(): void
    {
        $data = $this->request->getPost();
        
        // Validação básica
        if (empty($data['to']) || empty($data['subject']) || empty($data['body'])) {
            $this->jsonResponse([
                'error' => 'Todos os campos são obrigatórios'
            ], 400);
            return;
        }

        // Criar e enviar o job para a fila
        $job = new SendEmailJob($data);
        $jobId = $this->queue->push($job);

        $this->jsonResponse([
            'message' => 'Email enviado para a fila',
            'job_id' => $jobId
        ]);
    }
} 