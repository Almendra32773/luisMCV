<?php
// Determinar si el usuario está logueado
$isLoggedIn = isset($_SESSION['user']);

if ($isLoggedIn) {
    // ============================================
    // VERSIÓN PARA USUARIOS LOGUEADOS
    // ============================================
    ob_start();
?>
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">
                                <i class="bi bi-house-door me-2 text-primary"></i>Bienvenido al Sistema
                            </h4>
                            <p class="text-muted mb-0 mt-1">
                                Hola, <?= htmlspecialchars($_SESSION['user']['name']) ?> 
                                <span class="badge bg-secondary ms-2"><?= $_SESSION['user']['role'] ?></span>
                            </p>
                        </div>
                        <div class="text-end">
                            <small class="text-muted"><?= date('d/m/Y H:i') ?></small>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Estadísticas rápidas -->
                    <div class="row mb-5">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card bg-primary bg-opacity-10 border-0">
                                <div class="card-body text-center">
                                    <div class="display-6 text-primary mb-2">
                                        <i class="bi bi-book"></i>
                                    </div>
                                    <h5 class="card-title">Libros</h5>
                                    <p class="card-text fs-4 fw-bold">0</p>
                                    <a href="/luisMCV/public/books" class="btn btn-sm btn-outline-primary">
                                        Ver todos
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card bg-success bg-opacity-10 border-0">
                                <div class="card-body text-center">
                                    <div class="display-6 text-success mb-2">
                                        <i class="bi bi-people"></i>
                                    </div>
                                    <h5 class="card-title">Socios</h5>
                                    <p class="card-text fs-4 fw-bold">0</p>
                                    <a href="/luisMCV/public/members" class="btn btn-sm btn-outline-success">
                                        Ver todos
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card bg-info bg-opacity-10 border-0">
                                <div class="card-body text-center">
                                    <div class="display-6 text-info mb-2">
                                        <i class="bi bi-arrow-left-right"></i>
                                    </div>
                                    <h5 class="card-title">Préstamos Activos</h5>
                                    <p class="card-text fs-4 fw-bold">0</p>
                                    <a href="/luisMCV/public/loans" class="btn btn-sm btn-outline-info">
                                        Ver todos
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card bg-warning bg-opacity-10 border-0">
                                <div class="card-body text-center">
                                    <div class="display-6 text-warning mb-2">
                                        <i class="bi bi-exclamation-triangle"></i>
                                    </div>
                                    <h5 class="card-title">Vencidos</h5>
                                    <p class="card-text fs-4 fw-bold">0</p>
                                    <a href="/luisMCV/public/loans/overdue" class="btn btn-sm btn-outline-warning">
                                        Revisar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Acciones rápidas -->
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0">
                                        <i class="bi bi-lightning me-2 text-primary"></i>Acciones Rápidas
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <a href="/luisMCV/public/books/create" class="btn btn-primary w-100 py-3">
                                                <i class="bi bi-plus-circle me-2"></i>
                                                Nuevo Libro
                                            </a>
                                        </div>
                                        <div class="col-md-4">
                                            <a href="/luisMCV/public/members/create" class="btn btn-success w-100 py-3">
                                                <i class="bi bi-person-plus me-2"></i>
                                                Nuevo Socio
                                            </a>
                                        </div>
                                        <div class="col-md-4">
                                            <a href="/luisMCV/public/loans/create" class="btn btn-info w-100 py-3">
                                                <i class="bi bi-plus-square me-2"></i>
                                                Nuevo Préstamo
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0">
                                        <i class="bi bi-clock-history me-2 text-primary"></i>Actividad Reciente
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="text-center py-4">
                                        <div class="display-4 text-muted mb-3">
                                            <i class="bi bi-inbox"></i>
                                        </div>
                                        <p class="text-muted mb-0">No hay actividad reciente</p>
                                        <small class="text-muted">Los registros aparecerán aquí</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer bg-white border-top-0 text-center">
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        Sistema de Gestión Bibliotecaria v1.0
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    $content = ob_get_clean();
    $title = 'Dashboard';
    require_once __DIR__ . '/layouts/guest.php';
    
} else {
    // ============================================
    // VERSIÓN PARA INVITADOS (NO LOGUEADOS)
    // ============================================
    ob_start();
?>
<div class="hero-section text-center py-5">
    <div class="container">
        <h1 class="display-4 fw-bold mb-4">
            <i class="bi bi-book text-primary"></i> 
            Sistema de Gestión Bibliotecaria
        </h1>
        <p class="lead mb-5">
            Gestiona tu biblioteca de manera eficiente con nuestro sistema completo 
            de control de libros, socios y préstamos. Fácil, rápido y seguro.
        </p>
        
        <div class="d-flex flex-wrap justify-content-center gap-3 mb-5">
            <a href="/luisMCV/public/login" class="btn btn-primary btn-lg px-4 py-3">
                <i class="bi bi-box-arrow-in-right me-2"></i>
                Iniciar Sesión
            </a>
            <a href="/luisMCV/public/register" class="btn btn-outline-primary btn-lg px-4 py-3">
                <i class="bi bi-person-plus me-2"></i>
                Registrarse
            </a>
        </div>
    </div>
</div>

<div class="features-section py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5">¿Por qué elegir nuestro sistema?</h2>
        
        <div class="row g-4">
            <!-- Feature 1 -->
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon mb-3">
                            <i class="bi bi-book text-primary" style="font-size: 3rem;"></i>
                        </div>
                        <h4 class="card-title">Gestión de Libros</h4>
                        <p class="card-text">
                            Catálogo completo con búsqueda avanzada, categorización 
                            y control de inventario.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Feature 2 -->
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon mb-3">
                            <i class="bi bi-people text-success" style="font-size: 3rem;"></i>
                        </div>
                        <h4 class="card-title">Control de Socios</h4>
                        <p class="card-text">
                            Administra socios, préstamos, renovaciones y 
                            sanciones de manera eficiente.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Feature 3 -->
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon mb-3">
                            <i class="bi bi-graph-up text-info" style="font-size: 3rem;"></i>
                        </div>
                        <h4 class="card-title">Reportes y Análisis</h4>
                        <p class="card-text">
                            Genera reportes detallados y analiza el uso de 
                            tu biblioteca en tiempo real.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="demo-section py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h3 class="mb-4">Acceso de Prueba</h3>
                <p class="mb-4">
                    Prueba nuestro sistema con las siguientes credenciales de demostración:
                </p>
                
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <i class="bi bi-person-badge me-2"></i>Administrador
                    </div>
                    <div class="card-body">
                        <p class="mb-1"><strong>Email:</strong> admin@biblioteca.com</p>
                        <p class="mb-0"><strong>Contraseña:</strong> 123456</p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <i class="bi bi-person me-2"></i>Bibliotecario
                    </div>
                    <div class="card-body">
                        <p class="mb-1"><strong>Email:</strong> librarian@biblioteca.com</p>
                        <p class="mb-0"><strong>Contraseña:</strong> 123456</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card border-0 shadow">
                    <div class="card-body p-4">
                        <h4 class="card-title mb-3">
                            <i class="bi bi-lightning-charge text-warning me-2"></i>
                            Características Principales
                        </h4>
                        <ul class="list-unstyled">
                            <li class="mb-3">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <strong>Interfaz Intuitiva:</strong> Fácil de usar y aprender
                            </li>
                            <li class="mb-3">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <strong>Multi-usuario:</strong> Roles y permisos configurables
                            </li>
                            <li class="mb-3">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <strong>Respaldos Automáticos:</strong> Tus datos siempre seguros
                            </li>
                            <li class="mb-3">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <strong>Soporte Técnico:</strong> Asistencia 24/7
                            </li>
                            <li class="mb-3">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <strong>Actualizaciones Gratuitas:</strong> Siempre la última versión
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    $content = ob_get_clean();
    require_once __DIR__ . '/layouts/app.php';
}
?>