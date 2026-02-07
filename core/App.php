<?php

namespace Core;

use Exception;

class App
{
    public function run()
    {
        try {
            // Conectar a la base de datos
            Database::connect();
            
            // Crear router
            $router = new Router();
            
            // Cargar rutas
            require_once dirname(__DIR__) . '/routes/web.php';
            
            // Resolver ruta
            $route = $router->resolve();
            
            // Procesar controlador
            $this->processRoute($route);
            
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }
    
    protected function processRoute($route)
    {
        if (!isset($route['action'])) {
            throw new Exception('Acción no definida en la ruta');
        }
        
        // Separar controlador y método
        list($controllerName, $methodName) = explode('@', $route['action']);
        $params = $route['params'] ?? [];
        
        // Namespace completo del controlador
        $controllerClass = 'App\\Controllers\\' . $controllerName;
        
        // Verificar que exista la clase
        if (!class_exists($controllerClass)) {
            throw new Exception("Controlador no encontrado: {$controllerClass}", 404);
        }
        
        // Crear instancia del controlador
        $controller = new $controllerClass();
        
        // Verificar que exista el método
        if (!method_exists($controller, $methodName)) {
            throw new Exception("Método no encontrado: {$controllerClass}@{$methodName}", 404);
        }
        
        // Preparar parámetros
        $methodParams = $this->prepareMethodParameters($controllerClass, $methodName, $params);
        
        // Llamar al método del controlador
        call_user_func_array([$controller, $methodName], $methodParams);
    }
    
    protected function prepareMethodParameters($controllerClass, $methodName, $routeParams)
    {
        $method = new \ReflectionMethod($controllerClass, $methodName);
        $parameters = $method->getParameters();
        $methodParams = [];
        
        foreach ($parameters as $param) {
            $paramName = $param->getName();
            
            // Buscar en los parámetros de la ruta
            if (isset($routeParams[$paramName])) {
                $methodParams[] = $routeParams[$paramName];
            }
            // Si tiene valor por defecto
            elseif ($param->isDefaultValueAvailable()) {
                $methodParams[] = $param->getDefaultValue();
            }
            // Si no, null
            else {
                $methodParams[] = null;
            }
        }
        
        return $methodParams;
    }
    
    protected function handleException(Exception $e)
    {
        $code = $e->getCode();
        if ($code < 100 || $code >= 600) {
            $code = 500;
        }
        
        http_response_code($code);
        
        // Modo desarrollo: mostrar errores detallados
        $env = $_ENV['APP_ENV'] ?? 'development';
        $debug = ($_ENV['APP_DEBUG'] ?? 'false') === 'true';
        
        if ($env === 'development' || $debug) {
            $this->showDevelopmentError($e);
        } else {
            // Modo producción: página de error simple
            $this->showProductionError($code);
        }
        
        exit;
    }
    
    protected function showDevelopmentError(Exception $e)
    {
        echo '<!DOCTYPE html>';
        echo '<html><head><title>Error</title>';
        echo '<style>';
        echo 'body { font-family: Arial, sans-serif; padding: 20px; background: #f8f9fa; }';
        echo '.error-container { background: white; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }';
        echo '.error-title { color: #dc3545; margin-top: 0; }';
        echo 'pre { background: #f8f9fa; padding: 10px; border-radius: 3px; overflow: auto; }';
        echo '</style>';
        echo '</head><body>';
        
        echo '<div class="error-container">';
        echo '<h1 class="error-title">Error ' . $e->getCode() . '</h1>';
        echo '<p><strong>Mensaje:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<p><strong>Archivo:</strong> ' . $e->getFile() . ':' . $e->getLine() . '</p>';
        
        echo '<h3>Stack Trace:</h3>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        
        echo '<h3>Información de la solicitud:</h3>';
        echo '<pre>';
        echo 'URI: ' . ($_SERVER['REQUEST_URI'] ?? 'N/A') . "\n";
        echo 'Método: ' . ($_SERVER['REQUEST_METHOD'] ?? 'N/A') . "\n";
        echo 'Script: ' . ($_SERVER['SCRIPT_NAME'] ?? 'N/A') . "\n";
        echo 'Base Path: ' . dirname($_SERVER['SCRIPT_NAME'] ?? '') . "\n";
        echo '</pre>';
        
        echo '</div>';
        echo '</body></html>';
    }
    
    protected function showProductionError($code)
    {
        $errorFile = BASE_PATH . "/app/views/errors/{$code}.php";
        if (file_exists($errorFile)) {
            require $errorFile;
        } else {
            echo '<!DOCTYPE html>';
            echo '<html><head><title>Error ' . $code . '</title></head>';
            echo '<body style="font-family: Arial, sans-serif; text-align: center; padding: 50px;">';
            echo '<h1>Error ' . $code . '</h1>';
            echo '<p>Ha ocurrido un error inesperado.</p>';
            echo '<p><a href="/luisMCV/public/">Volver al inicio</a></p>';
            echo '</body></html>';
        }
    }
}