<?php
namespace App\Models;

use Core\Model;

class User extends Model
{
    protected $table = 'user';
    
    /**
     * Autenticar usuario
     */
    public static function authenticate($email, $password)
    {
        $user = \R::findOne('user', 'email = ? AND active = 1', [$email]);
        
        if ($user && password_verify($password, $user->password)) {
            return $user;
        }
        
        return false;
    }

    public static function allOrdered()
    {
        return \R::findAll('user', ' ORDER BY created_at DESC');
    }
    
    /**
     * Verificar si existe email
     */
    public static function emailExists($email)
    {
        return \R::count('user', 'email = ?', [$email]) > 0;
    }
    
    /**
     * Crear usuario con hash de contraseña
     */
    public static function createWithHash($data)
    {
        try {
            $user = \R::dispense('user');
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->password = password_hash($data['password'], PASSWORD_DEFAULT);
            $user->role = $data['role'] ?? 'user';
            $user->active = 1;
            
            \R::store($user);
            return $user->id;
            
        } catch (\Exception $e) {
            error_log('Error al crear usuario: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Convertir usuario a array de sesión
     */
    public static function toSessionArray($user)
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role
        ];
    }
    
    /**
     * Encontrar usuario por ID
     */
    public static function findById($id)
    {
        return \R::findOne('user', 'id = ?', [$id]);
    }
    
    /**
     * Encontrar usuario por email
     */
    public static function findByEmail($email)
    {
        return \R::findOne('user', 'email = ?', [$email]);
    }
    
    /**
     * Obtener todos los usuarios
     */
    public static function getAll($limit = 100)
    {
        return \R::findAll('user', 'ORDER BY created_at DESC LIMIT ?', [$limit]);
    }
    
    /**
     * Actualizar usuario
     */
    public static function update($id, $data)
    {
        try {
            $user = self::findById($id);
            
            if (!$user) {
                return false;
            }
            
            if (isset($data['name'])) {
                $user->name = $data['name'];
            }
            
            if (isset($data['email'])) {
                $user->email = $data['email'];
            }
            
            if (isset($data['password']) && !empty($data['password'])) {
                $user->password = password_hash($data['password'], PASSWORD_DEFAULT);
            }
            
            if (isset($data['role'])) {
                $user->role = $data['role'];
            }
            
            \R::store($user);
            return true;
            
        } catch (\Exception $e) {
            error_log('Error al actualizar usuario: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Eliminar usuario (soft delete)
     */
    public static function delete($id)
    {
        try {
            $user = self::findById($id);
            
            if ($user) {
                $user->active = 0;
                \R::store($user);
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            error_log('Error al eliminar usuario: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verificar si usuario es admin
     */
    public static function isAdmin($userId)
    {
        $user = self::findById($userId);
        return $user && $user->role === 'admin';
    }
    
    /**
     * Cambiar contraseña
     */
    public static function changePassword($userId, $newPassword)
    {
        try {
            $user = self::findById($userId);
            
            if (!$user) {
                return false;
            }
            
            $user->password = password_hash($newPassword, PASSWORD_DEFAULT);
            \R::store($user);
            return true;
            
        } catch (\Exception $e) {
            error_log('Error al cambiar contraseña: ' . $e->getMessage());
            return false;
        }
    }
}