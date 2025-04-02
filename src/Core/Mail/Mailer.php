<?php

namespace FrameJam\Core\Mail;

use Symfony\Component\Mailer\Mailer as SymfonyMailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use FrameJam\Core\Application;

function config(string $key, $default = null)
{
    return Application::getInstance()->getConfig()->get($key, $default);
}

class Mailer
{
    private SymfonyMailer $mailer;

    public function __construct()
    {
        $config = Application::getInstance()->getConfig()->get('mail');
        
        $transport = Transport::fromDsn(
            "{$config['driver']}://{$config['username']}:{$config['password']}@{$config['host']}:{$config['port']}"
        );
        
        $this->mailer = new SymfonyMailer($transport);
    }

    public function send(string $to, string $subject, string $body, array $options = []): void
    {
        $email = new Email();
        
        $email->from($options['from'] ?? config('mail.from.address'))
              ->to($to)
              ->subject($subject)
              ->html($body);

        if (isset($options['cc'])) {
            $email->cc($options['cc']);
        }

        if (isset($options['bcc'])) {
            $email->bcc($options['bcc']);
        }

        if (isset($options['reply_to'])) {
            $email->replyTo($options['reply_to']);
        }

        if (isset($options['attachments'])) {
            foreach ($options['attachments'] as $attachment) {
                $email->attachFromPath($attachment['path'], $attachment['name'] ?? null);
            }
        }

        $this->mailer->send($email);
    }

    public function sendTemplate(string $to, string $subject, string $template, array $data = [], array $options = []): void
    {
        $view = Application::getInstance()->getContainer()->make('view');
        $body = $view->render($template, $data);
        
        $this->send($to, $subject, $body, $options);
    }
} 