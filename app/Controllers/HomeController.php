<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\{Book, Member, Loan};

class HomeController extends Controller
{
    public function index()
    {
        // Si no está logueado, mostrar vista de invitado
        if (!isset($_SESSION['user'])) {
            $this->view('home', [], 'layouts/guest');
            return;
        }
        
        // Obtener estadísticas del sistema
        $stats = Book::getSystemStats();
        
        // Obtener préstamos vencidos
        $overdueLoans = Loan::getRecentOverdue(5);
        
        // Obtener libros recientes
        $recentBooks = Book::getRecent(5);
        
        // Obtener préstamos recientes
        $recentLoans = Loan::getRecent(5);
        
        $this->view('dashboard/index', [
            'stats' => $stats,
            'overdueLoans' => $overdueLoans,
            'recentBooks' => $recentBooks,
            'recentLoans' => $recentLoans
        ], 'layouts/app');
    }
    
    // Dashboard alternativo (si prefieres separar)
    public function dashboard()
    {
        Middleware::auth();
        
        // Obtener estadísticas del sistema
        $stats = Book::getSystemStats();
        
        // Obtener préstamos vencidos
        $overdueLoans = Loan::getRecentOverdue(5);
        
        // Obtener libros recientes
        $recentBooks = Book::getRecent(5);
        
        // Obtener préstamos recientes
        $recentLoans = Loan::getRecent(5);
        
        $this->view('dashboard/index', [
            'stats' => $stats,
            'overdueLoans' => $overdueLoans,
            'recentBooks' => $recentBooks,
            'recentLoans' => $recentLoans
        ]);
    }
}