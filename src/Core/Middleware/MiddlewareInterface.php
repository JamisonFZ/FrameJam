<?php

namespace FrameJam\Core\Middleware;

use FrameJam\Core\Request;
use FrameJam\Core\Response;

interface MiddlewareInterface
{
    public function handle(Request $request): ?Response;
} 