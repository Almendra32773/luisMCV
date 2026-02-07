<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $_ENV['APP_NAME'] ?? 'Biblioteca MVC' ?> | <?= $title ?? 'Dashboard' ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    
    <style>
        body {
            padding-top: 56px;
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        
        .sidebar {
            position: fixed;
            top: 56px;
            bottom: 0;
            left: 0;
            z-index: 1000;
            padding: 10px 0;
            background-color: #3b4045; /* Gris */
            border-right: none;
            height: calc(100vh - 56px); /* Ajuste para que no cubra el footer */
        }
        
        .sidebar .nav-link {
            color: #e9ecef;
            border-radius: 5px;
            margin: 5px 0;
        }
        
        .sidebar .nav-link:hover {
            color: #fff;
            background-color: #495057;
        }
        
        .main-content {
            padding: 20px;
            margin-left: 200px;
            background-color: #f8f9fa; /* Fondo plano */
            border: 1px solid #dee2e6; /* Bordes para secciones de información */
            border-radius: 5px;
        }
        
        .btn {
            border-radius: 5px;
            background-color: #6c757d;
            color: #fff;
        }
        
        .btn:hover {
            background-color: #5a6268;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                position: static;
                height: auto;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <?php require_once __DIR__ . '/../partials/navbar.php'; ?>
    
    <!-- Container -->
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar">
                <div class="sidebar-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?= ($_SERVER['REQUEST_URI'] == '/') ? 'active' : '' ?>" 
                               href="/luisMCV/public/dashboard">
                                <i class="bi bi-house-door"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/books') === 0 ? 'active' : '' ?>" 
                               href="/luisMCV/public/books">
                                <i class="bi bi-book"></i> Libros
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/members') === 0 ? 'active' : '' ?>" 
                               href="/luisMCV/public/members">
                                <i class="bi bi-people"></i> Socios
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/loans') === 0 ? 'active' : '' ?>" 
                               href="/luisMCV/public/loans">
                                <i class="bi bi-arrow-left-right"></i> Préstamos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/categories') === 0 ? 'active' : '' ?>" 
                               href="/luisMCV/public/categories">
                                <i class="bi bi-tags"></i> Categorías
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/users') === 0 ? 'active' : '' ?>" 
                               href="/luisMCV/public/users">
                                <i class="bi bi-person-badge"></i> Usuarios
                            </a>
                        </li>
                    </ul>
                    
                    <hr class="my-3 border-secondary">
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/reports') === 0 ? 'active' : '' ?>" 
                               href="#">
                                <i class="bi bi-graph-up"></i> Reportes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/settings') === 0 ? 'active' : '' ?>" 
                               href="#">
                                <i class="bi bi-gear"></i> Configuración
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            
            <!-- Main Content -->
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 main-content">
                <!-- Toast Messages -->
                <?php require_once __DIR__ . '/../partials/toast.php'; ?>
                
                <!-- Page Content -->
                <?= $content ?? '' ?>
            </main>
        </div>
    </div>
    
    <!-- Footer -->
    <?php require_once __DIR__ . '/../partials/footer.php'; ?>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Scripts -->
    <script>
        // Auto-dismiss alerts after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.alert:not(.alert-permanent)').forEach(alert => {
                bootstrap.Alert.getOrCreateInstance(alert).close();
            });
        }, 5000);
        
        // Activar tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
</body>
</html>