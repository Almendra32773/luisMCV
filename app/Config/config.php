<?php
namespace App\Config;

class AppConfig {
    private static $config = [];
    
    public static function init() {
        // Cargar variables de entorno si no están cargadas
        if (empty($_ENV)) {
            $dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__, 2));
            $dotenv->load();
        }
        
        // Configuración de aplicación
        self::$config = [
            'app' => [
                'name' => self::env('APP_NAME', 'Mi MVC'),
                'env' => self::env('APP_ENV', 'development'),
                'debug' => self::env('APP_DEBUG', true),
                'url' => self::env('APP_URL', 'http://localhost'),
                'timezone' => 'America/Mexico_City'
            ],
            
            'database' => [
                'driver' => 'mysql',
                'host' => self::env('DB_HOST', 'localhost'),
                'port' => self::env('DB_PORT', '3306'),
                'database' => self::env('DB_NAME', ''),
                'username' => self::env('DB_USER', 'root'),
                'password' => self::env('DB_PASSWORD', ''),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => ''
            ],
            
            'mail' => [
                'host' => self::env('MAIL_HOST', ''),
                'port' => self::env('MAIL_PORT', 587),
                'username' => self::env('MAIL_USERNAME', ''),
                'password' => self::env('MAIL_PASSWORD', ''),
                'encryption' => self::env('MAIL_ENCRYPTION', 'tls')
            ]
        ];
    }
    
    public static function env($key, $default = null) {
        return $_ENV[$key] ?? $default;
    }
    
    public static function get($key = null, $default = null) {
        if ($key === null) {
            return self::$config;
        }
        
        $keys = explode('.', $key);
        $value = self::$config;
        
        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }
        
        return $value;
    }
    
    public static function isProduction() {
        return self::env('APP_ENV') === 'production';
    }
    
    public static function isDevelopment() {
        return self::env('APP_ENV') === 'development';
    }
}

// Inicializar configuración
AppConfig::init();