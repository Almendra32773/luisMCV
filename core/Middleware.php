<?php
namespace Core;

class Middleware
{
    public static function auth()
    {
        if (!isset($_SESSION['user'])) {
            $_SESSION['error'] = 'Debes iniciar sesión para acceder';
            header('Location: /login');
            exit;
        }
    }
    
    public static function guest()
    {
        if (isset($_SESSION['user'])) {
            header('Location: /');
            exit;
        }
    }
    
    public static function admin()
    {
        self::auth();
        
        if ($_SESSION['user']['role'] !== 'admin') {
            $_SESSION['error'] = 'No tienes permisos de administrador';
            header('Location: /');
            exit;
        }
    }
    
    public static function ensureSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}