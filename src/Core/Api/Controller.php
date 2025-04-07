<?php

namespace FrameJam\Core\Api;

use FrameJam\Core\Controller;

abstract class Controller extends \FrameJam\Core\Controller
{
    protected function jsonResponse($data, int $status = 200): array
    {
        http_response_code($status);
        return $data;
    }

    protected function errorResponse(string $message, int $status = 400): array
    {
        return $this->jsonResponse([
            'error' => true,
            'message' => $message
        ], $status);
    }

    protected function successResponse($data = null, string $message = 'Success'): array
    {
        return $this->jsonResponse([
            'error' => false,
            'message' => $message,
            'data' => $data
        ]);
    }

    protected function paginatedResponse($data, int $total, int $page, int $perPage): array
    {
        return $this->jsonResponse([
            'error' => false,
            'data' => $data,
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => ceil($total / $perPage)
            ]
        ]);
    }
} 