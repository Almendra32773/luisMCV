<?php
namespace App\Controllers;

use Core\{Controller, Middleware, Csrf};
use App\Models\User;

class UserController extends Controller
{
    // GET /users - Listar usuarios del sistema
    public function index()
    {
        Middleware::auth();
        Middleware::admin(); // Solo administradores
        
        $users = User::allOrdered(); // Cambiado de getAll a allOrdered
        
        $this->view('users/index', [
            'users' => $users
        ]);
    }
    
    // GET /users/{id} - Mostrar usuario
    public function show($id)
    {
        Middleware::auth();
        
        // Un usuario solo puede ver su propio perfil a menos que sea admin
        if ($_SESSION['user']['id'] != $id && $_SESSION['user']['role'] != 'admin') {
            $_SESSION['error'] = 'No tienes permiso para ver este perfil';
            $this->redirect('/');
        }
        
        $user = User::findById($id);
        
        if (!$user) {
            $_SESSION['error'] = 'Usuario no encontrado';
            $this->redirect('/');
        }
        
        $this->view('users/show', [
            'user' => $user
        ]);
    }
    
    // GET /users/{id}/edit - Formulario para editar usuario
    public function edit($id)
    {
        Middleware::auth();
        
        // Un usuario solo puede editar su propio perfil a menos que sea admin
        if ($_SESSION['user']['id'] != $id && $_SESSION['user']['role'] != 'admin') {
            $_SESSION['error'] = 'No tienes permiso para editar este perfil';
            $this->redirect('/');
        }
        
        $user = User::findById($id);
        
        if (!$user) {
            $_SESSION['error'] = 'Usuario no encontrado';
            $this->redirect('/');
        }
        
        $this->view('users/edit', [
            'user' => $user
        ]);
    }
    
    // POST /users/{id}/update - Actualizar usuario
    public function update($id)
    {
        Middleware::auth();
        Csrf::verify();
        
        // Un usuario solo puede actualizar su propio perfil a menos que sea admin
        if ($_SESSION['user']['id'] != $id && $_SESSION['user']['role'] != 'admin') {
            $_SESSION['error'] = 'No tienes permiso para actualizar este perfil';
            $this->redirect('/');
        }
        
        $data = [
            'name' => $_POST['name'] ?? '',
            'email' => $_POST['email'] ?? ''
        ];
        
        // Si es admin o el usuario está cambiando su propia contraseña
        if (!empty($_POST['password']) && ($_SESSION['user']['role'] == 'admin' || $_SESSION['user']['id'] == $id)) {
            if ($_POST['password'] === $_POST['password_confirmation']) {
                $data['password'] = $_POST['password'];
            } else {
                $_SESSION['error'] = 'Las contraseñas no coinciden';
                $this->redirect("/users/{$id}/edit");
            }
        }
        
        // Solo admin puede cambiar el rol
        if ($_SESSION['user']['role'] == 'admin' && isset($_POST['role'])) {
            $data['role'] = $_POST['role'];
        }
        
        // Validación básica
        $errors = [];
        
        if (empty($data['name'])) {
            $errors[] = 'El nombre es obligatorio';
        }
        
        if (empty($data['email'])) {
            $errors[] = 'El email es obligatorio';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'El email no es válido';
        }
        
        // Verificar si el email ya existe en otro usuario
        $existingUser = User::findByEmail($data['email']);
        if ($existingUser && $existingUser->id != $id) {
            $errors[] = 'El email ya está registrado por otro usuario';
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            $this->redirect("/users/{$id}/edit");
        }
        
        if (User::update($id, $data)) {
            // Si el usuario actualizó su propio perfil, actualizar sesión
            if ($_SESSION['user']['id'] == $id) {
                $user = User::findById($id);
                $_SESSION['user']['name'] = $user->name;
                $_SESSION['user']['email'] = $user->email;
                if (isset($data['role'])) {
                    $_SESSION['user']['role'] = $data['role'];
                }
            }
            
            $_SESSION['success'] = 'Usuario actualizado exitosamente';
            $this->redirect("/users/{$id}");
        } else {
            $_SESSION['error'] = 'Error al actualizar el usuario';
            $this->redirect("/users/{$id}/edit");
        }
    }

    public function store()
    {
        \Core\Middleware::auth();
        \Core\Middleware::admin(); // Solo admin puede crear usuarios
        \Core\Csrf::verify();
        
        $data = [
            'name' => $_POST['name'] ?? '',
            'email' => $_POST['email'] ?? '',
            'password' => $_POST['password'] ?? '',
            'role' => $_POST['role'] ?? 'user'
        ];
        
        // Validación
        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            $_SESSION['error'] = 'Todos los campos son obligatorios';
            $this->redirect('/users');
        }
        
        if (\App\Models\User::emailExists($data['email'])) {
            $_SESSION['error'] = 'El email ya está registrado';
            $this->redirect('/users');
        }
        
        if (\App\Models\User::createWithHash($data)) {
            $_SESSION['success'] = 'Usuario creado exitosamente';
        } else {
            $_SESSION['error'] = 'Error al crear el usuario';
        }
        
        $this->redirect('/users');
    }
    
    // POST /users/{id}/delete - Eliminar usuario (marcar como inactivo)
    public function delete($id)
    {
        Middleware::auth();
        Middleware::admin(); // Solo administradores
        Csrf::verify();
        
        // No permitir eliminar el propio usuario
        if ($_SESSION['user']['id'] == $id) {
            $_SESSION['error'] = 'No puedes eliminar tu propia cuenta';
            $this->redirect('/users');
        }
        
        if (User::delete($id)) {
            $_SESSION['success'] = 'Usuario eliminado exitosamente';
        } else {
            $_SESSION['error'] = 'Error al eliminar el usuario';
        }
        
        $this->redirect('/users');
    }
    
    // GET /profile - Perfil del usuario actual
    public function profile()
    {
        Middleware::auth();
        
        $user = User::findById($_SESSION['user']['id']);
        
        $this->view('users/profile', [
            'user' => $user
        ]);
    }
}