<?php

namespace FrameJam\Core\Api\Middleware;

use FrameJam\Core\Middleware\MiddlewareInterface;
use FrameJam\Core\Api\Auth\ApiAuth;
use FrameJam\Core\Cache\FileCache;

class ApiAuthMiddleware implements MiddlewareInterface
{
    private ApiAuth $auth;
    private array $scopes = [];

    public function __construct(?ApiAuth $auth = null)
    {
        $this->auth = $auth ?? new ApiAuth(new FileCache(__DIR__ . '/../../../../storage/cache'));
    }

    public function requireScope(string $scope): self
    {
        $this->scopes[] = $scope;
        return $this;
    }

    public function handle($request, \Closure $next)
    {
        $token = $this->getTokenFromRequest();
        
        if (!$token) {
            return $this->unauthorized('Token não fornecido');
        }
        
        $tokenData = $this->auth->validateToken($token);
        
        if (!$tokenData) {
            return $this->unauthorized('Token inválido ou expirado');
        }
        
        if (!empty($this->scopes)) {
            foreach ($this->scopes as $scope) {
                if (!$this->auth->hasScope($token, $scope)) {
                    return $this->forbidden("Acesso negado: escopo '{$scope}' necessário");
                }
            }
        }
        
        // Adicionar dados do usuário à requisição
        $request['user_id'] = $tokenData['user_id'];
        
        return $next($request);
    }

    private function getTokenFromRequest(): ?string
    {
        $headers = getallheaders();
        
        // Verificar no cabeçalho Authorization
        if (isset($headers['Authorization'])) {
            $auth = $headers['Authorization'];
            if (preg_match('/Bearer\s+(.*)$/i', $auth, $matches)) {
                return $matches[1];
            }
        }
        
        // Verificar no parâmetro da URL
        if (isset($_GET['token'])) {
            return $_GET['token'];
        }
        
        return null;
    }

    private function unauthorized(string $message): array
    {
        http_response_code(401);
        return ['error' => $message];
    }

    private function forbidden(string $message): array
    {
        http_response_code(403);
        return ['error' => $message];
    }
} 