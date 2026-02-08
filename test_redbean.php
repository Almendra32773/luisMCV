<?php
require_once __DIR__ . '/vendor/autoload.php';

use RedBeanPHP\R;

try {
    // Probar si la clase R está disponible
    if (class_exists('RedBeanPHP\\R')) {
        echo "RedBeanPHP está disponible.<br>";
    } else {
        echo "RedBeanPHP no está disponible.<br>";
    }

    // Intentar conectar a la base de datos
    Core\Database::connect();
    echo "Conexión exitosa a la base de datos.<br>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}