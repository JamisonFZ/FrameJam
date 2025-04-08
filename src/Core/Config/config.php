<?php

namespace FrameJam\Core\Config;

class Config
{
    private static $config = [];

    public static function load()
    {
        // Carrega o arquivo .env
        $envFile = dirname(dirname(dirname(dirname(__DIR__)))) . '/.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                    list($key, $value) = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value);
                    
                    // Remove aspas se existirem
                    if (preg_match('/^(["\']).*\1$/', $value)) {
                        $value = substr($value, 1, -1);
                    }
                    
                    putenv("$key=$value");
                    $_ENV[$key] = $value;
                    $_SERVER[$key] = $value;
                }
            }
        }

        // Carrega as configurações específicas
        self::loadAppConfig();
        self::loadDatabaseConfig();
        self::loadCacheConfig();
        self::loadSessionConfig();
        self::loadLogConfig();
        self::loadMailConfig();
        self::loadQueueConfig();
        self::loadUploadConfig();
        self::loadApiConfig();
        self::loadSecurityConfig();
    }

    private static function loadAppConfig()
    {
        self::$config['app'] = [
            'name' => getenv('APP_NAME') ?: 'FrameJam',
            'env' => getenv('APP_ENV') ?: 'local',
            'key' => getenv('APP_KEY'),
            'debug' => getenv('APP_DEBUG') === 'true',
            'url' => getenv('APP_URL') ?: 'http://localhost',
            'timezone' => getenv('APP_TIMEZONE') ?: 'America/Sao_Paulo',
            'locale' => getenv('APP_LOCALE') ?: 'pt_BR',
        ];
    }

    private static function loadDatabaseConfig()
    {
        self::$config['database'] = [
            'connection' => getenv('DB_CONNECTION') ?: 'mysql',
            'host' => getenv('DB_HOST') ?: '127.0.0.1',
            'port' => getenv('DB_PORT') ?: '3306',
            'database' => getenv('DB_DATABASE') ?: 'framejam',
            'username' => getenv('DB_USERNAME') ?: 'root',
            'password' => getenv('DB_PASSWORD') ?: '',
        ];
    }

    private static function loadCacheConfig()
    {
        self::$config['cache'] = [
            'driver' => getenv('CACHE_DRIVER') ?: 'file',
            'prefix' => getenv('CACHE_PREFIX') ?: 'framejam_cache',
            'ttl' => getenv('CACHE_TTL') ?: 3600,
        ];
    }

    private static function loadSessionConfig()
    {
        self::$config['session'] = [
            'driver' => getenv('SESSION_DRIVER') ?: 'file',
            'lifetime' => getenv('SESSION_LIFETIME') ?: 120,
            'secure_cookie' => getenv('SESSION_SECURE_COOKIE') === 'true',
            'domain' => getenv('SESSION_DOMAIN'),
            'path' => getenv('SESSION_PATH') ?: '/storage/sessions',
        ];
    }

    private static function loadLogConfig()
    {
        self::$config['log'] = [
            'channel' => getenv('LOG_CHANNEL') ?: 'stack',
            'level' => getenv('LOG_LEVEL') ?: 'debug',
            'slack_webhook_url' => getenv('LOG_SLACK_WEBHOOK_URL'),
        ];
    }

    private static function loadMailConfig()
    {
        self::$config['mail'] = [
            'driver' => getenv('MAIL_DRIVER') ?: 'smtp',
            'host' => getenv('MAIL_HOST'),
            'port' => getenv('MAIL_PORT'),
            'username' => getenv('MAIL_USERNAME'),
            'password' => getenv('MAIL_PASSWORD'),
            'encryption' => getenv('MAIL_ENCRYPTION') ?: 'tls',
            'from_address' => getenv('MAIL_FROM_ADDRESS'),
            'from_name' => getenv('MAIL_FROM_NAME') ?: getenv('APP_NAME'),
        ];
    }

    private static function loadQueueConfig()
    {
        self::$config['queue'] = [
            'connection' => getenv('QUEUE_CONNECTION') ?: 'database',
            'timeout' => getenv('QUEUE_TIMEOUT') ?: 60,
            'retry_after' => getenv('QUEUE_RETRY_AFTER') ?: 90,
        ];
    }

    private static function loadUploadConfig()
    {
        self::$config['upload'] = [
            'driver' => getenv('FILESYSTEM_DRIVER') ?: 'local',
            'disk' => getenv('FILESYSTEM_DISK') ?: 'public',
            'max_size' => getenv('UPLOAD_MAX_SIZE') ?: 5120,
            'allowed_types' => explode(',', getenv('UPLOAD_ALLOWED_TYPES') ?: 'jpg,jpeg,png,pdf,doc,docx'),
        ];
    }

    private static function loadApiConfig()
    {
        self::$config['api'] = [
            'debug' => getenv('API_DEBUG') === 'true',
            'throttle' => getenv('API_THROTTLE') ?: '60,1',
            'token_lifetime' => getenv('API_TOKEN_LIFETIME') ?: 60,
        ];
    }

    private static function loadSecurityConfig()
    {
        self::$config['security'] = [
            'encryption_key' => getenv('ENCRYPTION_KEY'),
            'cors' => [
                'allowed_origins' => explode(',', getenv('CORS_ALLOWED_ORIGINS') ?: '*'),
                'allowed_methods' => explode(',', getenv('CORS_ALLOWED_METHODS') ?: 'GET,POST,PUT,DELETE,OPTIONS'),
                'allowed_headers' => explode(',', getenv('CORS_ALLOWED_HEADERS') ?: 'Content-Type,Authorization,X-Requested-With'),
            ],
        ];
    }

    public static function get($key, $default = null)
    {
        $keys = explode('.', $key);
        $config = self::$config;

        foreach ($keys as $segment) {
            if (!isset($config[$segment])) {
                return $default;
            }
            $config = $config[$segment];
        }

        return $config;
    }

    public static function set($key, $value)
    {
        $keys = explode('.', $key);
        $config = &self::$config;

        foreach ($keys as $segment) {
            if (!isset($config[$segment])) {
                $config[$segment] = [];
            }
            $config = &$config[$segment];
        }

        $config = $value;
    }
} 