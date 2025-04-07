<?php

namespace FrameJam\Core\Middleware;

interface MiddlewareInterface
{
    public function handle($request, \Closure $next);
} 