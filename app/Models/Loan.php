<?php
namespace App\Models;

use Core\Model;
use RedBeanPHP\R;

class Loan extends Model
{
    protected static string $table = 'loans'; // Cambiado a estática y tipada para coincidir con la clase base
    
    // ============================================
    // MÉTODOS DE CONSULTA
    // ============================================
    
    /**
     * Obtener todos los préstamos con filtros
     */
    public static function getAll($status = '', $dateFrom = '', $dateTo = '', $limit = 20, $offset = 0)
    {
        $where = '1=1';
        $params = [];
        
        if ($status) {
            $where .= ' AND l.status = ?';
            $params[] = $status;
        }
        
        if ($dateFrom) {
            $where .= ' AND l.loan_date >= ?';
            $params[] = $dateFrom;
        }
        
        if ($dateTo) {
            $where .= ' AND l.loan_date <= ?';
            $params[] = $dateTo;
        }
        
        $sql = "
            SELECT l.*, 
                   m.first_name, m.last_name, m.member_code, m.id as member_id,
                   b.title, b.author, b.isbn,
                   c.copy_code
            FROM loans l
            JOIN members m ON l.member_id = m.id
            JOIN copies c ON l.copy_id = c.id
            JOIN books b ON c.isbn = b.isbn
            WHERE {$where}
            ORDER BY l.loan_date DESC, l.id DESC
            LIMIT ? OFFSET ?
        ";
        
        $params[] = $limit;
        $params[] = $offset;
        
        return R::getAll($sql, $params);
    }
    
    /**
     * Contar total de préstamos
     */
    public static function count($status = '', $dateFrom = '', $dateTo = '')
    {
        $where = '1=1';
        $params = [];
        
        if ($status) {
            $where .= ' AND status = ?';
            $params[] = $status;
        }
        
        if ($dateFrom) {
            $where .= ' AND loan_date >= ?';
            $params[] = $dateFrom;
        }
        
        if ($dateTo) {
            $where .= ' AND loan_date <= ?';
            $params[] = $dateTo;
        }
        
        return R::count('loans', $where, $params);
    }
    
    /**
     * Obtener préstamos recientes
     */
    public static function getRecent($limit = 5)
    {
        $sql = "
            SELECT l.*, 
                   m.first_name, m.last_name,
                   b.title
            FROM loans l
            JOIN members m ON l.member_id = m.id
            JOIN copies c ON l.copy_id = c.id
            JOIN books b ON c.isbn = b.isbn
            WHERE l.status = 'active'
            ORDER BY l.loan_date DESC
            LIMIT ?
        ";
        
        return R::getAll($sql, [$limit]);
    }
    
    /**
     * Obtener préstamos vencidos
     */
    public static function getOverdue($limit = 20, $offset = 0)
    {
        $sql = "
            SELECT l.*, 
                   m.first_name, m.last_name, m.member_code,
                   b.title, b.author, b.isbn,
                   c.copy_code,
                   DATEDIFF(CURDATE(), l.due_date) as days_overdue
            FROM loans l
            JOIN members m ON l.member_id = m.id
            JOIN copies c ON l.copy_id = c.id
            JOIN books b ON c.isbn = b.isbn
            WHERE l.status = 'active' AND l.due_date < CURDATE()
            ORDER BY l.due_date ASC
            LIMIT ? OFFSET ?
        ";
        
        return R::getAll($sql, [$limit, $offset]);
    }
    
    /**
     * Contar préstamos vencidos
     */
    public static function countOverdue()
    {
        return R::count('loans', 
            'status = ? AND due_date < CURDATE()', 
            ['active']);
    }
    
    /**
     * Obtener préstamos vencidos recientes
     */
    public static function getRecentOverdue($limit = 5)
    {
        $sql = "
            SELECT l.*, 
                   m.first_name, m.last_name, m.member_code,
                   b.title
            FROM loans l
            JOIN members m ON l.member_id = m.id
            JOIN copies c ON l.copy_id = c.id
            JOIN books b ON c.isbn = b.isbn
            WHERE l.status = 'active' AND l.due_date < CURDATE()
            ORDER BY l.due_date ASC
            LIMIT ?
        ";
        
        return R::getAll($sql, [$limit]);
    }
    
