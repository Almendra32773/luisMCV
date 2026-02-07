<?php
namespace App\Models;

use Core\Model;

class Book extends Model
{
    protected $table = 'book';
    
    // ============================================
    // MÉTODOS DE CONSULTA
    // ============================================
    
    /**
     * Obtener todos los libros con paginación
     */
    public static function getAll($limit = 20, $offset = 0)
    {
        $sql = "
            SELECT b.*, 
                   GROUP_CONCAT(DISTINCT c.name SEPARATOR ', ') as categories,
                   COUNT(DISTINCT cp.id) as total_copies_count,
                   SUM(CASE WHEN cp.status = 'available' THEN 1 ELSE 0 END) as available_copies_count
            FROM book b
            LEFT JOIN book_category bc ON b.isbn = bc.isbn
            LEFT JOIN category c ON bc.category_id = c.id
            LEFT JOIN copy cp ON b.isbn = cp.isbn AND cp.active = 1
            WHERE b.active = 1
            GROUP BY b.isbn
            ORDER BY b.title
            LIMIT ? OFFSET ?
        ";
        
        return \R::getAll($sql, [$limit, $offset]);
    }
    
    /**
     * Buscar libros
     */
    public static function search($query, $limit = 20, $offset = 0)
    {
        $sql = "
            SELECT b.*, 
                   GROUP_CONCAT(DISTINCT c.name SEPARATOR ', ') as categories,
                   COUNT(DISTINCT cp.id) as total_copies_count,
                   SUM(CASE WHEN cp.status = 'available' THEN 1 ELSE 0 END) as available_copies_count
            FROM book b
            LEFT JOIN book_category bc ON b.isbn = bc.isbn
            LEFT JOIN category c ON bc.category_id = c.id
            LEFT JOIN copy cp ON b.isbn = cp.isbn AND cp.active = 1
            WHERE b.active = 1 AND 
                  (b.title LIKE ? OR b.author LIKE ? OR b.isbn LIKE ?)
            GROUP BY b.isbn
            ORDER BY b.title
            LIMIT ? OFFSET ?
        ";
        
        $searchTerm = "%{$query}%";
        return \R::getAll($sql, [$searchTerm, $searchTerm, $searchTerm, $limit, $offset]);
    }
    
    /**
     * Contar total de libros
     */
    public static function count($search = '')
    {
        if ($search) {
            $searchTerm = "%{$search}%";
            return \R::count('book', 
                'active = 1 AND (title LIKE ? OR author LIKE ? OR isbn LIKE ?)', 
                [$searchTerm, $searchTerm, $searchTerm]);
        }
        
        return \R::count('book', 'active = 1');
    }
    
    /**
     * Contar libros en búsqueda
     */
    public static function countSearch($query)
    {
        $searchTerm = "%{$query}%";
        return \R::count('book', 
            'active = 1 AND (title LIKE ? OR author LIKE ? OR isbn LIKE ?)', 
            [$searchTerm, $searchTerm, $searchTerm]);
    }
    
    /**
     * Encontrar libro por ISBN
     */
    public static function find($isbn)
    {
        $sql = "
            SELECT b.*, 
                   GROUP_CONCAT(DISTINCT c.id) as category_ids,
                   GROUP_CONCAT(DISTINCT c.name SEPARATOR ', ') as categories,
                   COUNT(DISTINCT cp.id) as total_copies_count,
                   SUM(CASE WHEN cp.status = 'available' THEN 1 ELSE 0 END) as available_copies_count
            FROM book b
            LEFT JOIN book_category bc ON b.isbn = bc.isbn
            LEFT JOIN category c ON bc.category_id = c.id
            LEFT JOIN copy cp ON b.isbn = cp.isbn AND cp.active = 1
            WHERE b.isbn = ?
            GROUP BY b.isbn
        ";
        
        return \R::getRow($sql, [$isbn]);
    }
    
