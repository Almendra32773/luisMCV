<?php ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Socios de la Biblioteca</h1>
        <p class="text-muted mb-0">Gestión de socios registrados</p>
    </div>
    <a href="/members/create" class="btn btn-primary">
        <i class="bi bi-person-plus me-2"></i>Nuevo Socio
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <!-- Search Bar -->
        <div class="row mb-4">
            <div class="col-md-8">
                <form method="GET" action="/members" class="input-group">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Buscar por nombre, apellido o email..." 
                           value="<?= htmlspecialchars($search ?? '') ?>">
                    <button type="submit" class="btn btn-outline-secondary">
                        <i class="bi bi-search"></i>
                    </button>
                    <?php if (!empty($search)): ?>
                        <a href="/members" class="btn btn-outline-danger">
                            <i class="bi bi-x-circle"></i>
                        </a>
                    <?php endif; ?>
                </form>
            </div>
            <div class="col-md-4 text-md-end mt-2 mt-md-0">
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-secondary dropdown-toggle" 
                            data-bs-toggle="dropdown">
                        <i class="bi bi-filter me-2"></i>Filtrar
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="/members?status=active">Activos</a></li>
                        <li><a class="dropdown-item" href="/members?status=inactive">Inactivos</a></li>
                        <li><a class="dropdown-item" href="/members?status=all">Todos</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Members Table -->
        <?php if (empty($members)): ?>
            <div class="text-center py-5">
                <i class="bi bi-people display-1 text-muted mb-3"></i>
                <h4 class="text-muted">No se encontraron socios</h4>
                <?php if (!empty($search)): ?>
                    <p class="text-muted">Intenta con otros términos de búsqueda</p>
                <?php else: ?>
                    <p class="text-muted">Comienza registrando un nuevo socio</p>
                    <a href="/members/create" class="btn btn-primary mt-2">
                        <i class="bi bi-person-plus me-2"></i>Registrar Primer Socio
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="10%">Código</th>
                            <th width="25%">Nombre</th>
                            <th width="20%">Contacto</th>
                            <th width="15%">Préstamos Activos</th>
                            <th width="10%">Estado</th>
                            <th width="20%" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($members as $member): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-info"><?= $member['member_code'] ?></span>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) ?></strong>
                                    <?php if ($member['birth_date']): ?>
                                        <br>
                                        <small class="text-muted">
                                            <?= date('d/m/Y', strtotime($member['birth_date'])) ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div>
                                        <small class="d-block">
                                            <i class="bi bi-envelope me-1"></i>
                                            <?= htmlspecialchars($member['email']) ?>
                                        </small>
                                        <?php if ($member['phone']): ?>
                                            <small class="d-block">
                                                <i class="bi bi-telephone me-1"></i>
                                                <?= htmlspecialchars($member['phone']) ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    $activeLoans = \R::count('loan', 'member_id = ? AND status = ?', 
                                        [$member['id'], 'active']);
                                    ?>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                            <div class="progress-bar bg-<?= $activeLoans >= $member['max_loans'] ? 'danger' : 'success' ?>" 
                                                 style="width: <?= min(100, ($activeLoans / $member['max_loans']) * 100) ?>%">
                                            </div>
                                        </div>
                                        <span class="small">
                                            <?= $activeLoans ?>/<?= $member['max_loans'] ?>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($member['active']): ?>
                                        <span class="badge bg-success">Activo</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="/members/<?= $member['id'] ?>" 
                                           class="btn btn-outline-info"
                                           data-bs-toggle="tooltip" title="Ver detalles">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="/members/<?= $member['id'] ?>/edit" 
                                           class="btn btn-outline-primary"
                                           data-bs-toggle="tooltip" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="/loans/create?member_id=<?= $member['id'] ?>" 
                                           class="btn btn-outline-success"
                                           data-bs-toggle="tooltip" title="Nuevo préstamo">
                                            <i class="bi bi-plus-circle"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="/members?page=<?= $page - 1 ?>&search=<?= urlencode($search ?? '') ?>">
                                Anterior
                            </a>
                        </li>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" href="/members?page=<?= $i ?>&search=<?= urlencode($search ?? '') ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link" href="/members?page=<?= $page + 1 ?>&search=<?= urlencode($search ?? '') ?>">
                                Siguiente
                            </a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Stats -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Total Socios</h6>
                        <p class="card-text h4 mb-0"><?= $totalMembers ?? 0 ?></p>
                    </div>
                    <i class="bi bi-people display-6 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Socios Activos</h6>
                        <p class="card-text h4 mb-0"><?= $activeMembers ?? 0 ?></p>
                    </div>
                    <i class="bi bi-check-circle display-6 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Con Préstamos</h6>
                        <p class="card-text h4 mb-0"><?= $membersWithLoans ?? 0 ?></p>
                    </div>
                    <i class="bi bi-book display-6 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Promedio Préstamos</h6>
                        <p class="card-text h4 mb-0"><?= number_format($avgLoansPerMember ?? 0, 1) ?></p>
                    </div>
                    <i class="bi bi-graph-up display-6 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>
<?php 
$content = ob_get_clean(); 
$title = 'Socios';
require_once __DIR__ . '/../layouts/app.php'; 
?>