    /**
     * Encontrar préstamo por ID
     */
    public static function find($id)
    {
        $sql = "
            SELECT l.*, 
                   m.first_name, m.last_name, m.member_code, m.email, m.phone,
                   b.title, b.author, b.isbn,
                   c.copy_code
            FROM loans l
            JOIN members m ON l.member_id = m.id
            JOIN copies c ON l.copy_id = c.id
            JOIN books b ON c.isbn = b.isbn
            WHERE l.id = ?
        ";
        
        return R::getRow($sql, [$id]);
    }
    
    /**
     * Verificar si un préstamo está vencido
     */
    public static function isOverdue($loanId)
    {
        $loan = R::findOne('loans', 'id = ? AND status = ?', [$loanId, 'active']);
        if ($loan && strtotime($loan->due_date) < time()) {
            return true;
        }
        return false;
    }
    
    // ============================================
    // MÉTODOS DE CREACIÓN/ACTUALIZACIÓN
    // ============================================
    
    /**
     * Crear nuevo préstamo
     */
    public static function create($data)
    {
        try {
            R::begin();
            
            // Calcular fecha de vencimiento
            $loanDate = new \DateTime($data['loan_date']);
            $dueDate = clone $loanDate;
            $dueDate->modify('+' . $data['loan_days'] . ' days');
            
            // Crear préstamo
            $loan = R::dispense('loans');
            $loan->member_id = $data['member_id'];
            $loan->copy_id = $data['copy_id'];
            $loan->loan_date = $loanDate->format('Y-m-d');
            $loan->due_date = $dueDate->format('Y-m-d');
            $loan->loan_days = $data['loan_days'];
            $loan->status = 'active';
            $loan->fine = 0.00;
            $loan->notes = $data['notes'] ?? null;
            
            R::store($loan);
            
            // Actualizar estado de la copia
            $copy = R::findOne('copies', 'id = ?', [$data['copy_id']]);
            if ($copy) {
                $copy->status = 'borrowed';
                R::store($copy);
            }
            
            // Actualizar contador de copias disponibles del libro
            $book = R::findOne('books', 'isbn = ?', [$copy->isbn]);
            if ($book && $book->available_copies > 0) {
                $book->available_copies--;
                R::store($book);
            }
            
            R::commit();
            return $loan->id;
            
        } catch (\Exception $e) {
            R::rollback();
            error_log('Error al crear préstamo: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Marcar préstamo como devuelto
     */
    public static function returnLoan($loanId, $fine = 0)
    {
        try {
            R::begin();
            
            $loan = R::findOne('loans', 'id = ? AND status = ?', [$loanId, 'active']);
            if (!$loan) {
                throw new \Exception('Préstamo no encontrado o ya devuelto');
            }
            
            // Actualizar préstamo
            $loan->return_date = date('Y-m-d');
            $loan->status = 'returned';
            $loan->fine = $fine;
            
            R::store($loan);
            
            // Actualizar estado de la copia
            $copy = R::findOne('copies', 'id = ?', [$loan->copy_id]);
            if ($copy) {
                $copy->status = 'available';
                R::store($copy);
            }
            
            // Actualizar contador de copias disponibles del libro
            $book = R::findOne('books', 'isbn = ?', [$copy->isbn]);
            if ($book) {
                $book->available_copies++;
                R::store($book);
            }
            
            R::commit();
            return true;
            
        } catch (\Exception $e) {
            R::rollback();
            error_log('Error al devolver préstamo: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Renovar préstamo
     */
    public static function renew($loanId, $additionalDays = 15)
    {
        try {
            $loan = R::findOne('loans', 'id = ? AND status = ?', [$loanId, 'active']);
            if (!$loan) {
                throw new \Exception('Préstamo no encontrado o no activo');
            }
            
            // Verificar si ya está vencido
            if (strtotime($loan->due_date) < time()) {
                throw new \Exception('No se puede renovar un préstamo vencido');
            }
            
            // Extender fecha de vencimiento
            $dueDate = new \DateTime($loan->due_date);
            $dueDate->modify('+' . $additionalDays . ' days');
            
            $loan->due_date = $dueDate->format('Y-m-d');
            $loan->loan_days += $additionalDays;
            $loan->notes = ($loan->notes ? $loan->notes . "\n" : '') . 
                          "Renovado el " . date('Y-m-d') . " por {$additionalDays} días adicionales.";
            
            R::store($loan);
            return true;
            
        } catch (\Exception $e) {
            error_log('Error al renovar préstamo: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Aplicar multa a un préstamo
     */
    public static function applyFine($loanId, $amount, $reason = '')
    {
        try {
            $loan = R::findOne('loans', 'id = ?', [$loanId]);
            if (!$loan) {
                throw new \Exception('Préstamo no encontrado');
            }
            
            $loan->fine = $amount;
            $loan->notes = ($loan->notes ? $loan->notes . "\n" : '') . 
                          "Multa aplicada: {$reason} - \${$amount} - " . date('Y-m-d H:i:s');
            
            R::store($loan);
            return true;
            
        } catch (\Exception $e) {
            error_log('Error al aplicar multa: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Marcar libro como perdido
     */
    public static function markAsLost($loanId)
    {
        try {
            R::begin();
            
            $loan = R::findOne('loans', 'id = ? AND status = ?', [$loanId, 'active']);
            if (!$loan) {
                throw new \Exception('Préstamo no encontrado o no activo');
            }
            
            // Actualizar préstamo
            $loan->status = 'lost';
            $loan->return_date = date('Y-m-d');
            $loan->fine = 50.00; // Multa por libro perdido
            
            R::store($loan);
            
            // Actualizar estado de la copia
            $copy = R::findOne('copies', 'id = ?', [$loan->copy_id]);
            if ($copy) {
                $copy->status = 'lost';
                R::store($copy);
            }
            
            // Actualizar contador de copias del libro
            $book = R::findOne('books', 'isbn = ?', [$copy->isbn]);
            if ($book) {
                $book->available_copies--;
                $book->total_copies--;
                R::store($book);
            }
            
            R::commit();
            return true;
            
        } catch (\Exception $e) {
            R::rollback();
            error_log('Error al marcar como perdido: ' . $e->getMessage());
            return false;
        }
    }
    
    // ============================================
    // ESTADÍSTICAS
    // ============================================
    
    /**
     * Obtener estadísticas de préstamos
     */
    public static function getStats()
    {
        $stats = [
            'total_loans' => 0,
            'active_loans' => 0,
            'overdue_loans' => 0,
            'total_fines' => 0
        ];
        
        // Total préstamos
        $stats['total_loans'] = R::count('loans');
        
        // Préstamos activos
        $stats['active_loans'] = R::count('loans', 'status = ?', ['active']);
        
        // Préstamos vencidos
        $stats['overdue_loans'] = self::countOverdue();
        
        // Multas totales pendientes
        $sql = "
            SELECT SUM(fine) as total
            FROM loans
            WHERE status = 'active' AND fine > 0
        ";
        $result = R::getRow($sql);
        $stats['total_fines'] = $result['total'] ?? 0;
        
        return $stats;
    }
    
    /**
     * Obtener estadísticas mensuales
     */
    public static function getMonthlyStats($year = null, $month = null)
    {
        if (!$year) $year = date('Y');
        if (!$month) $month = date('m');
        
        $startDate = "{$year}-{$month}-01";
        $endDate = date('Y-m-t', strtotime($startDate));
        
        $stats = [
            'total_loans' => 0,
            'returns' => 0,
            'active' => 0,
            'overdue' => 0
        ];
        
        // Total préstamos del mes
        $stats['total_loans'] = R::count('loans', 
            'loan_date >= ? AND loan_date <= ?', 
            [$startDate, $endDate]);
        
        // Devoluciones del mes
        $stats['returns'] = R::count('loans', 
            'return_date >= ? AND return_date <= ? AND status = ?', 
            [$startDate, $endDate, 'returned']);
        
        // Activos al final del mes
        $stats['active'] = R::count('loans', 
            'status = ? AND (loan_date <= ? AND (return_date IS NULL OR return_date > ?))', 
            ['active', $endDate, $endDate]);
        
        // Vencidos al final del mes
        $sql = "
            SELECT COUNT(*) as count
            FROM loans
            WHERE status = 'active' AND due_date <= ?
        ";
        $result = R::getRow($sql, [$endDate]);
        $stats['overdue'] = $result['count'] ?? 0;
        
        return $stats;
    }
}