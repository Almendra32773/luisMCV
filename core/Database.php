<?php

namespace Core;

use Exception;
use RedBeanPHP\R;

class Database
{
    public static function connect()
    {
        try {
            $host = $_ENV['DB_HOST'] ?? 'localhost';
            $db   = $_ENV['DB_DATABASE'] ?? 'mi_mvc_db';
            $user = $_ENV['DB_USERNAME'] ?? 'root';
            $pass = $_ENV['DB_PASSWORD'] ?? '';
            $port = $_ENV['DB_PORT'] ?? '3306';

            R::setup(
                "mysql:host={$host};port={$port};dbname={$db}",
                $user,
                $pass
            );

            if (!R::testConnection()) {
                throw new Exception('No se pudo establecer conexión con la base de datos');
            }

            if (($_ENV['APP_ENV'] ?? 'development') === 'production') {
                R::freeze(true);
            }

        } catch (Exception $e) {

            if (($_ENV['APP_ENV'] ?? 'development') !== 'production') {
                die(
                    "<pre><b>Error de base de datos:</b>\n" .
                    $e->getMessage() .
                    "</pre>"
                );
            }

            die('Error interno del servidor');
        }
    }
}
