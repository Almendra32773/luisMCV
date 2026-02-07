<?php
return [
    'name' => $_ENV['APP_NAME'] ?? 'Biblioteca MVC',
    'url' => $_ENV['APP_URL'] ?? 'http://localhost:8000',
    'debug' => ($_ENV['APP_DEBUG'] ?? 'false') === 'true',
    'env' => $_ENV['APP_ENV'] ?? 'development',
    'timezone' => $_ENV['APP_TIMEZONE'] ?? 'America/Caracas',
];