<?php ob_start(); ?>
<div class="row mb-4">
    <div class="col-12">
        <h1 class="h3 mb-0">Dashboard</h1>
        <p class="text-muted mb-0">
            Bienvenido, <?= htmlspecialchars($_SESSION['user']['name']) ?> 
            <span class="badge bg-secondary ms-1"><?= $_SESSION['user']['role'] ?></span>
        </p>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start border-primary border-4 shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col me-2">
                        <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                            Total Libros
                        </div>
                        <div class="h5 mb-0 fw-bold text-gray-800">
                            <?= $stats['total_books'] ?? 0 ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-book fs-1 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start border-success border-4 shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col me-2">
                        <div class="text-xs fw-bold text-success text-uppercase mb-1">
                            Socios Activos
                        </div>
                        <div class="h5 mb-0 fw-bold text-gray-800">
                            <?= $stats['total_members'] ?? 0 ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-people fs-1 text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start border-info border-4 shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col me-2">
                        <div class="text-xs fw-bold text-info text-uppercase mb-1">
                            Préstamos Activos
                        </div>
                        <div class="h5 mb-0 fw-bold text-gray-800">
                            <?= $stats['active_loans'] ?? 0 ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-arrow-left-right fs-1 text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start border-warning border-4 shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col me-2">
                        <div class="text-xs fw-bold text-warning text-uppercase mb-1">
                            Préstamos Vencidos
                        </div>
                        <div class="h5 mb-0 fw-bold text-gray-800">
                            <?= $stats['overdue_loans'] ?? 0 ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-exclamation-triangle fs-1 text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Content -->
<div class="row">
    <!-- Recent Books -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold text-primary">Libros Recientes</h6>
                <a href="/books" class="btn btn-sm btn-outline-primary">Ver todos</a>
            </div>
            <div class="card-body">
                <?php if (empty($recentBooks)): ?>
                    <p class="text-muted text-center mb-0">No hay libros registrados</p>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentBooks as $book): ?>
                            <a href="/books/<?= $book['isbn'] ?>" 
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1"><?= htmlspecialchars($book['title']) ?></h6>
                                    <small class="text-muted"><?= htmlspecialchars($book['author']) ?></small>
                                </div>
                                <span class="badge bg-primary rounded-pill">
                                    <?= $book['available_copies'] ?> disp.
                                </span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Overdue Loans -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold text-warning">Préstamos Vencidos</h6>
                <a href="/loans" class="btn btn-sm btn-outline-warning">Ver todos</a>
            </div>
            <div class="card-body">
                <?php if (empty($overdueLoans)): ?>
                    <p class="text-success text-center mb-0">
                        <i class="bi bi-check-circle me-2"></i>No hay préstamos vencidos
                    </p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Socio</th>
                                    <th>Libro</th>
                                    <th>Vence</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($overdueLoans as $loan): ?>
                                    <tr class="table-warning">
                                        <td>
                                            <small><?= htmlspecialchars($loan['first_name']) ?></small>
                                        </td>
                                        <td>
                                            <small><?= htmlspecialchars($loan['title']) ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-danger">
                                                <?= date('d/m', strtotime($loan['due_date'])) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 fw-bold text-primary">Acciones Rápidas</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3 col-sm-6">
                        <a href="/books/create" class="btn btn-primary w-100">
                            <i class="bi bi-plus-circle me-2"></i>Nuevo Libro
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="/members/create" class="btn btn-success w-100">
                            <i class="bi bi-person-plus me-2"></i>Nuevo Socio
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="/loans/create" class="btn btn-info w-100">
                            <i class="bi bi-plus-square me-2"></i>Nuevo Préstamo
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="/reports" class="btn btn-secondary w-100">
                            <i class="bi bi-graph-up me-2"></i>Ver Reportes
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php 
$content = ob_get_clean(); 
$title = 'Dashboard';
require_once __DIR__ . '/../layouts/app.php'; 
?>