# Sistema de Log

O sistema de log do FrameJam fornece uma interface simples e flexível para registrar informações, erros e eventos em sua aplicação.

## Configuração

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

## Níveis de Log

```php
use FrameJam\Core\Log\Log;

// Informações gerais
Log::info('Mensagem informativa');

// Avisos
Log::warning('Aviso importante');

// Erros
Log::error('Erro encontrado');

// Debug
Log::debug('Informação de debug');

// Crítico
Log::critical('Erro crítico');

// Emergencial
Log::emergency('Situação de emergência');
```

## Contexto e Dados

```php
// Log com contexto
Log::info('Usuário logado', [
    'user_id' => $userId,
    'ip' => $request->ip()
]);

// Log de exceção
try {
    // código que pode gerar exceção
} catch (\Exception $e) {
    Log::error('Erro ao processar pagamento', [
        'exception' => $e,
        'order_id' => $orderId
    ]);
}
```

## Canais de Log

```php
// Log em canal específico
Log::channel('slack')->critical('Servidor fora do ar!');

// Log em múltiplos canais
Log::stack(['single', 'slack'])->emergency('Falha crítica!');
```

## Exemplos Práticos

### Log de Requisições

```php
class RequestLogger
{
    public function handle($request, \Closure $next)
    {
        $start = microtime(true);

        $response = $next($request);

        $duration = microtime(true) - $start;

        Log::info('Requisição processada', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_id' => Auth::id(),
            'duration' => $duration,
            'status' => $response->status(),
        ]);

        return $response;
    }
}
```

### Log de Erros

```php
class ErrorLogger
{
    public function handle($request, \Closure $next)
    {
        try {
            return $next($request);
        } catch (\Exception $e) {
            Log::error('Erro não tratado', [
                'exception' => $e,
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'input' => $request->except(['password', 'password_confirmation']),
                'user_id' => Auth::id(),
            ]);

            throw $e;
        }
    }
}
```

### Log de Auditoria

```php
class AuditLogger
{
    public function logAction($action, $model, $userId)
    {
        Log::channel('audit')->info('Ação de auditoria', [
            'action' => $action,
            'model' => get_class($model),
            'model_id' => $model->id,
            'user_id' => $userId,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now(),
        ]);
    }
}
```

### Log de Performance

```php
class PerformanceLogger
{
    public function logQuery($query, $time)
    {
        if ($time > 100) { // Log queries que levam mais de 100ms
            Log::channel('performance')->warning('Query lenta detectada', [
                'sql' => $query->toSql(),
                'bindings' => $query->getBindings(),
                'time' => $time,
                'url' => request()->fullUrl(),
            ]);
        }
    }
}
```

## Boas Práticas

1. **Organização**
   - Use níveis apropriados
   - Inclua contexto relevante
   - Estruture mensagens claramente

2. **Performance**
   - Evite log excessivo
   - Use canais específicos
   - Configure rotação de logs

3. **Segurança**
   - Não log dados sensíveis
   - Sanitize dados de entrada
   - Configure permissões de arquivo

4. **Manutenção**
   - Monitore tamanho dos logs
   - Implemente rotação automática
   - Mantenha backup dos logs 