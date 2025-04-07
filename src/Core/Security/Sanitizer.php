<?php

namespace FrameJam\Core\Security;

class Sanitizer
{
    public static function sanitize($input)
    {
        if (is_array($input)) {
            return array_map([self::class, 'sanitize'], $input);
        }

        if (is_string($input)) {
            // Remove caracteres invisíveis
            $input = trim($input);
            
            // Converte caracteres especiais em entidades HTML
            $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
            
            // Remove tags HTML e PHP
            $input = strip_tags($input);
            
            // Remove caracteres de controle
            $input = preg_replace('/[\x00-\x1F\x7F]/u', '', $input);
            
            // Remove espaços em branco extras
            $input = preg_replace('/\s+/', ' ', $input);
        }

        return $input;
    }

    public static function sanitizeEmail(string $email): string
    {
        return filter_var($email, FILTER_SANITIZE_EMAIL);
    }

    public static function sanitizeUrl(string $url): string
    {
        return filter_var($url, FILTER_SANITIZE_URL);
    }

    public static function sanitizeInt($input): int
    {
        return filter_var($input, FILTER_SANITIZE_NUMBER_INT);
    }

    public static function sanitizeFloat($input): float
    {
        return filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }

    public static function sanitizeFileName(string $filename): string
    {
        // Remove caracteres especiais e espaços
        $filename = preg_replace('/[^a-zA-Z0-9\-\_\.]/', '', $filename);
        
        // Remove múltiplos pontos
        $filename = preg_replace('/\.+/', '.', $filename);
        
        // Remove pontos no início e fim
        $filename = trim($filename, '.');
        
        return $filename;
    }
} 