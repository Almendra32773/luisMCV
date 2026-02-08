<?php
namespace App\Models;

use Core\Model;

class Copy extends Model
{
    protected $table = 'copies';
    
    /**
     * Obtener todas las copias de un libro
     */
    public static function getByBook($isbn)
    {
        return \R::findAll('copies', 
            'isbn = ? AND active = 1 ORDER BY copy_code', 
            [$isbn]);
    }
    
    /**
     * Obtener copias disponibles de un libro
     */
    public static function getAvailableByBook($isbn)
    {
        return \R::findAll('copies', 
            'isbn = ? AND status = ? AND active = 1 ORDER BY copy_code', 
            [$isbn, 'available']);
    }
    
    /**
     * Encontrar copia por ID
     */
    public static function find($id)
    {
        return \R::findOne('copies', 'id = ?', [$id]);
    }
    
    /**
     * Encontrar copia por código
     */
    public static function findByCode($copyCode)
    {
        return \R::findOne('copies', 'copy_code = ? AND active = 1', [$copyCode]);
    }
    
    /**
     * Crear nueva copia
     */
    public static function create($isbn, $copyCode = null)
    {
        try {
            $copy = \R::dispense('copies');
            $copy->isbn = $isbn;
            
            if ($copyCode) {
                $copy->copy_code = $copyCode;
            } else {
                // Generar código automático
                $lastCopy = \R::findOne('copies', 
                    'isbn = ? ORDER BY copy_code DESC', 
                    [$isbn]);
                
                if ($lastCopy) {
                    $lastNumber = intval(substr($lastCopy->copy_code, -3));
                    $copy->copy_code = 'COPY-' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
                } else {
                    $copy->copy_code = 'COPY-001';
                }
            }
            
            $copy->status = 'available';
            $copy->location = 'General Shelf';
            $copy->active = 1;
            
            \R::store($copy);
            return $copy->id;
            
        } catch (\Exception $e) {
            error_log('Error al crear copia: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Actualizar estado de copia
     */
    public static function updateStatus($id, $status)
    {
        try {
            $copy = self::find($id);
            if (!$copy) return false;
            
            $copy->status = $status;
            \R::store($copy);
            return true;
            
        } catch (\Exception $e) {
            error_log('Error al actualizar estado de copia: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Actualizar ubicación de copia
     */
    public static function updateLocation($id, $location)
    {
        try {
            $copy = self::find($id);
            if (!$copy) return false;
            
            $copy->location = $location;
            \R::store($copy);
            return true;
            
        } catch (\Exception $e) {
            error_log('Error al actualizar ubicación de copia: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Eliminar copia (soft delete)
     */
    public static function delete($id)
    {
        try {
            $copy = self::find($id);
            if ($copy) {
                $copy->active = 0;
                \R::store($copy);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            error_log('Error al eliminar copia: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verificar si copia está disponible
     */
    public static function isAvailable($id)
    {
        $copy = self::find($id);
        return $copy && $copy->status === 'available' && $copy->active === 1;
    }
    
    /**
     * Obtener estadísticas de copias
     */
    public static function getStats()
    {
        $stats = [
            'total' => 0,
            'available' => 0,
            'borrowed' => 0,
            'maintenance' => 0,
            'lost' => 0
        ];
        
        $sql = "
            SELECT status, COUNT(*) as count
            FROM copies
            WHERE active = 1
            GROUP BY status
        ";
        
        $results = \R::getAll($sql);
        
        foreach ($results as $row) {
            $stats[strtolower($row['status'])] = $row['count'];
            $stats['total'] += $row['count'];
        }
        
        return $stats;
    }
}