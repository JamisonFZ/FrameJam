<?php

namespace FrameJam\Core\Auth;

use FrameJam\Core\Database\Model;
use FrameJam\Core\Application;

class Auth
{
    private ?Model $user = null;
    private string $guard = 'web';
    private string $sessionKey = 'auth_user';

    public function __construct()
    {
        $this->user = $this->getUserFromSession();
    }

    public function attempt(array $credentials): bool
    {
        $model = $this->getModel();
        
        $user = $model::where('email', $credentials['email'])->first();
        
        if (!$user || !password_verify($credentials['password'], $user->password)) {
            return false;
        }

        $this->login($user);
        return true;
    }

    public function login(Model $user): void
    {
        $this->user = $user;
        $_SESSION[$this->sessionKey] = $user->id;
    }

    public function logout(): void
    {
        $this->user = null;
        unset($_SESSION[$this->sessionKey]);
    }

    public function check(): bool
    {
        return $this->user !== null;
    }

    public function user(): ?Model
    {
        return $this->user;
    }

    public function id(): ?int
    {
        return $this->user ? $this->user->id : null;
    }

    public function setGuard(string $guard): self
    {
        $this->guard = $guard;
        $this->sessionKey = "auth_user_{$guard}";
        return $this;
    }

    private function getUserFromSession(): ?Model
    {
        if (!isset($_SESSION[$this->sessionKey])) {
            return null;
        }

        $model = $this->getModel();
        return $model::find($_SESSION[$this->sessionKey]);
    }

    private function getModel(): string
    {
        $config = Application::getInstance()->getConfig();
        return $config->get("auth.guards.{$this->guard}.model");
    }
} 