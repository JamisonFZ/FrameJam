# Sistema de Middleware

O sistema de middleware do FrameJam fornece uma maneira conveniente de filtrar e modificar requisições HTTP em sua aplicação.

## Estrutura Básica

### Middleware Básico

```php
use FrameJam\Core\Middleware\Middleware;

class ExampleMiddleware extends Middleware
{
    public function handle($request, \Closure $next)
    {
        // Lógica antes da requisição
        
        $response = $next($request);
        
        // Lógica depois da requisição
        
        return $response;
    }
}
```

### Registrando Middleware

```php
// app/Http/Kernel.php
class Kernel extends HttpKernel
{
    protected $middleware = [
        \FrameJam\Core\Middleware\TrustProxies::class,
        \FrameJam\Core\Middleware\PreventRequestsDuringMaintenance::class,
        \FrameJam\Core\Middleware\TrimStrings::class,
        \FrameJam\Core\Middleware\ConvertEmptyStringsToNull::class,
    ];

    protected $middlewareGroups = [
        'web' => [
            \FrameJam\Core\Middleware\EncryptCookies::class,
            \FrameJam\Core\Middleware\AddQueuedCookiesToResponse::class,
            \FrameJam\Core\Middleware\StartSession::class,
            \FrameJam\Core\Middleware\VerifyCsrfToken::class,
            \FrameJam\Core\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'throttle:api',
            \FrameJam\Core\Middleware\SubstituteBindings::class,
        ],
    ];

    protected $routeMiddleware = [
        'auth' => \FrameJam\Core\Middleware\Authenticate::class,
        'auth.basic' => \FrameJam\Core\Middleware\AuthenticateWithBasicAuth::class,
        'cache.headers' => \FrameJam\Core\Middleware\SetCacheHeaders::class,
        'can' => \FrameJam\Core\Middleware\Authorize::class,
        'guest' => \FrameJam\Core\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \FrameJam\Core\Middleware\RequirePassword::class,
        'signed' => \FrameJam\Core\Middleware\ValidateSignature::class,
        'throttle' => \FrameJam\Core\Middleware\ThrottleRequests::class,
        'verified' => \FrameJam\Core\Middleware\EnsureEmailIsVerified::class,
    ];
}
```

## Exemplos Práticos

### Middleware de Autenticação

```php
class Authenticate extends Middleware
{
    public function handle($request, \Closure $next)
    {
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Não autenticado.'], 401);
            }

            return redirect()->route('login');
        }

        return $next($request);
    }
}
```

### Middleware de Rate Limiting

```php
class ThrottleRequests extends Middleware
{
    protected $maxAttempts = 60;
    protected $decayMinutes = 1;

    public function handle($request, \Closure $next)
    {
        $key = $this->resolveRequestSignature($request);

        if ($this->tooManyAttempts($key)) {
            return $this->buildResponse($key);
        }

        $this->incrementAttempts($key);

        $response = $next($request);

        return $this->addHeaders(
            $response,
            $this->maxAttempts,
            $this->calculateRemainingAttempts($key)
        );
    }

    protected function resolveRequestSignature($request)
    {
        return sha1(implode('|', [
            $request->method(),
            $request->path(),
            $request->ip(),
        ]));
    }

    protected function tooManyAttempts($key)
    {
        return Cache::get($key, 0) >= $this->maxAttempts;
    }

    protected function incrementAttempts($key)
    {
        Cache::add($key, 1, $this->decayMinutes * 60);
        Cache::increment($key);
    }

    protected function buildResponse($key)
    {
        $response = response()->json([
            'message' => 'Muitas requisições.'
        ], 429);

        return $this->addHeaders(
            $response,
            $this->maxAttempts,
            $this->calculateRemainingAttempts($key)
        );
    }

    protected function addHeaders($response, $maxAttempts, $remainingAttempts)
    {
        return $response->withHeaders([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => $remainingAttempts,
            'Retry-After' => Cache::get($key, 0),
        ]);
    }
}
```

### Middleware de Log

```php
class RequestLogger extends Middleware
{
    public function handle($request, \Closure $next)
    {
        $start = microtime(true);

        $response = $next($request);

        $duration = microtime(true) - $start;

        Log::info('Request processed', [
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

### Middleware de CORS

```php
class Cors extends Middleware
{
    public function handle($request, \Closure $next)
    {
        $response = $next($request);

        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');

        if ($request->isMethod('OPTIONS')) {
            $response->setStatusCode(200);
        }

        return $response;
    }
}
```

### Middleware de Cache

```php
class CacheResponse extends Middleware
{
    protected $ttl = 3600; // 1 hora

    public function handle($request, \Closure $next)
    {
        if ($request->isMethod('GET')) {
            $key = $this->getCacheKey($request);

            if (Cache::has($key)) {
                return response(Cache::get($key))
                    ->header('X-Cache', 'HIT');
            }

            $response = $next($request);

            if ($response->isSuccessful()) {
                Cache::put($key, $response->getContent(), $this->ttl);
            }

            return $response->header('X-Cache', 'MISS');
        }

        return $next($request);
    }

    protected function getCacheKey($request)
    {
        return 'response:' . md5($request->fullUrl());
    }
}
```

## Boas Práticas

1. **Organização**
   - Mantenha middleware focado
   - Use grupos de middleware
   - Documente comportamento

2. **Performance**
   - Otimize lógica de middleware
   - Use cache quando apropriado
   - Evite processamento pesado

3. **Segurança**
   - Valide dados de entrada
   - Use headers de segurança
   - Implemente rate limiting

4. **Manutenção**
   - Monitore performance
   - Mantenha logs
   - Revise periodicamente 