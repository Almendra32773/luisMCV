<?php
namespace App\Controllers;

use Core\{Controller, Middleware, Csrf};
use App\Models\User;
use RedBeanPHP\R; // Asegurar que la clase R esté disponible

class AuthController extends Controller
{
    // GET /login
    public function login()
    {
        Middleware::guest();
        Middleware::ensureSession();
        $this->view('auth/login');
    }

    // POST /login
    public function authenticate()
    {
        Middleware::ensureSession();
        Csrf::verify();
        
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (!$email || !$password) {
            $_SESSION['error'] = 'Datos incompletos';
            header('Location: /login');
            exit;
        }

        // Buscar usuario en tu base de datos (tabla 'users')
        $user = R::findOne('user', 'email = ? AND active = 1', [$email]);

        if (!$user || !password_verify($password, $user->password)) {
            $_SESSION['error'] = 'Credenciales inválidas';
            header('Location: /login');
            exit;
        }

        if ($user && password_verify($password, $user->password)) {
            // Regenerar ID de sesión por seguridad
            session_regenerate_id(true);

            // Guardar datos de usuario en sesión
            $_SESSION['user'] = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role
            ];
            $_SESSION['toast'] = 'Login exitoso 🎉';

            // Redirigir al dashboard o página principal
            header('Location: /dashboard');
            exit;
        }

        // Regenerar ID de sesión por seguridad
        session_regenerate_id(true);
        
        // Guardar datos de usuario en sesión
        $_SESSION['user'] = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role
        ];
        $_SESSION['toast'] = 'Login exitoso 🎉';
        
        header('Location: /');
        exit;
    }

    // GET /register
    public function register()
    {
        Middleware::guest();
        Middleware::ensureSession();
        $this->view('auth/register');
    }

    // POST /register
    public function store()
    {
        Middleware::ensureSession();
        Csrf::verify();
        
        $name     = $_POST['name'] ?? '';
        $email    = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (!$name || !$email || !$password) {
            $_SESSION['error'] = 'Datos incompletos';
            header('Location: /register');
            exit;
        }

        // Verificar si el email ya existe
        $existingUser = R::findOne('user', 'email = ?', [$email]);
        if ($existingUser) {
            $_SESSION['error'] = 'El email ya está registrado';
            header('Location: /register');
            exit;
        }

        // Crear nuevo usuario
        $user = R::dispense('user');
        $user->name = $name;
        $user->email = $email;
        $user->password = password_hash($password, PASSWORD_DEFAULT);
        $user->role = 'user'; // Rol por defecto
        $user->active = 1;
        
        R::store($user);

        $_SESSION['toast'] = 'Cuenta creada correctamente, ahora inicia sesión 👌';
        header('Location: /login');
        exit;
    }

    public function logout()
    {
        Middleware::auth();
        
        // Destruir completamente la sesión
        $_SESSION = [];
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
        
        // Iniciar nueva sesión solo para el mensaje flash
        session_start();
        $_SESSION['toast'] = 'Sesión cerrada correctamente';
        
        header('Location: /');
        exit;
    }
}