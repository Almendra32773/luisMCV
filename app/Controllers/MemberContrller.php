<?php
namespace App\Controllers;

use Core\{Controller, Middleware, Csrf};
use App\Models\Member;

class MemberController extends Controller
{
    // GET /members - Listar socios
    public function index()
    {
        Middleware::auth();
        
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? 'active';
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = 15;
        $offset = ($page - 1) * $limit;
        
        $members = Member::getAll($search, $status, $limit, $offset);
        $totalMembers = Member::count($search, $status);
        
        $stats = Member::getStats();
        
        $totalPages = ceil($totalMembers / $limit);
        
        $this->view('members/index', [
            'members' => $members,
            'search' => $search,
            'status' => $status,
            'page' => $page,
            'totalPages' => $totalPages,
            'totalMembers' => $totalMembers,
            'activeMembers' => $stats['active_members'] ?? 0,
            'membersWithLoans' => $stats['members_with_loans'] ?? 0,
            'avgLoansPerMember' => $stats['avg_loans_per_member'] ?? 0
        ]);
    }
    
    // GET /members/create - Formulario para crear socio
    public function create()
    {
        Middleware::auth();
        
        $this->view('members/create');
    }
    
    // POST /members - Guardar nuevo socio
    public function store()
    {
        Middleware::auth();
        Csrf::verify();
        
        $data = [
            'first_name' => $_POST['first_name'] ?? '',
            'last_name' => $_POST['last_name'] ?? '',
            'email' => $_POST['email'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'address' => $_POST['address'] ?? '',
            'birth_date' => $_POST['birth_date'] ?? null,
            'max_loans' => intval($_POST['max_loans'] ?? 5),
            'notes' => $_POST['notes'] ?? ''
        ];
        
        // Validación básica
        $errors = [];
        
        if (empty($data['first_name'])) {
            $errors[] = 'El nombre es obligatorio';
        }
        
        if (empty($data['last_name'])) {
            $errors[] = 'El apellido es obligatorio';
        }
        
        if (empty($data['email'])) {
            $errors[] = 'El email es obligatorio';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'El email no es válido';
        } elseif (Member::emailExists($data['email'])) {
            $errors[] = 'El email ya está registrado';
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            $this->redirect('/members/create');
        }
        
        if (Member::create($data)) {
            $_SESSION['success'] = 'Socio creado exitosamente';
            $this->redirect('/members');
        } else {
            $_SESSION['error'] = 'Error al crear el socio';
            $this->redirect('/members/create');
        }
    }
    
    // GET /members/{id} - Mostrar socio
    public function show($id)
    {
        Middleware::auth();
        
        $member = Member::find($id);
        
        if (!$member) {
            $_SESSION['error'] = 'Socio no encontrado';
            $this->redirect('/members');
        }
        
        // Obtener préstamos activos del socio
        $activeLoans = Member::getActiveLoans($id);
        
        // Obtener historial de préstamos
        $loanHistory = Member::getLoanHistory($id, 10);
        
        // Obtener estadísticas del socio
        $stats = Member::getMemberStats($id);
        
        $this->view('members/show', [
            'member' => $member,
            'activeLoans' => $activeLoans,
            'loanHistory' => $loanHistory,
            'activeLoansCount' => $stats['active_loans'] ?? 0,
            'totalLoans' => $stats['total_loans'] ?? 0,
            'overdueLoansCount' => $stats['overdue_loans'] ?? 0,
            'totalFines' => $stats['total_fines'] ?? 0
        ]);
    }
    
    // GET /members/{id}/edit - Formulario para editar socio
    public function edit($id)
    {
        Middleware::auth();
        
        $member = Member::find($id);
        
        if (!$member) {
            $_SESSION['error'] = 'Socio no encontrado';
            $this->redirect('/members');
        }
        
        $this->view('members/edit', [
            'member' => $member
        ]);
    }
    
    // POST /members/{id}/update - Actualizar socio
    public function update($id)
    {
        Middleware::auth();
        Csrf::verify();
        
        $data = [
            'first_name' => $_POST['first_name'] ?? '',
            'last_name' => $_POST['last_name'] ?? '',
            'email' => $_POST['email'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'address' => $_POST['address'] ?? '',
            'birth_date' => $_POST['birth_date'] ?? null,
            'max_loans' => intval($_POST['max_loans'] ?? 5),
            'notes' => $_POST['notes'] ?? '',
            'active' => isset($_POST['active']) ? 1 : 0
        ];
        
        // Validación básica
        $errors = [];
        
        if (empty($data['first_name'])) {
            $errors[] = 'El nombre es obligatorio';
        }
        
        if (empty($data['last_name'])) {
            $errors[] = 'El apellido es obligatorio';
        }
        
        if (empty($data['email'])) {
            $errors[] = 'El email es obligatorio';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'El email no es válido';
        }
        
        // Verificar si el email ya existe en otro socio
        $existingMember = Member::findByEmail($data['email']);
        if ($existingMember && $existingMember['id'] != $id) {
            $errors[] = 'El email ya está registrado por otro socio';
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            $this->redirect("/members/{$id}/edit");
        }
        
        if (Member::update($id, $data)) {
            $_SESSION['success'] = 'Socio actualizado exitosamente';
            $this->redirect('/members');
        } else {
            $_SESSION['error'] = 'Error al actualizar el socio';
            $this->redirect("/members/{$id}/edit");
        }
    }
    
    // POST /members/{id}/delete - Eliminar socio (marcar como inactivo)
    public function delete($id)
    {
        Middleware::auth();
        Csrf::verify();
        
        if (Member::softDelete($id)) {
            $_SESSION['success'] = 'Socio eliminado exitosamente';
        } else {
            $_SESSION['error'] = 'Error al eliminar el socio';
        }
        
        $this->redirect('/members');
    }
    
    // API: Buscar socios (para autocompletado en préstamos)
    public function searchApi()
    {
        header('Content-Type: application/json');
        
        $query = $_GET['q'] ?? '';
        $limit = intval($_GET['limit'] ?? 10);
        
        if (empty($query)) {
            echo json_encode([]);
            exit;
        }
        
        $members = Member::search($query, $limit);
        echo json_encode($members);
        exit;
    }
}