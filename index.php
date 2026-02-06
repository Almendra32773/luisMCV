<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Config\AppConfig;

// Ya está inicializado automáticamente en AppConfig

use Dotenv\Dotenv;

// Cargar variables de entorno
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Configurar reporte de errores basado en entorno
if ($_ENV['APP_ENV'] === 'production') {
    error_reporting(0);
    ini_set('display_errors', 0);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Función helper para acceder a variables de entorno
function env($key, $default = null) {
    return $_ENV[$key] ?? $default;
}

// Configuración de base de datos usando variables
$dbConfig = [
    'host' => env('DB_HOST', 'localhost'),
    'database' => env('DB_NAME', 'mi_base_datos'),
    'username' => env('DB_USER', 'root'),
    'password' => env('DB_PASSWORD', ''),
    'charset' => 'utf8mb4'
];

// Configuración de la aplicación
$appConfig = [
    'name' => env('APP_NAME', 'Mi Proyecto MVC'),
    'env' => env('APP_ENV', 'development'),
    'debug' => env('APP_DEBUG', true),
    'url' => 'http://localhost/luisMCV'
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($appConfig['name']) ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; line-height: 1.6; }
        .success { color: green; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; }
        .config-box { background: #f8f9fa; border: 1px solid #ddd; padding: 15px; margin: 10px 0; }
        .warning { color: #856404; background: #fff3cd; border: 1px solid #ffeaa7; padding: 10px; }
    </style>
</head>
<body>
    <h1><?= htmlspecialchars($appConfig['name']) ?></h1>
    
    <div class="success">
        ✅ Proyecto PHP funcionando correctamente con Dotenv
    </div>
    
    <h2>Configuración de la Aplicación</h2>
    <div class="config-box">
        <strong>Entorno:</strong> <?= htmlspecialchars($appConfig['env']) ?><br>
        <strong>Debug:</strong> <?= $appConfig['debug'] ? 'Activado' : 'Desactivado' ?><br>
        <strong>URL:</strong> <?= htmlspecialchars($appConfig['url']) ?>
    </div>
    
    <h2>Configuración de Base de Datos</h2>
    <div class="config-box">
        <?php foreach ($dbConfig as $key => $value): ?>
            <?php 
            // Ocultar contraseña por seguridad
            $displayValue = ($key === 'password') ? 
                (empty($value) ? '(vacía)' : '********') : 
                htmlspecialchars($value);
            ?>
            <strong><?= ucfirst($key) ?>:</strong> <?= $displayValue ?><br>
        <?php endforeach; ?>
    </div>
    
    <h2>Verificación de Librerías</h2>
    <div class="config-box">
        <?php
        $libraries = [
            'Dotenv' => class_exists('Dotenv\Dotenv'),
            'Dompdf' => class_exists('Dompdf\Dompdf'),
            'PDO' => extension_loaded('pdo'),
            'MySQLi' => extension_loaded('mysqli'),
            'GD (para imágenes)' => extension_loaded('gd'),
            'cURL' => extension_loaded('curl')
        ];
        
        foreach ($libraries as $lib => $installed):
            $status = $installed ? '✅ Instalado' : '❌ No instalado';
            $color = $installed ? 'green' : 'red';
        ?>
            <strong><?= $lib ?>:</strong> <span style="color: <?= $color ?>"><?= $status ?></span><br>
        <?php endforeach; ?>
    </div>
    
    <?php if (empty($_ENV['DB_PASSWORD'])): ?>
    <div class="warning">
        ⚠️ Advertencia: La contraseña de la base de datos está vacía. 
        En producción, configura una contraseña segura.
    </div>
    <?php endif; ?>
    
    <h2>Prueba de Conexión a Base de Datos</h2>
    <div class="config-box">
        <?php
        try {
            // Intentar conexión a MySQL
            $dsn = "mysql:host={$dbConfig['host']};charset={$dbConfig['charset']}";
            $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
            
            echo "✅ Conexión a MySQL exitosa<br>";
            echo "✅ Versión del servidor: " . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
            
            // Verificar si la base de datos existe
            $stmt = $pdo->query("SHOW DATABASES LIKE '{$dbConfig['database']}'");
            if ($stmt->rowCount() > 0) {
                echo "<br>✅ Base de datos '{$dbConfig['database']}' existe";
            } else {
                echo "<br>ℹ️ Base de datos '{$dbConfig['database']}' no existe. Puedes crearla.";
            }
            
        } catch (PDOException $e) {
            echo "❌ Error de conexión: " . htmlspecialchars($e->getMessage());
        }
        ?>
    </div>
    
    <hr>
    <footer>
        <small>
            Entorno: <?= htmlspecialchars($appConfig['env']) ?> | 
            PHP: <?= phpversion() ?> | 
            Servidor: <?= $_SERVER['SERVER_SOFTWARE'] ?? 'Desconocido' ?>
        </small>
    </footer>
</body>
</html>