# Sistema de Cache

O sistema de cache do FrameJam fornece uma API unificada para armazenamento em cache, suportando vários drivers como arquivo, Redis, Memcached e array.

## Configuração

```php
// config/cache.php
return [
    'default' => env('CACHE_DRIVER', 'file'),

    'stores' => [
        'file' => [
            'driver' => 'file',
            'path' => storage_path('framework/cache/data'),
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'cache',
        ],

        'memcached' => [
            'driver' => 'memcached',
            'persistent_id' => env('MEMCACHED_PERSISTENT_ID'),
            'sasl' => [
                env('MEMCACHED_USERNAME'),
                env('MEMCACHED_PASSWORD'),
            ],
            'options' => [
                // Memcached::OPT_CONNECT_TIMEOUT => 2000,
            ],
            'servers' => [
                [
                    'host' => env('MEMCACHED_HOST', '127.0.0.1'),
                    'port' => env('MEMCACHED_PORT', 11211),
                    'weight' => 100,
                ],
            ],
        ],
    ],

    'prefix' => env('CACHE_PREFIX', 'framejam_cache'),
];
```

## Uso Básico

### Armazenando Itens

```php
use FrameJam\Core\Cache\Cache;

// Armazenar por 60 minutos
Cache::put('key', 'value', 60);

// Armazenar permanentemente
Cache::forever('key', 'value');

// Armazenar se não existir
Cache::add('key', 'value', 60);

// Incrementar/Decrementar
Cache::increment('key');
Cache::decrement('key');
```

### Recuperando Itens

```php
// Recuperar valor
$value = Cache::get('key');

// Recuperar com valor padrão
$value = Cache::get('key', 'default');

// Recuperar e remover
$value = Cache::pull('key');

// Verificar existência
if (Cache::has('key')) {
    // ...
}
```

### Removendo Itens

```php
// Remover item específico
Cache::forget('key');

// Remover múltiplos itens
Cache::forget(['key1', 'key2']);

// Limpar todo o cache
Cache::flush();
```

## Tags de Cache

```php
// Armazenar com tags
Cache::tags(['users', 'posts'])->put('key', 'value', 60);

// Recuperar com tags
$value = Cache::tags(['users'])->get('key');

// Limpar cache por tags
Cache::tags(['users'])->flush();
```

## Cache de Consultas

```php
// Cache de consulta por 60 minutos
$users = Cache::remember('users', 60, function () {
    return DB::table('users')->get();
});

// Cache de consulta permanente
$users = Cache::rememberForever('users', function () {
    return DB::table('users')->get();
});
```

## Exemplos Práticos

### Cache de Configurações

```php
class ConfigCache
{
    public function get($key, $default = null)
    {
        return Cache::remember('config.' . $key, 60, function () use ($key, $default) {
            return DB::table('configurations')
                ->where('key', $key)
                ->value('value') ?? $default;
        });
    }

    public function set($key, $value)
    {
        DB::table('configurations')
            ->updateOrInsert(['key' => $key], ['value' => $value]);

        Cache::forget('config.' . $key);
    }
}
```

### Cache de API

```php
class ApiCache
{
    public function get($url, $ttl = 60)
    {
        $key = 'api:' . md5($url);

        return Cache::remember($key, $ttl, function () use ($url) {
            $response = Http::get($url);
            return $response->json();
        });
    }
}
```

### Cache de Menu

```php
class MenuCache
{
    public function getMenu($userId)
    {
        return Cache::tags(['menu', 'user:' . $userId])->remember(
            'menu',
            60,
            function () use ($userId) {
                return $this->buildMenu($userId);
            }
        );
    }

    public function clearMenu($userId)
    {
        Cache::tags(['menu', 'user:' . $userId])->flush();
    }
}
```

## Boas Práticas

1. **Organização**
   - Use chaves descritivas
   - Agrupe por contexto
   - Documente estrutura

2. **Performance**
   - Configure TTL apropriado
   - Use tags quando necessário
   - Monitore uso de memória

3. **Segurança**
   - Não cache dados sensíveis
   - Use prefixos por ambiente
   - Valide dados cacheados

4. **Manutenção**
   - Limpe cache periodicamente
   - Monitore hit/miss ratio
   - Mantenha logs de cache 