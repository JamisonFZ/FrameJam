# Sistema de Filas

O sistema de filas do FrameJam fornece uma maneira robusta de processar tarefas em segundo plano, melhorando a performance e a experiência do usuário.

## Estrutura Básica

### Configuração Básica

```php
// config/queue.php
return [
    'default' => env('QUEUE_CONNECTION', 'database'),
    
    'connections' => [
        'sync' => [
            'driver' => 'sync',
        ],
        
        'database' => [
            'driver' => 'database',
            'table' => 'jobs',
            'queue' => 'default',
            'retry_after' => 90,
            'after_commit' => false,
        ],
        
        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
            'queue' => 'default',
            'retry_after' => 90,
            'block_for' => null,
        ],
    ],
    
    'failed' => [
        'driver' => 'database',
        'database' => 'mysql',
        'table' => 'failed_jobs',
    ],
];
```

### Jobs Básicos

```php
use FrameJam\Core\Queue\Job;

class SendWelcomeEmail extends Job
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function handle()
    {
        Mail::to($this->user)->send(new WelcomeEmail($this->user));
    }
}

// Enfileirar job
SendWelcomeEmail::dispatch($user);
```

## Exemplos Práticos

### Processamento de Imagens

```php
class ProcessImage extends Job
{
    protected $image;
    protected $sizes;

    public function __construct($image, array $sizes = [])
    {
        $this->image = $image;
        $this->sizes = $sizes ?: [100, 200, 500];
    }

    public function handle()
    {
        $img = Image::make($this->image->path);

        foreach ($this->sizes as $size) {
            $resized = $img->resize($size, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            $path = "images/{$size}/{$this->image->filename}";
            Storage::put($path, $resized->encode());
        }

        $this->image->update(['processed' => true]);
    }
}

// Uso
ProcessImage::dispatch($image)->onQueue('images');
```

### Importação de Dados

```php
class ImportUsers extends Job
{
    protected $file;
    protected $chunkSize = 1000;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function handle()
    {
        $handle = fopen($this->file, 'r');
        
        // Pular cabeçalho
        fgetcsv($handle);
        
        $chunk = [];
        
        while (($data = fgetcsv($handle)) !== false) {
            $chunk[] = [
                'name' => $data[0],
                'email' => $data[1],
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            if (count($chunk) >= $this->chunkSize) {
                User::insert($chunk);
                $chunk = [];
            }
        }
        
        if (!empty($chunk)) {
            User::insert($chunk);
        }
        
        fclose($handle);
        
        // Notificar conclusão
        event(new ImportCompleted($this->file));
    }
}

// Uso
ImportUsers::dispatch($file)->onQueue('imports');
```

### Notificações em Massa

```php
class SendBulkNotification extends Job
{
    protected $notification;
    protected $users;
    protected $chunkSize = 100;

    public function __construct($notification, $users)
    {
        $this->notification = $notification;
        $this->users = $users;
    }

    public function handle()
    {
        $this->users->chunk($this->chunkSize, function ($chunk) {
            foreach ($chunk as $user) {
                $user->notify($this->notification);
            }
        });
    }
}

// Uso
SendBulkNotification::dispatch(
    new SystemMaintenanceNotification(),
    User::all()
)->onQueue('notifications');
```

### Processamento de Relatórios

```php
class GenerateReport extends Job
{
    protected $report;
    protected $filters;
    protected $user;

    public function __construct($report, $filters, $user)
    {
        $this->report = $report;
        $this->filters = $filters;
        $this->user = $user;
    }

    public function handle()
    {
        // Gerar relatório
        $data = $this->report->generate($this->filters);
        
        // Salvar arquivo
        $filename = "reports/{$this->report->id}.pdf";
        PDF::loadView('reports.template', $data)
           ->save(storage_path("app/{$filename}"));
        
        // Notificar usuário
        $this->user->notify(new ReportReadyNotification(
            $this->report,
            $filename
        ));
    }
}

// Uso
GenerateReport::dispatch($report, $filters, $user)
    ->onQueue('reports')
    ->delay(now()->addMinutes(5));
```

## Boas Práticas

1. **Organização**
   - Use filas específicas para diferentes tipos de jobs
   - Mantenha jobs pequenos e focados
   - Documente dependências e requisitos

2. **Performance**
   - Configure timeouts apropriados
   - Use processamento em chunks
   - Implemente retry policies

3. **Segurança**
   - Valide dados nos jobs
   - Use autenticação quando necessário
   - Implemente logs de jobs críticos

4. **Manutenção**
   - Monitore filas
   - Implemente tratamento de falhas
   - Mantenha documentação atualizada 