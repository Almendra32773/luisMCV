<?php

namespace Core;

class Controller
{
    protected function view($view, $data = [], $layout = 'layouts/app')
    {
        // Extraer datos a variables
        extract($data);
        
        // Iniciar buffer
        ob_start();
        
        try {
            // Cargar la vista
            $viewPath = APP_PATH . "/Views/{$view}.php";
            if (!file_exists($viewPath)) {
                throw new \Exception("Vista no encontrada: {$view}");
            }
            require $viewPath;
            $content = ob_get_clean();

            // Cargar layout si se especifica
            if ($layout) {
                $layoutPath = APP_PATH . "/Views/{$layout}.php";
                if (!file_exists($layoutPath)) {
                    throw new \Exception("Layout no encontrado: {$layout}");
                }

                // El layout usará $content automáticamente
                require $layoutPath;
            } else {
                echo $content;
            }
        } catch (\Exception $e) {
            // Manejo de errores: mostrar mensaje y detener ejecución
            echo "<h1>Error al cargar la vista o layout</h1>";
            echo "<p>" . $e->getMessage() . "</p>";
            exit;
        }
    }
    
    protected function redirect($path)
    {
        $baseUrl = $this->getBaseUrl();
        $url = $baseUrl . '/' . ltrim($path, '/');
        header('Location: ' . $url);
        exit;
    }
    
    protected function getBaseUrl()
    {
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $basePath = dirname($scriptName);
        
        // Para subdirectorio /luisMCV/public/
        return rtrim($basePath, '/');
    }
    
    protected function url($path = '')
    {
        $baseUrl = $this->getBaseUrl();
        return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
    }
    
    protected function isLoggedIn()
    {
        return isset($_SESSION['user']);
    }
    
    protected function json($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}