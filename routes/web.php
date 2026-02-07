<?php
/** @var \Core\Router $router */

// ============================================
// AUTENTICACIÓN
// ============================================
$router->get('/login', 'AuthController@login');
$router->post('/login', 'AuthController@authenticate');
$router->get('/register', 'AuthController@register');
$router->post('/register', 'AuthController@store');
$router->post('/logout', 'AuthController@logout');

// ============================================
// PÁGINAS PRINCIPALES
// ============================================
$router->get('/', 'HomeController@index');
$router->get('/dashboard', 'HomeController@dashboard');

// ============================================
// LIBROS
// ============================================
$router->get('/books', 'BookController@index');
$router->get('/books/create', 'BookController@create');
$router->post('/books', 'BookController@store');
$router->get('/books/{isbn}', 'BookController@show');
$router->get('/books/{isbn}/edit', 'BookController@edit');
$router->post('/books/{isbn}/update', 'BookController@update');
$router->post('/books/{isbn}/delete', 'BookController@delete');

// API para búsqueda y copias (para préstamos)
$router->get('/api/books/search', 'BookController@searchApi');
$router->get('/api/books/{isbn}/copies', 'BookController@copiesApi');

// ============================================
// SOCIOS
// ============================================
$router->get('/members', 'MemberController@index');
$router->get('/members/create', 'MemberController@create');
$router->post('/members', 'MemberController@store');
$router->get('/members/{id}', 'MemberController@show');
$router->get('/members/{id}/edit', 'MemberController@edit');
$router->post('/members/{id}/update', 'MemberController@update');
$router->post('/members/{id}/delete', 'MemberController@delete');

// API para búsqueda de socios
$router->get('/api/members/search', 'MemberController@searchApi');

// ============================================
// PRÉSTAMOS
// ============================================
$router->get('/loans', 'LoanController@index');
$router->get('/loans/create', 'LoanController@create');
$router->post('/loans', 'LoanController@store');
$router->get('/loans/{id}', 'LoanController@show');
$router->post('/loans/{id}/return', 'LoanController@return');
$router->post('/loans/{id}/renew', 'LoanController@renew');
$router->post('/loans/{id}/fine', 'LoanController@fine');
$router->get('/loans/overdue', 'LoanController@overdue');

// ============================================
// CATEGORÍAS
// ============================================
$router->get('/categories', 'CategoryController@index');
$router->post('/categories', 'CategoryController@store');
$router->post('/categories/{id}/update', 'CategoryController@update');
$router->post('/categories/{id}/delete', 'CategoryController@delete');

// ============================================
// USUARIOS DEL SISTEMA
// ============================================
$router->get('/users', 'UserController@index');
$router->get('/users/{id}', 'UserController@show');
$router->get('/users/{id}/edit', 'UserController@edit');
$router->post('/users/{id}/update', 'UserController@update');
$router->post('/users/{id}/delete', 'UserController@delete');
$router->get('/profile', 'UserController@profile');
$router->post('/users', 'UserController@store');

// ============================================
// ERRORES - COMENTAR TEMPORALMENTE HASTA QUE FUNCIONE
// ============================================
// $router->set404(function() {
//     http_response_code(404);
//     require_once '../app/views/errors/404.php';
// });