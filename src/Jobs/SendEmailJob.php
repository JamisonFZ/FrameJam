<?php

namespace FrameJam\Jobs;

use FrameJam\Core\Queue\Job;
use FrameJam\Core\Mail\Mailer;

class SendEmailJob extends Job
{
    private Mailer $mailer;

    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->mailer = new Mailer();
    }

    public function handle(): void
    {
        $this->mailer->send(
            $this->data['to'],
            $this->data['subject'],
            $this->data['body'],
            $this->data['options'] ?? []
        );
    }

    public function failed(\Throwable $exception): void
    {
        // Aqui você pode implementar lógica adicional para lidar com falhas
        // Por exemplo, notificar um administrador ou registrar em um sistema de monitoramento
    }
} 