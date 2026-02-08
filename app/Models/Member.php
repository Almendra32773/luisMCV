<?php
namespace App\Models;

use Core\Model;
use RedBeanPHP\R;

class Member extends Model
{
    protected static string $table = 'members'; // Cambiado a estática y tipada para coincidir con la clase base
    
    // ============================================
    // MÉTODOS DE CONSULTA
    // ============================================
    
    /**
     * Obtener todos los socios con paginación
     */
    public static function getAll($search = '', $status = 'active', $limit = 15, $offset = 0)
    {
        $where = '1=1';
        $params = [];
        
        if ($status === 'active') {
            $where .= ' AND active = 1';
        } elseif ($status === 'inactive') {
            $where .= ' AND active = 0';
        }
        
        if ($search) {
            $where .= ' AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR member_code LIKE ?)';
            $searchTerm = "%{$search}%";
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        }
        
        $sql = "
            SELECT m.*, 
                   (SELECT COUNT(*) FROM loans l WHERE l.member_id = m.id AND l.status = 'active') as active_loans
            FROM members m
            WHERE {$where}
            ORDER BY m.first_name, m.last_name
            LIMIT ? OFFSET ?
        ";
        
        $params[] = $limit;
        $params[] = $offset;
        
        return R::getAll($sql, $params);
    }
    
    /**
     * Contar total de socios
     */
    public static function count($search = '', $status = 'active')
    {
        $where = '1=1';
        $params = [];
        
        if ($status === 'active') {
            $where .= ' AND active = 1';
        } elseif ($status === 'inactive') {
            $where .= ' AND active = 0';
        }
        
        if ($search) {
            $where .= ' AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR member_code LIKE ?)';
            $searchTerm = "%{$search}%";
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        }
        
        return R::count('members', $where, $params);
    }
    
    /**
     * Buscar socios (para autocompletado)
     */
    public static function search($query, $limit = 10)
    {
        $sql = "
            SELECT m.*, 
                   (SELECT COUNT(*) FROM loans l WHERE l.member_id = m.id AND l.status = 'active') as active_loans
            FROM members m
            WHERE m.active = 1 AND 
                  (m.first_name LIKE ? OR m.last_name LIKE ? OR m.email LIKE ? OR m.member_code LIKE ?)
            ORDER BY m.first_name, m.last_name
            LIMIT ?
        ";
        
        $searchTerm = "%{$query}%";
        return R::getAll($sql, [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $limit]);
    }
    
    /**
     * Obtener socios para formulario de préstamo
     */
    public static function getAllForLoan()
    {
        $sql = "
            SELECT m.*, 
                   (SELECT COUNT(*) FROM loans l WHERE l.member_id = m.id AND l.status = 'active') as active_loans
            FROM members m
            WHERE m.active = 1
            ORDER BY m.first_name, m.last_name
        ";
        
        return R::getAll($sql);
    }
    
    /**
     * Encontrar socio por ID
     */
    public static function find($id)
    {
        return R::findOne('members', 'id = ?', [$id]);
    }
    
    /**
     * Encontrar socio por email
     */
    public static function findByEmail($email)
    {
        return R::findOne('members', 'email = ?', [$email]);
    }
    
    /**
     * Verificar si existe email
     */
    public static function emailExists($email)
    {
        return R::count('members', 'email = ?', [$email]) > 0;
    }
    
