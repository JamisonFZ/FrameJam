<?php

namespace FrameJam\Core\Security;

class CsrfProtection
{
    private const TOKEN_LENGTH = 32;

    public function generateToken(): string
    {
        $token = bin2hex(random_bytes(self::TOKEN_LENGTH));
        $_SESSION['csrf_token'] = $token;
        return $token;
    }

    public function validateToken(string $token): bool
    {
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }

    public function getTokenField(): string
    {
        return '<input type="hidden" name="_token" value="' . $this->generateToken() . '">';
    }

    public function validateRequest(): bool
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return true;
        }

        $token = $_POST['_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;

        if (!$token) {
            return false;
        }

        return $this->validateToken($token);
    }
} 