    /**
     * Verificar si existe un libro por ISBN
     */
    public static function exists($isbn)
    {
        return \R::count('book', 'isbn = ? AND active = 1', [$isbn]) > 0;
    }
    
    /**
     * Obtener libros recientes
     */
    public static function getRecent($limit = 5)
    {
        $sql = "
            SELECT b.* 
            FROM book b 
            WHERE b.active = 1 
            ORDER BY b.created_at DESC 
            LIMIT ?
        ";
        
        return \R::getAll($sql, [$limit]);
    }
    
    // ============================================
    // MÉTODOS DE COPIA
    // ============================================
    
    /**
     * Obtener copias de un libro
     */
    public static function getCopies($isbn)
    {
        return \R::findAll('copy', 
            'isbn = ? AND active = 1 ORDER BY copy_code', 
            [$isbn]);
    }
    
    /**
     * Obtener copias disponibles de un libro
     */
    public static function getAvailableCopies($isbn)
    {
        return \R::findAll('copy', 
            'isbn = ? AND status = ? AND active = 1 ORDER BY copy_code', 
            [$isbn, 'available']);
    }
    
    /**
     * Verificar si una copia está disponible
     */
    public static function isCopyAvailable($copyId)
    {
        $copy = \R::findOne('copy', 
            'id = ? AND status = ? AND active = 1', 
            [$copyId, 'available']);
        return $copy ? true : false;
    }
    
    /**
     * Actualizar estado de una copia
     */
    public static function updateCopyStatus($copyId, $status)
    {
        $copy = \R::findOne('copy', 'id = ?', [$copyId]);
        if ($copy) {
            $copy->status = $status;
            \R::store($copy);
            return true;
        }
        return false;
    }
    
    // ============================================
    // MÉTODOS DE PRÉSTAMOS
    // ============================================
    
    /**
     * Obtener préstamos activos de un libro
     */
    public static function getActiveLoans($isbn)
    {
        $sql = "
            SELECT l.*, m.first_name, m.last_name, m.member_code, m.id as member_id
            FROM loan l
            JOIN member m ON l.member_id = m.id
            JOIN copy c ON l.copy_id = c.id
            WHERE c.isbn = ? AND l.status = 'active'
            ORDER BY l.due_date
        ";
        
        return \R::getAll($sql, [$isbn]);
    }
    
    /**
     * Contar préstamos activos de un libro
     */
    public static function countActiveLoans($isbn)
    {
        $sql = "
            SELECT COUNT(*) as count
            FROM loan l
            JOIN copy c ON l.copy_id = c.id
            WHERE c.isbn = ? AND l.status = 'active'
        ";
        
        $result = \R::getRow($sql, [$isbn]);
        return $result['count'] ?? 0;
    }
    
    // ============================================
    // MÉTODOS DE CREACIÓN/ACTUALIZACIÓN
    // ============================================
    
