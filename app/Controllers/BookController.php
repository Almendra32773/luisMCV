<?php
namespace App\Controllers;

use Core\{Controller, Middleware, Csrf};
use App\Models\{Book, Category};

class BookController extends Controller
{
    // GET /books - Listar libros
    public function index()
    {
        Middleware::auth();
        
        $search = $_GET['search'] ?? '';
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        if ($search) {
            $books = Book::search($search, $limit, $offset);
            $totalBooks = Book::countSearch($search);
        } else {
            $books = Book::getAll($limit, $offset);
            $totalBooks = Book::count();
        }
        
        $totalPages = ceil($totalBooks / $limit);
        
        $this->view('books/index', [
            'books' => $books,
            'search' => $search,
            'page' => $page,
            'totalPages' => $totalPages,
            'totalBooks' => $totalBooks
        ]);
    }
    
    // GET /books/create - Formulario para crear libro
    public function create()
    {
        Middleware::auth();
        
        $categories = Category::getAll();
        
        $this->view('books/create', [
            'categories' => $categories
        ]);
    }
    
    // POST /books - Guardar nuevo libro
    public function store()
    {
        Middleware::auth();
        Csrf::verify();
        
        $data = [
            'isbn' => $_POST['isbn'] ?? '',
            'title' => $_POST['title'] ?? '',
            'author' => $_POST['author'] ?? '',
            'publisher' => $_POST['publisher'] ?? '',
            'publication_year' => $_POST['publication_year'] ?? null,
            'pages' => $_POST['pages'] ?? null,
            'synopsis' => $_POST['synopsis'] ?? '',
            'language' => $_POST['language'] ?? 'Español',
            'total_copies' => intval($_POST['total_copies'] ?? 1),
            'categories' => $_POST['categories'] ?? []
        ];
        
        // Validación básica
        $errors = [];
        
        if (empty($data['isbn'])) {
            $errors[] = 'El ISBN es obligatorio';
        }
        
        if (empty($data['title'])) {
            $errors[] = 'El título es obligatorio';
        }
        
        if (empty($data['author'])) {
            $errors[] = 'El autor es obligatorio';
        }
        
        if (empty($data['publisher'])) {
            $errors[] = 'La editorial es obligatoria';
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            $this->redirect('/books/create');
        }
        
        if (Book::exists($data['isbn'])) {
            $_SESSION['error'] = 'El ISBN ya está registrado';
            $this->redirect('/books/create');
        }
        
        if (Book::create($data)) {
            $_SESSION['success'] = 'Libro creado exitosamente';
            $this->redirect('/books');
        } else {
            $_SESSION['error'] = 'Error al crear el libro';
            $this->redirect('/books/create');
        }
    }
    
    // GET /books/{isbn} - Mostrar libro
    public function show($isbn)
    {
        Middleware::auth();
        
        $book = Book::find($isbn);
        
        if (!$book) {
            $_SESSION['error'] = 'Libro no encontrado';
            $this->redirect('/books');
        }
        
        // Obtener copias del libro
        $copies = Book::getCopies($isbn);
        
        // Obtener préstamos activos para este libro
        $activeLoans = Book::getActiveLoans($isbn);
        
        $this->view('books/show', [
            'book' => $book,
            'copies' => $copies,
            'activeLoans' => $activeLoans
        ]);
    }
    
    // GET /books/{isbn}/edit - Formulario para editar libro
    public function edit($isbn)
    {
        Middleware::auth();
        
        $book = Book::find($isbn);
        
        if (!$book) {
            $_SESSION['error'] = 'Libro no encontrado';
            $this->redirect('/books');
        }
        
        $categories = Category::getAll();
        
        // Convertir category_ids string a array
        $book['category_ids'] = !empty($book['category_ids']) ? 
            explode(',', $book['category_ids']) : [];
        
        $this->view('books/edit', [
            'book' => $book,
            'categories' => $categories
        ]);
    }
    
    // POST /books/{isbn}/update - Actualizar libro
    public function update($isbn)
    {
        Middleware::auth();
        Csrf::verify();
        
        $data = [
            'title' => $_POST['title'] ?? '',
            'author' => $_POST['author'] ?? '',
            'publisher' => $_POST['publisher'] ?? '',
            'publication_year' => $_POST['publication_year'] ?? null,
            'pages' => $_POST['pages'] ?? null,
            'synopsis' => $_POST['synopsis'] ?? '',
            'available_copies' => intval($_POST['available_copies'] ?? 0),
            'total_copies' => intval($_POST['total_copies'] ?? 1),
            'categories' => $_POST['categories'] ?? []
        ];
        
        // Validación básica
        $errors = [];
        
        if (empty($data['title'])) {
            $errors[] = 'El título es obligatorio';
        }
        
        if (empty($data['author'])) {
            $errors[] = 'El autor es obligatorio';
        }
        
        if (empty($data['publisher'])) {
            $errors[] = 'La editorial es obligatoria';
        }
        
        if ($data['available_copies'] > $data['total_copies']) {
            $errors[] = 'Las copias disponibles no pueden ser más que las copias totales';
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            $this->redirect("/books/{$isbn}/edit");
        }
        
        if (Book::update($isbn, $data)) {
            $_SESSION['success'] = 'Libro actualizado exitosamente';
            $this->redirect('/books');
        } else {
            $_SESSION['error'] = 'Error al actualizar el libro';
            $this->redirect("/books/{$isbn}/edit");
        }
    }
    
    // POST /books/{isbn}/delete - Eliminar libro (marcar como inactivo)
    public function delete($isbn)
    {
        Middleware::auth();
        Csrf::verify();
        
        if (Book::softDelete($isbn)) {
            $_SESSION['success'] = 'Libro eliminado exitosamente';
        } else {
            $_SESSION['error'] = 'Error al eliminar el libro';
        }
        
        $this->redirect('/books');
    }
    
    // API: Buscar libros (para autocompletado en préstamos)
    public function searchApi()
    {
        header('Content-Type: application/json');
        
        $query = $_GET['q'] ?? '';
        $limit = intval($_GET['limit'] ?? 10);
        
        if (empty($query)) {
            echo json_encode([]);
            exit;
        }
        
        $books = Book::search($query, $limit);
        echo json_encode($books);
        exit;
    }
    
    // API: Obtener copias disponibles de un libro
    public function copiesApi($isbn)
    {
        header('Content-Type: application/json');
        
        $copies = Book::getAvailableCopies($isbn);
        echo json_encode($copies);
        exit;
    }
}