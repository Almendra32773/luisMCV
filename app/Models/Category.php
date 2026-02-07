<?php
namespace App\Models;

use Core\Model;

class Category extends Model
{
    protected $table = 'category';
    
    /**
     * Obtener todas las categorías
     */
    public static function getAll()
    {
        return \R::findAll('category', 'ORDER BY name');
    }
    
    /**
     * Obtener categorías con estadísticas
     */
    public static function getAllWithStats()
    {
        $sql = "
            SELECT c.*, 
                   COUNT(DISTINCT bc.isbn) as book_count
            FROM category c
            LEFT JOIN book_category bc ON c.id = bc.category_id
            LEFT JOIN book b ON bc.isbn = b.isbn AND b.active = 1
            GROUP BY c.id
            ORDER BY c.name
        ";
        
        return \R::getAll($sql);
    }
    
    /**
     * Encontrar categoría por ID
     */
    public static function find($id)
    {
        return \R::findOne('category', 'id = ?', [$id]);
    }
    
    /**
     * Encontrar categoría por nombre
     */
    public static function findByName($name)
    {
        return \R::findOne('category', 'name = ?', [$name]);
    }
    
    /**
     * Verificar si existe categoría
     */
    public static function exists($name)
    {
        return \R::count('category', 'name = ?', [$name]) > 0;
    }
    
    /**
     * Contar libros en una categoría
     */
    public static function getBookCount($categoryId)
    {
        $sql = "
            SELECT COUNT(DISTINCT bc.isbn) as count
            FROM book_category bc
            JOIN book b ON bc.isbn = b.isbn AND b.active = 1
            WHERE bc.category_id = ?
        ";
        
        $result = \R::getRow($sql, [$categoryId]);
        return $result['count'] ?? 0;
    }
    
    /**
     * Crear nueva categoría
     */
    public static function create($data)
    {
        try {
            $category = \R::dispense('category');
            $category->name = $data['name'];
            $category->description = $data['description'] ?? null;
            
            \R::store($category);
            return $category->id;
            
        } catch (\Exception $e) {
            error_log('Error al crear categoría: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Actualizar categoría
     */
    public static function update($id, $data)
    {
        try {
            $category = \R::findOne('category', 'id = ?', [$id]);
            if (!$category) return false;
            
            $category->name = $data['name'];
            $category->description = $data['description'] ?? null;
            
            \R::store($category);
            return true;
            
        } catch (\Exception $e) {
            error_log('Error al actualizar categoría: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Eliminar categoría
     */
    public static function delete($id)
    {
        try {
            $category = \R::findOne('category', 'id = ?', [$id]);
            if ($category) {
                \R::trash($category);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            error_log('Error al eliminar categoría: ' . $e->getMessage());
            return false;
        }
    }
}