<?php
namespace App\Controllers;

use Core\{Controller, Middleware, Csrf};
use App\Models\{Loan, Member, Book};

class LoanController extends Controller
{
    // GET /loans - Listar préstamos
    public function index()
    {
        Middleware::auth();
        
        $status = $_GET['status'] ?? '';
        $dateFrom = $_GET['date_from'] ?? '';
        $dateTo = $_GET['date_to'] ?? '';
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $loans = Loan::getAll($status, $dateFrom, $dateTo, $limit, $offset);
        $totalLoans = Loan::count($status, $dateFrom, $dateTo);
        
        $stats = Loan::getStats();
        
        $totalPages = ceil($totalLoans / $limit);
        
        // Construir query string para paginación
        $queryParams = [];
        if ($status) $queryParams[] = "status={$status}";
        if ($dateFrom) $queryParams[] = "date_from={$dateFrom}";
        if ($dateTo) $queryParams[] = "date_to={$dateTo}";
        $queryString = $queryParams ? '&' . implode('&', $queryParams) : '';
        
        $this->view('loans/index', [
            'loans' => $loans,
            'status' => $status,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'page' => $page,
            'totalPages' => $totalPages,
            'queryString' => $queryString,
            'totalLoans' => $totalLoans,
            'activeLoans' => $stats['active_loans'] ?? 0,
            'overdueLoans' => $stats['overdue_loans'] ?? 0,
            'totalFines' => $stats['total_fines'] ?? 0
        ]);
    }
    
    // GET /loans/create - Formulario para crear préstamo
    public function create()
    {
        Middleware::auth();
        
        $members = Member::getAllForLoan();
        
        $this->view('loans/create', [
            'members' => $members
        ]);
    }
    
    // POST /loans - Guardar nuevo préstamo
    public function store()
    {
        Middleware::auth();
        Csrf::verify();
        
        $data = [
            'member_id' => intval($_POST['member_id'] ?? 0),
            'copy_id' => intval($_POST['copy_id'] ?? 0),
            'loan_date' => $_POST['loan_date'] ?? date('Y-m-d'),
            'loan_days' => intval($_POST['loan_days'] ?? 15),
            'notes' => $_POST['notes'] ?? ''
        ];
        
        // Validación básica
        $errors = [];
        
        if (empty($data['member_id'])) {
            $errors[] = 'Debe seleccionar un socio';
        }
        
        if (empty($data['copy_id'])) {
            $errors[] = 'Debe seleccionar una copia';
        }
        
        if (empty($data['loan_date'])) {
            $errors[] = 'La fecha de préstamo es obligatoria';
        }
        
        if ($data['loan_days'] <= 0) {
            $errors[] = 'Los días de préstamo deben ser mayores a 0';
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            $this->redirect('/loans/create');
        }
        
        // Verificar límite de préstamos del socio
        $member = Member::find($data['member_id']);
        if ($member && !Member::canBorrow($data['member_id'])) {
            $_SESSION['error'] = 'El socio ha alcanzado su límite de préstamos';
            $this->redirect('/loans/create');
        }
        
        // Verificar disponibilidad de la copia
        $copyAvailable = Book::isCopyAvailable($data['copy_id']);
        if (!$copyAvailable) {
            $_SESSION['error'] = 'La copia seleccionada no está disponible';
            $this->redirect('/loans/create');
        }
        
        // Crear préstamo
        if (Loan::create($data)) {
            $_SESSION['success'] = 'Préstamo registrado exitosamente';
            $this->redirect('/loans');
        } else {
            $_SESSION['error'] = 'Error al registrar el préstamo';
            $this->redirect('/loans/create');
        }
    }
    
    // POST /loans/{id}/return - Marcar préstamo como devuelto
    public function return($id)
    {
        Middleware::auth();
        Csrf::verify();
        
        $fine = $_POST['fine'] ?? 0;
        
        if (Loan::returnLoan($id, $fine)) {
            $_SESSION['success'] = 'Préstamo marcado como devuelto';
        } else {
            $_SESSION['error'] = 'Error al devolver el préstamo';
        }
        
        $this->redirect('/loans');
    }
    
    // POST /loans/{id}/renew - Renovar préstamo
    public function renew($id)
    {
        Middleware::auth();
        Csrf::verify();
        
        $additionalDays = intval($_POST['additional_days'] ?? 15);
        
        if (Loan::renew($id, $additionalDays)) {
            $_SESSION['success'] = 'Préstamo renovado exitosamente';
        } else {
            $_SESSION['error'] = 'Error al renovar el préstamo';
        }
        
        $this->redirect('/loans');
    }
    
    // GET /loans/{id} - Mostrar préstamo
    public function show($id)
    {
        Middleware::auth();
        
        $loan = Loan::find($id);
        
        if (!$loan) {
            $_SESSION['error'] = 'Préstamo no encontrado';
            $this->redirect('/loans');
        }
        
        $this->view('loans/show', [
            'loan' => $loan
        ]);
    }
    
    // GET /loans/overdue - Listar préstamos vencidos
    public function overdue()
    {
        Middleware::auth();
        
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $loans = Loan::getOverdue($limit, $offset);
        $totalLoans = Loan::countOverdue();
        
        $totalPages = ceil($totalLoans / $limit);
        
        $this->view('loans/overdue', [
            'loans' => $loans,
            'page' => $page,
            'totalPages' => $totalPages,
            'totalLoans' => $totalLoans
        ]);
    }
    
    // POST /loans/{id}/fine - Aplicar multa
    public function fine($id)
    {
        Middleware::auth();
        Csrf::verify();
        
        $amount = floatval($_POST['amount'] ?? 0);
        $reason = $_POST['reason'] ?? '';
        
        if ($amount <= 0) {
            $_SESSION['error'] = 'El monto de la multa debe ser mayor a 0';
            $this->redirect('/loans');
        }
        
        if (Loan::applyFine($id, $amount, $reason)) {
            $_SESSION['success'] = "Multa de \${$amount} aplicada exitosamente";
        } else {
            $_SESSION['error'] = 'Error al aplicar la multa';
        }
        
        $this->redirect('/loans');
    }
}