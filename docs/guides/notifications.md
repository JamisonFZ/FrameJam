# Sistema de Notificações

O sistema de notificações do FrameJam permite enviar notificações através de diversos canais como email, SMS, push e banco de dados.

## Estrutura Básica

### Definindo Notificações

```php
use FrameJam\Core\Notifications\Notification;

class WelcomeNotification extends Notification
{
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Bem-vindo ao FrameJam!')
            ->greeting('Olá ' . $notifiable->name)
            ->line('Obrigado por se cadastrar em nossa plataforma.')
            ->action('Acessar Dashboard', url('/dashboard'))
            ->line('Se você não criou uma conta, nenhuma ação é necessária.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Bem-vindo ao FrameJam!',
            'action_url' => url('/dashboard'),
        ];
    }
}
```

### Enviando Notificações

```php
// Para um usuário específico
$user->notify(new WelcomeNotification());

// Para múltiplos usuários
Notification::send($users, new OrderStatusNotification($order));

// Notificação imediata
$user->notifyNow(new AlertNotification());
```

## Configuração

### Configuração dos Canais

```php
// config/notifications.php
return [
    'channels' => [
        'mail' => [
            'driver' => 'smtp',
            'host' => env('MAIL_HOST'),
            'port' => env('MAIL_PORT'),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
        ],
        'sms' => [
            'driver' => 'twilio',
            'account_sid' => env('TWILIO_SID'),
            'auth_token' => env('TWILIO_TOKEN'),
            'from' => env('TWILIO_FROM'),
        ],
        'push' => [
            'driver' => 'firebase',
            'credentials' => storage_path('firebase-credentials.json'),
        ],
    ],
];
```

## Exemplos Práticos

### Notificação de Pedido

```php
class OrderStatusNotification extends Notification
{
    private $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Atualização do Pedido #' . $this->order->id)
            ->greeting('Olá ' . $notifiable->name)
            ->line('Seu pedido foi atualizado para: ' . $this->order->status)
            ->action('Ver Pedido', route('orders.show', $this->order))
            ->line('Obrigado por comprar conosco!');
    }

    public function toDatabase($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'status' => $this->order->status,
            'message' => 'Seu pedido foi atualizado para: ' . $this->order->status,
        ];
    }
}
```

### Notificação de Segurança

```php
class SecurityAlertNotification extends Notification
{
    private $alert;

    public function __construct($alert)
    {
        $this->alert = $alert;
    }

    public function via($notifiable)
    {
        return ['mail', 'sms'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Alerta de Segurança')
            ->greeting('Alerta!')
            ->line($this->alert->message)
            ->action('Verificar Conta', route('security.verify'))
            ->line('Se você não realizou esta ação, entre em contato conosco imediatamente.');
    }

    public function toSms($notifiable)
    {
        return (new SmsMessage)
            ->content($this->alert->message)
            ->action('Verificar: ' . route('security.verify'));
    }
}
```

### Notificação de Sistema

```php
class SystemNotification extends Notification
{
    private $metric;
    private $value;

    public function __construct($metric, $value)
    {
        $this->metric = $metric;
        $this->value = $value;
    }

    public function via($notifiable)
    {
        return ['mail', 'slack'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Alerta do Sistema')
            ->greeting('Alerta!')
            ->line("Métrica {$this->metric} excedeu o limite: {$this->value}")
            ->action('Ver Dashboard', route('admin.metrics'));
    }

    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->error()
            ->content('Alerta do Sistema')
            ->attachment(function ($attachment) {
                $attachment
                    ->title('Métrica Excedida')
                    ->fields([
                        'Métrica' => $this->metric,
                        'Valor' => $this->value,
                    ]);
            });
    }
}
```

### Notificação de Lembrete

```php
class ReminderNotification extends Notification
{
    private $reminder;

    public function __construct($reminder)
    {
        $this->reminder = $reminder;
    }

    public function via($notifiable)
    {
        return ['mail', 'push'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Lembrete: ' . $this->reminder->title)
            ->greeting('Olá ' . $notifiable->name)
            ->line($this->reminder->description)
            ->action('Ver Detalhes', route('reminders.show', $this->reminder));
    }

    public function toPush($notifiable)
    {
        return (new PushMessage)
            ->title($this->reminder->title)
            ->body($this->reminder->description)
            ->data([
                'reminder_id' => $this->reminder->id,
                'action' => 'view_reminder',
            ]);
    }
}
```

## Boas Práticas

1. **Organização**
   - Mantenha notificações em namespaces separados
   - Use nomes descritivos para notificações
   - Documente o propósito de cada notificação

2. **Performance**
   - Use filas para notificações pesadas
   - Implemente rate limiting
   - Monitore tempo de entrega

3. **Segurança**
   - Não inclua dados sensíveis
   - Use HTTPS para links
   - Implemente verificação de destinatário

4. **Manutenção**
   - Mantenha logs de notificações
   - Monitore taxas de entrega
   - Documente canais utilizados 