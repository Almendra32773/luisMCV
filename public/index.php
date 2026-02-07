<?php
// Activar errores para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Definir constantes PRIMERO
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('PUBLIC_PATH', __DIR__);

// Iniciar sesión DESPUÉS de definir constantes
session_start();

// Cargar autoload de Composer
require_once BASE_PATH . '/vendor/autoload.php';

// Cargar variables de entorno
use Dotenv\Dotenv;

if (file_exists(BASE_PATH . '/.env')) {
    $dotenv = Dotenv::createImmutable(BASE_PATH);
    $dotenv->load();
}

// Iniciar la aplicación
$app = new Core\App();
$app->run();