    /**
     * Generar código único de socio
     */
    public static function generateMemberCode($firstName, $lastName)
    {
        $initials = strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));
        $timestamp = time();
        
        do {
            $memberCode = 'MEM' . $initials . substr($timestamp, -4);
            $exists = R::count('members', 'member_code = ?', [$memberCode]);
            $timestamp++;
        } while ($exists > 0);
        
        return $memberCode;
    }
    
    // ============================================
    // MÉTODOS DE PRÉSTAMOS
    // ============================================
    
    /**
     * Obtener préstamos activos de un socio
     */
    public static function getActiveLoans($memberId)
    {
        $sql = "
            SELECT l.*, b.title, b.author, b.isbn, c.copy_code
            FROM loans l
            JOIN copies c ON l.copy_id = c.id
            JOIN books b ON c.isbn = b.isbn
            WHERE l.member_id = ? AND l.status = 'active'
            ORDER BY l.due_date
        ";
        
        return R::getAll($sql, [$memberId]);
    }
    
    /**
     * Obtener historial de préstamos
     */
    public static function getLoanHistory($memberId, $limit = 10)
    {
        $sql = "
            SELECT l.*, b.title, b.author, c.copy_code
            FROM loans l
            JOIN copies c ON l.copy_id = c.id
            JOIN books b ON c.isbn = b.isbn
            WHERE l.member_id = ?
            ORDER BY l.loan_date DESC
            LIMIT ?
        ";
        
        return R::getAll($sql, [$memberId, $limit]);
    }
    
    /**
     * Verificar si un socio puede tomar más préstamos
     */
    public static function canBorrow($memberId)
    {
        $member = self::find($memberId);
        if (!$member) return false;
        
        $activeLoans = R::count('loans', 
            'member_id = ? AND status = ?', 
            [$memberId, 'active']);
        
        return $activeLoans < $member->max_loans;
    }
    
    /**
     * Contar préstamos activos de un socio
     */
    public static function countActiveLoans($memberId)
    {
        return R::count('loans', 
            'member_id = ? AND status = ?', 
            [$memberId, 'active']);
    }
    
    /**
     * Contar préstamos vencidos de un socio
     */
    public static function countOverdueLoans($memberId)
    {
        $sql = "
            SELECT COUNT(*) as count
            FROM loans
            WHERE member_id = ? AND status = 'active' AND due_date < CURDATE()
        ";
        
        $result = R::getRow($sql, [$memberId]);
        return $result['count'] ?? 0;
    }
    
    // ============================================
    // MÉTODOS DE CREACIÓN/ACTUALIZACIÓN
    // ============================================
    
    /**
     * Crear nuevo socio
     */
    public static function create($data)
    {
        try {
            $member = R::dispense('members');
            
            // Generar código de socio
            $memberCode = self::generateMemberCode($data['first_name'], $data['last_name']);
            
            $member->member_code = $memberCode;
            $member->first_name = $data['first_name'];
            $member->last_name = $data['last_name'];
            $member->email = $data['email'];
            $member->phone = $data['phone'] ?? null;
            $member->address = $data['address'] ?? null;
            $member->birth_date = $data['birth_date'] ?: null;
            $member->max_loans = $data['max_loans'] ?? 5;
            $member->notes = $data['notes'] ?? null;
            $member->active = 1;
            
            R::store($member);
            return $member->id;
            
        } catch (\Exception $e) {
            error_log('Error al crear socio: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Actualizar socio
     */
    public static function update($id, $data)
    {
        try {
            $member = R::findOne('members', 'id = ?', [$id]);
            if (!$member) return false;
            
            if (isset($data['first_name'])) $member->first_name = $data['first_name'];
            if (isset($data['last_name'])) $member->last_name = $data['last_name'];
            if (isset($data['email'])) $member->email = $data['email'];
            if (isset($data['phone'])) $member->phone = $data['phone'];
            if (isset($data['address'])) $member->address = $data['address'];
            if (isset($data['birth_date'])) $member->birth_date = $data['birth_date'] ?: null;
            if (isset($data['max_loans'])) $member->max_loans = $data['max_loans'];
            if (isset($data['notes'])) $member->notes = $data['notes'];
            if (isset($data['active'])) $member->active = $data['active'];
            
            R::store($member);
            return true;
            
        } catch (\Exception $e) {
            error_log('Error al actualizar socio: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Eliminar socio (soft delete)
     */
    public static function softDelete($id, $column = 'active')
    {
        try {
            $member = R::findOne('members', 'id = ?', [$id]);
            if ($member) {
                $member->$column = 0;
                R::store($member);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            error_log('Error al eliminar socio: ' . $e->getMessage());
            return false;
        }
    }
    
    // ============================================
    // ESTADÍSTICAS
    // ============================================
    
    /**
     * Obtener estadísticas de socios
     */
    public static function getStats()
    {
        $stats = [
            'total_members' => 0,
            'active_members' => 0,
            'members_with_loans' => 0,
            'avg_loans_per_member' => 0
        ];
        
        // Total de socios
        $stats['total_members'] = R::count('members');
        $stats['active_members'] = R::count('members', 'active = 1');
        
        // Socios con préstamos activos
        $sql = "
            SELECT COUNT(DISTINCT member_id) as count
            FROM loans 
            WHERE status = 'active'
        ";
        $result = R::getRow($sql);
        $stats['members_with_loans'] = $result['count'] ?? 0;
        
        // Promedio de préstamos por socio activo
        if ($stats['active_members'] > 0) {
            $sql = "
                SELECT AVG(loan_count) as avg
                FROM (
                    SELECT COUNT(*) as loan_count
                    FROM loans l
                    JOIN members m ON l.member_id = m.id
                    WHERE m.active = 1
                    GROUP BY l.member_id
                ) as subquery
            ";
            $result = R::getRow($sql);
            $stats['avg_loans_per_member'] = number_format($result['avg'] ?? 0, 1);
        }
        
        return $stats;
    }
    
    /**
     * Obtener estadísticas de un socio específico
     */
    public static function getMemberStats($memberId)
    {
        $stats = [
            'active_loans' => 0,
            'total_loans' => 0,
            'overdue_loans' => 0,
            'total_fines' => 0
        ];
        
        // Préstamos activos
        $stats['active_loans'] = self::countActiveLoans($memberId);
        
        // Total préstamos
        $stats['total_loans'] = R::count('loans', 'member_id = ?', [$memberId]);
        
        // Préstamos vencidos
        $stats['overdue_loans'] = self::countOverdueLoans($memberId);
        
        // Multas totales
        $sql = "
            SELECT SUM(fine) as total
            FROM loans
            WHERE member_id = ? AND fine > 0
        ";
        $result = R::getRow($sql, [$memberId]);
        $stats['total_fines'] = $result['total'] ?? 0;
        
        return $stats;
    }
}