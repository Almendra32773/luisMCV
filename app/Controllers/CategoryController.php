<?php
namespace App\Controllers;

use Core\{Controller, Middleware, Csrf};
use App\Models\Category;

class CategoryController extends Controller
{
    // GET /categories - Listar categorías
    public function index()
    {
        Middleware::auth();
        
        $categories = Category::getAllWithStats();
        $categories = $categories ?? [];
        
        $this->view('categories/index', [
            'categories' => $categories
        ]);
    }
    
    // POST /categories - Guardar nueva categoría
    public function store()
    {
        Middleware::auth();
        Csrf::verify();
        
        $data = [
            'name' => $_POST['name'] ?? '',
            'description' => $_POST['description'] ?? ''
        ];
        
        // Validación básica
        $errors = [];
        
        if (empty($data['name'])) {
            $errors[] = 'El nombre de la categoría es obligatorio';
        }
        
        if (Category::exists($data['name'])) {
            $errors[] = 'Ya existe una categoría con ese nombre';
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            $this->redirect('/categories');
        }
        
        if (Category::create($data)) {
            $_SESSION['success'] = 'Categoría creada exitosamente';
        } else {
            $_SESSION['error'] = 'Error al crear la categoría';
        }
        
        $this->redirect('/categories');
    }
    
    // POST /categories/{id}/update - Actualizar categoría
    public function update($id)
    {
        Middleware::auth();
        Csrf::verify();
        
        $data = [
            'name' => $_POST['name'] ?? '',
            'description' => $_POST['description'] ?? ''
        ];
        
        // Validación básica
        $errors = [];
        
        if (empty($data['name'])) {
            $errors[] = 'El nombre de la categoría es obligatorio';
        }
        
        // Verificar si el nombre ya existe en otra categoría
        $existingCategory = Category::findByName($data['name']);
        if ($existingCategory && $existingCategory['id'] != $id) {
            $errors[] = 'Ya existe una categoría con ese nombre';
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            $this->redirect('/categories');
        }
        
        if (Category::update($id, $data)) {
            $_SESSION['success'] = 'Categoría actualizada exitosamente';
        } else {
            $_SESSION['error'] = 'Error al actualizar la categoría';
        }
        
        $this->redirect('/categories');
    }
    
    // POST /categories/{id}/delete - Eliminar categoría
    public function delete($id)
    {
        Middleware::auth();
        Csrf::verify();
        
        // Verificar si la categoría tiene libros asignados
        $bookCount = Category::getBookCount($id);
        
        if ($bookCount > 0) {
            $_SESSION['warning'] = "Esta categoría tiene {$bookCount} libro(s) asignado(s). Se eliminará solo la categoría, los libros mantendrán esta categoría en su historial.";
        }
        
        if (Category::delete($id)) {
            $_SESSION['success'] = 'Categoría eliminada exitosamente';
        } else {
            $_SESSION['error'] = 'Error al eliminar la categoría';
        }
        
        $this->redirect('/categories');
    }
}