# Sistema de Filas

O FrameJam inclui um sistema de filas robusto para processar jobs em background. O sistema é baseado no Redis e oferece recursos como retry automático, jobs atrasados e monitoramento de falhas.

## Configuração

A configuração do sistema de filas está no arquivo `config/queue.php`:

```php
return [
    'driver' => 'redis',
    'host' => '127.0.0.1',
    'port' => 6379,
    'password' => null,
    'database' => 0,
    'prefix' => 'framejam:queue:',
    'default' => 'default',
    'failed' => 'failed',
    'retry_after' => 90,
    'block_for' => null,
];
```

## Criando Jobs

Para criar um job, estenda a classe base `FrameJam\Core\Queue\Job`:

```php
use FrameJam\Core\Queue\Job;

class SendEmailJob extends Job
{
    public function handle(): void
    {
        // Lógica do job
    }

    public function failed(\Throwable $exception): void
    {
        // Lógica de falha
    }
}
```

## Enviando Jobs para a Fila

Você pode enviar jobs para a fila de várias maneiras:

```php
// Usando o container
$queue = $app->getContainer()->make('queue');

// Enviando para a fila imediatamente
$queue->push('default', new SendEmailJob([
    'to' => 'usuario@exemplo.com',
    'subject' => 'Teste',
    'body' => 'Conteúdo do email'
]));

// Enviando para a fila com atraso
$queue->later('default', new SendEmailJob([
    'to' => 'usuario@exemplo.com',
    'subject' => 'Teste',
    'body' => 'Conteúdo do email'
]), 60); // 60 segundos de atraso
```

## Executando o Worker

Para processar os jobs, execute o worker usando o comando:

```bash
php framejam queue:work
```

Opções disponíveis:

- `--queue`: A fila para escutar (padrão: default)
- `--memory`: Limite de memória em megabytes (padrão: 128)
- `--timeout`: Número de segundos que um job pode rodar (padrão: 60)
- `--sleep`: Número de segundos para dormir quando não há jobs (padrão: 3)
- `--tries`: Número de tentativas antes de falhar (padrão: 3)
- `--max-jobs`: Número máximo de jobs para processar antes de parar

## Recursos

### Retry Automático

O sistema tenta automaticamente executar jobs que falham. O número de tentativas é configurável através da opção `--tries` do worker.

### Jobs Atrasados

Você pode agendar jobs para serem executados no futuro usando o método `later()`:

```php
$queue->later('default', $job, 60); // Executa após 60 segundos
```

### Monitoramento de Falhas

Jobs que falham após todas as tentativas são registrados no log. Você pode implementar lógica adicional no método `failed()` do job.

### Múltiplas Filas

O sistema suporta múltiplas filas, permitindo priorizar diferentes tipos de jobs:

```php
$queue->push('high', $job); // Fila de alta prioridade
$queue->push('low', $job);  // Fila de baixa prioridade
```

## Boas Práticas

1. Mantenha os jobs pequenos e focados em uma única responsabilidade
2. Use jobs atrasados para tarefas que não precisam ser executadas imediatamente
3. Implemente tratamento de erros adequado no método `failed()`
4. Monitore o uso de memória e ajuste o limite conforme necessário
5. Use filas diferentes para diferentes tipos de jobs 