    /**
     * Crear nuevo libro
     */
    public static function create($data)
    {
        try {
            \R::begin();
            
            // Crear libro
            $book = \R::dispense('book');
            $book->isbn = $data['isbn'];
            $book->title = $data['title'];
            $book->author = $data['author'];
            $book->publisher = $data['publisher'];
            $book->publication_year = $data['publication_year'] ?? null;
            $book->pages = $data['pages'] ?? null;
            $book->synopsis = $data['synopsis'] ?? '';
            $book->language = $data['language'] ?? 'Español';
            $book->available_copies = $data['total_copies'];
            $book->total_copies = $data['total_copies'];
            $book->active = 1;
            
            \R::store($book);
            
            // Asociar categorías
            if (!empty($data['categories'])) {
                foreach ($data['categories'] as $categoryId) {
                    \R::exec(
                        "INSERT INTO book_category (isbn, category_id) VALUES (?, ?)",
                        [$data['isbn'], $categoryId]
                    );
                }
            }
            
            // Crear copias
            for ($i = 1; $i <= $data['total_copies']; $i++) {
                $copy = \R::dispense('copy');
                $copy->isbn = $data['isbn'];
                $copy->copy_code = strtoupper(substr($data['isbn'], -4)) . '-' . str_pad($i, 3, '0', STR_PAD_LEFT);
                $copy->status = 'available';
                $copy->location = 'General Shelf';
                $copy->active = 1;
                \R::store($copy);
            }
            
            \R::commit();
            return true;
            
        } catch (\Exception $e) {
            \R::rollback();
            error_log('Error al crear libro: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Actualizar libro
     */
    public static function update($isbn, $data)
    {
        try {
            \R::begin();
            
            // Obtener libro existente
            $book = \R::findOne('book', 'isbn = ?', [$isbn]);
            if (!$book) {
                throw new \Exception('Libro no encontrado');
            }
            
            // Actualizar libro
            $book->title = $data['title'];
            $book->author = $data['author'];
            $book->publisher = $data['publisher'];
            $book->publication_year = $data['publication_year'] ?? null;
            $book->pages = $data['pages'] ?? null;
            $book->synopsis = $data['synopsis'] ?? '';
            $book->available_copies = $data['available_copies'];
            $book->total_copies = $data['total_copies'];
            
            \R::store($book);
            
            // Actualizar categorías
            \R::exec("DELETE FROM book_category WHERE isbn = ?", [$isbn]);
            
            if (!empty($data['categories'])) {
                foreach ($data['categories'] as $categoryId) {
                    \R::exec(
                        "INSERT INTO book_category (isbn, category_id) VALUES (?, ?)",
                        [$isbn, $categoryId]
                    );
                }
            }
            
            \R::commit();
            return true;
            
        } catch (\Exception $e) {
            \R::rollback();
            error_log('Error al actualizar libro: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Eliminar libro (soft delete)
     */
    public static function softDelete($isbn)
    {
        try {
            $book = \R::findOne('book', 'isbn = ?', [$isbn]);
            if ($book) {
                $book->active = 0;
                \R::store($book);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            error_log('Error al eliminar libro: ' . $e->getMessage());
            return false;
        }
    }
    
    // ============================================
    // ESTADÍSTICAS
    // ============================================
    
    /**
     * Obtener estadísticas del sistema
     */
    public static function getSystemStats()
    {
        $stats = \R::getRow("SELECT * FROM system_stats");
        
        if (!$stats) {
            // Si la vista no existe, calcular manualmente
            $stats = [
                'total_books' => self::count(),
                'total_members' => \R::count('member', 'active = 1'),
                'total_users' => \R::count('user', 'active = 1'),
                'active_loans' => \R::count('loan', 'status = ?', ['active']),
                'overdue_loans' => \R::count('loan', 'status = ? AND due_date < CURDATE()', ['active']),
                'available_copies' => \R::count('copy', 'status = ? AND active = 1', ['available'])
            ];
        }
        
        return $stats;
    }
    
    /**
     * Obtener estadísticas de un libro específico
     */
    public static function getBookStats($isbn)
    {
        $stats = [
            'total_loans' => 0,
            'active_loans' => 0,
            'returned_loans' => 0
        ];
        
        // Total préstamos
        $sql = "
            SELECT COUNT(*) as count
            FROM loan l
            JOIN copy c ON l.copy_id = c.id
            WHERE c.isbn = ?
        ";
        $result = \R::getRow($sql, [$isbn]);
        $stats['total_loans'] = $result['count'] ?? 0;
        
        // Préstamos activos
        $sql = "
            SELECT COUNT(*) as count
            FROM loan l
            JOIN copy c ON l.copy_id = c.id
            WHERE c.isbn = ? AND l.status = 'active'
        ";
        $result = \R::getRow($sql, [$isbn]);
        $stats['active_loans'] = $result['count'] ?? 0;
        
        // Préstamos devueltos
        $stats['returned_loans'] = $stats['total_loans'] - $stats['active_loans'];
        
        return $stats;
    }
}