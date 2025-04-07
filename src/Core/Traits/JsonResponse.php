<?php

namespace FrameJam\Core\Traits;

trait JsonResponse
{
    protected function jsonResponse($data, int $status = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
        exit;
    }
} 