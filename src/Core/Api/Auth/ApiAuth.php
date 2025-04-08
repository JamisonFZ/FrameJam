<?php

namespace FrameJam\Core\Api\Auth;

use FrameJam\Core\Cache\CacheInterface;
use FrameJam\Core\Cache\FileCache;

class ApiAuth
{
    private CacheInterface $cache;
    private string $tokenPrefix = 'api_token_';
    private int $tokenExpiration = 3600; // 1 hora

    public function __construct(?CacheInterface $cache = null)
    {
        $this->cache = $cache ?? new FileCache(__DIR__ . '/../../../storage/cache');
    }

    public function generateToken(int $userId, array $scopes = []): string
    {
        $token = bin2hex(random_bytes(32));
        
        $this->cache->set(
            $this->tokenPrefix . $token,
            [
                'user_id' => $userId,
                'scopes' => $scopes,
                'created_at' => time()
            ],
            $this->tokenExpiration
        );
        
        return $token;
    }

    public function validateToken(string $token): ?array
    {
        $data = $this->cache->get($this->tokenPrefix . $token);
        
        if (!$data) {
            return null;
        }
        
        return $data;
    }

    public function revokeToken(string $token): bool
    {
        return $this->cache->delete($this->tokenPrefix . $token);
    }

    public function hasScope(string $token, string $scope): bool
    {
        $data = $this->validateToken($token);
        
        if (!$data || !isset($data['scopes'])) {
            return false;
        }
        
        return in_array($scope, $data['scopes']);
    }

    public function setTokenExpiration(int $seconds): void
    {
        $this->tokenExpiration = $seconds;
    }
} 