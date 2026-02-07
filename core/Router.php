<?php

namespace Core;

use Exception;

class Router
{
    protected $routes = [
        'GET' => [],
        'POST' => []
    ];
    
    protected $patterns = [
        'GET' => [],
        'POST' => []
    ];
    
    protected $notFoundCallback = null;

    public function get($uri, $controller)
    {
        $this->addRoute('GET', $uri, $controller);
    }

    public function post($uri, $controller)
    {
        $this->addRoute('POST', $uri, $controller);
    }
    
    public function add($method, $uri, $controller)
    {
        $this->addRoute(strtoupper($method), $uri, $controller);
    }

    protected function addRoute($method, $uri, $controller)
    {
        // Normalizar URI
        $uri = $this->normalizeUri($uri);
        
        // Si tiene parámetros {param}, convertir a patrón regex
        if (strpos($uri, '{') !== false) {
            $pattern = $this->convertUriToPattern($uri);
            $paramNames = $this->extractParamNames($uri);
            
            $this->patterns[$method][$pattern] = [
                'controller' => $controller,
                'param_names' => $paramNames,
                'original_uri' => $uri
            ];
        } else {
            $this->routes[$method][$uri] = $controller;
        }
    }
    
    protected function normalizeUri($uri)
    {
        $uri = trim($uri, '/');
        return $uri === '' ? '/' : '/' . $uri;
    }
    
    protected function convertUriToPattern($uri)
    {
        // Convertir {param} a ([^/]+)
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $uri);
        // Escapar para regex
        $pattern = str_replace('/', '\/', $pattern);
        return $pattern;
    }
    
    protected function extractParamNames($uri)
    {
        preg_match_all('/\{([^}]+)\}/', $uri, $matches);
        return $matches[1] ?? [];
    }

    public function resolve()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $this->getCurrentUri();
        
        // Debug: ver qué URI está procesando
        error_log("Router resolving: {$method} {$uri}");
        // DEBUG EXTENDIDO
        error_log("=== ROUTER DEBUG ===");
        error_log("Method: $method");
        error_log("URI: $uri");
        error_log("REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'N/A'));
        error_log("=== END DEBUG ===");

        // 1. Buscar ruta exacta
        if (isset($this->routes[$method][$uri])) {
            return [
                'action' => $this->routes[$method][$uri],
                'params' => []
            ];
        }
        
        // 2. Buscar por patrón (parámetros)
        foreach ($this->patterns[$method] as $pattern => $data) {
            $regex = '#^' . $pattern . '$#';
            if (preg_match($regex, $uri, $matches)) {
                array_shift($matches); // Remover match completo
                
                $params = [];
                foreach ($data['param_names'] as $index => $name) {
                    $params[$name] = $matches[$index] ?? null;
                }
                
                return [
                    'action' => $data['controller'],
                    'params' => $params
                ];
            }
        }
        
        // 3. Si hay callback 404, usarlo
        if ($this->notFoundCallback && is_callable($this->notFoundCallback)) {
            call_user_func($this->notFoundCallback);
            exit;
        }
        
        // 4. Si no, lanzar excepción
        throw new Exception("Ruta no encontrada: {$method} {$uri}", 404);
    }
    
    protected function getCurrentUri()
    {
        // Obtener URI del request
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        
        // Convertir a minúsculas para consistencia
        $uri = strtolower($uri);
        
        // Remover query string
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }
        
        // CORRECCIÓN CRÍTICA 1: Si hay index.php en la URI, extraer la parte después
        if (strpos($uri, '/index.php') !== false) {
            $uri = substr($uri, strpos($uri, '/index.php') + 10);
        }
        
        // CORRECCIÓN CRÍTICA 2: Remover /luismcv/public del inicio SI ESTÁ PRESENTE
        $basePath = '/luismcv/public';
        
        if (strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }
        
        // Si la URI está vacía después de procesar, hacerla "/"
        $uri = trim($uri, '/');
        if ($uri === '') {
            $uri = '/';
        } else {
            $uri = '/' . $uri;
        }
        
        // DEBUG
        error_log("Router URI: {$uri}");
        
        return $uri;
    }
}