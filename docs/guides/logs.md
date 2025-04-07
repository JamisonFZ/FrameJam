# Sistema de Logs

O sistema de logs do FrameJam fornece uma maneira flexível e poderosa de registrar eventos e informações importantes da aplicação.

## Estrutura Básica

### Configuração Básica

```php
// config/logging.php
return [
    'default' => env('LOG_CHANNEL', 'stack'),
    
    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['single', 'daily'],
            'ignore_exceptions' => false,
        ],
        
        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/framejam.log'),
            'level' => 'debug',
        ],
        
        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/framejam.log'),
            'level' => 'debug',
            'days' => 14,
        ],
        
        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'FrameJam Log',
            'emoji' => ':boom:',
            'level' => 'critical',
        ],
    ],
];
```

### Uso Básico

```php
use FrameJam\Core\Log\Log;

// Log de diferentes níveis
Log::emergency('Sistema fora do ar!');
Log::alert('Alerta de segurança!');
Log::critical('Erro crítico!');
Log::error('Erro na aplicação');
Log::warning('Aviso importante');
Log::notice('Notificação');
Log::info('Informação');
Log::debug('Debug');

// Log com contexto
Log::info('Usuário logado', [
    'user_id' => $user->id,
    'ip' => request()->ip()
]);

// Log com exceção
try {
    // código que pode lançar exceção
} catch (\Exception $e) {
    Log::error('Erro ao processar pagamento', [
        'exception' => $e,
        'order_id' => $order->id
    ]);
}
```

## Exemplos Práticos

### Log de Ações do Usuário

```php
class UserActionLogger
{
    public function logLogin($user)
    {
        Log::info('Usuário logado', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }

    public function logLogout($user)
    {
        Log::info('Usuário deslogado', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => request()->ip()
        ]);
    }

    public function logProfileUpdate($user, $changes)
    {
        Log::info('Perfil atualizado', [
            'user_id' => $user->id,
            'email' => $user->email,
            'changes' => $changes
        ]);
    }
}
```

### Log de Transações Financeiras

```php
class TransactionLogger
{
    public function logPayment($payment)
    {
        Log::info('Pagamento processado', [
            'payment_id' => $payment->id,
            'amount' => $payment->amount,
            'status' => $payment->status,
            'user_id' => $payment->user_id,
            'method' => $payment->method
        ]);
    }

    public function logRefund($refund)
    {
        Log::info('Reembolso processado', [
            'refund_id' => $refund->id,
            'payment_id' => $refund->payment_id,
            'amount' => $refund->amount,
            'reason' => $refund->reason
        ]);
    }
}
```

### Log de Erros da API

```php
class ApiErrorLogger
{
    public function logValidationError($errors)
    {
        Log::warning('Erro de validação na API', [
            'errors' => $errors,
            'endpoint' => request()->path(),
            'method' => request()->method(),
            'ip' => request()->ip()
        ]);
    }

    public function logAuthenticationError($credentials)
    {
        Log::warning('Tentativa de autenticação falhou', [
            'email' => $credentials['email'],
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }

    public function logRateLimitExceeded($user)
    {
        Log::warning('Limite de requisições excedido', [
            'user_id' => $user->id,
            'ip' => request()->ip(),
            'endpoint' => request()->path()
        ]);
    }
}
```

### Log de Performance

```php
class PerformanceLogger
{
    public function logQueryTime($query, $time)
    {
        if ($time > 1000) { // mais de 1 segundo
            Log::warning('Query lenta detectada', [
                'query' => $query,
                'time' => $time,
                'endpoint' => request()->path()
            ]);
        }
    }

    public function logMemoryUsage()
    {
        $memory = memory_get_usage(true);
        if ($memory > 128 * 1024 * 1024) { // mais de 128MB
            Log::warning('Alto uso de memória', [
                'memory' => $memory,
                'endpoint' => request()->path()
            ]);
        }
    }
}
```

## Boas Práticas

1. **Organização**
   - Use níveis de log apropriados
   - Inclua contexto relevante
   - Mantenha mensagens claras e concisas

2. **Performance**
   - Evite logging excessivo
   - Use canais assíncronos quando possível
   - Implemente rotação de logs

3. **Segurança**
   - Não logue dados sensíveis
   - Sanitize dados antes do log
   - Implemente retenção de logs

4. **Manutenção**
   - Monitore tamanho dos logs
   - Implemente limpeza automática
   - Mantenha documentação atualizada 