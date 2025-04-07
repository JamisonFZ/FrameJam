<?php

namespace App\Jobs;

use FrameJam\Core\Queue\Job;

class SendEmailJob extends Job
{
    private string $to;
    private string $subject;
    private string $body;

    public function __construct(array $data)
    {
        parent::__construct($data);
        
        $this->to = $data['to'];
        $this->subject = $data['subject'];
        $this->body = $data['body'];
    }

    public function handle(): void
    {
        // Aqui você implementaria a lógica real de envio de email
        // Por exemplo, usando PHPMailer ou outro serviço de email
        
        // Simulando o envio de email
        sleep(1); // Simula o tempo de envio
        
        // Log do envio
        error_log("Email enviado para: {$this->to}");
    }

    public function failed(\Throwable $exception): void
    {
        // Log do erro
        error_log("Falha ao enviar email para {$this->to}: " . $exception->getMessage());
        
        // Aqui você poderia implementar uma lógica de retry ou notificação
    }
} 