<?php
namespace Core;

class Csrf
{
    public static function generate()
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    public static function get()
    {
        return self::generate();
    }
    
    public static function verify()
    {
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token'])) {
            throw new \Exception('Token CSRF no válido');
        }
        
        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            throw new \Exception('Token CSRF no coincide');
        }
        
        // Regenerar token después de verificar
        self::generate();
    }
}