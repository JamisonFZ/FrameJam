<?php

namespace FrameJam\Core\Middleware;

use FrameJam\Core\Session;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle($request, \Closure $next)
    {
        if (!Session::get('user_id')) {
            return redirect('/login');
        }

        return $next($request);
    }
} 