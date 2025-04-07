<?php

namespace FrameJam\Core\Middleware;

use FrameJam\Core\Session;

class CsrfMiddleware implements MiddlewareInterface
{
    public function handle($request, \Closure $next)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['_token'] ?? null;
            $sessionToken = Session::get('_token');

            if (!$token || $token !== $sessionToken) {
                throw new \Exception('CSRF token mismatch');
            }
        }

        return $next($request);
    }
} 