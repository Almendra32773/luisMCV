<?php ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Préstamos</h1>
        <p class="text-muted mb-0">Gestión de préstamos de libros</p>
    </div>
    <div>
        <a href="/loans/create" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Nuevo Préstamo
        </a>
    </div>
</div>

<!-- Filtros -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="/loans" class="row g-3">
            <div class="col-md-3">
                <label for="status" class="form-label">Estado</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Todos</option>
                    <option value="active" <?= ($_GET['status'] ?? '') == 'active' ? 'selected' : '' ?>>Activos</option>
                    <option value="overdue" <?= ($_GET['status'] ?? '') == 'overdue' ? 'selected' : '' ?>>Vencidos</option>
                    <option value="returned" <?= ($_GET['status'] ?? '') == 'returned' ? 'selected' : '' ?>>Devueltos</option>
                    <option value="lost" <?= ($_GET['status'] ?? '') == 'lost' ? 'selected' : '' ?>>Perdidos</option>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="date_from" class="form-label">Desde</label>
                <input type="date" class="form-control" id="date_from" name="date_from" 
                       value="<?= $_GET['date_from'] ?? '' ?>">
            </div>
            
            <div class="col-md-3">
                <label for="date_to" class="form-label">Hasta</label>
                <input type="date" class="form-control" id="date_to" name="date_to" 
                       value="<?= $_GET['date_to'] ?? '' ?>">
            </div>
            
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-filter me-2"></i>Filtrar
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <!-- Loans Table -->
        <?php if (empty($loans)): ?>
            <div class="text-center py-5">
                <i class="bi bi-arrow-left-right display-1 text-muted mb-3"></i>
                <h4 class="text-muted">No se encontraron préstamos</h4>
                <p class="text-muted">Comienza registrando un nuevo préstamo</p>
                <a href="/loans/create" class="btn btn-primary mt-2">
                    <i class="bi bi-plus-circle me-2"></i>Registrar Primer Préstamo
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="15%">Socio</th>
                            <th width="25%">Libro</th>
                            <th width="10%">Copia</th>
                            <th width="10%">Préstamo</th>
                            <th width="10%">Vencimiento</th>
                            <th width="10%">Estado</th>
                            <th width="10%">Multa</th>
                            <th width="10%" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($loans as $loan): ?>
                            <?php
                            $dueDate = strtotime($loan['due_date']);
                            $isOverdue = $dueDate < time() && $loan['status'] == 'active';
                            ?>
                            <tr class="<?= $isOverdue ? 'table-warning' : '' ?>">
                                <td>
                                    <a href="/members/<?= $loan['member_id'] ?>" class="text-decoration-none">
                                        <?= htmlspecialchars($loan['first_name'] . ' ' . $loan['last_name']) ?>
                                    </a>
                                    <br>
                                    <small class="text-muted"><?= $loan['member_code'] ?></small>
                                </td>
                                <td>
                                    <a href="/books/<?= $loan['isbn'] ?>" class="text-decoration-none">
                                        <?= htmlspecialchars($loan['title']) ?>
                                    </a>
                                    <br>
                                    <small class="text-muted"><?= htmlspecialchars($loan['author']) ?></small>
                                </td>
                                <td><code><?= $loan['copy_code'] ?></code></td>
                                <td><?= date('d/m/y', strtotime($loan['loan_date'])) ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?= date('d/m/y', $dueDate) ?>
                                        <?php if ($isOverdue): ?>
                                            <span class="badge bg-danger ms-1">
                                                <?= floor((time() - $dueDate) / (60 * 60 * 24)) ?>d
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    $statusColors = [
                                        'active' => 'success',
                                        'overdue' => 'warning',
                                        'returned' => 'secondary',
                                        'lost' => 'danger'
                                    ];
                                    ?>
                                    <span class="badge bg-<?= $statusColors[$loan['status']] ?? 'secondary' ?>">
                                        <?= ucfirst($loan['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($loan['fine'] > 0): ?>
                                        <span class="badge bg-danger">$<?= number_format($loan['fine'], 2) ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-success">$0.00</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <?php if ($loan['status'] == 'active'): ?>
                                            <form method="POST" 
                                                  action="/loans/<?= $loan['id'] ?>/return" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('¿Marcar como devuelto?');">
                                                <button type="submit" class="btn btn-outline-success" 
                                                        data-bs-toggle="tooltip" title="Devolver">
                                                    <i class="bi bi-check-circle"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <button type="button" class="btn btn-outline-info" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#loanModal<?= $loan['id'] ?>"
                                                data-bs-toggle="tooltip" title="Ver detalles">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-warning"
                                                data-bs-toggle="tooltip" title="Renovar">
                                            <i class="bi bi-arrow-clockwise"></i>
                                        </button>
                                    </div>
                                    
                                    <!-- Modal para detalles del préstamo -->
                                    <div class="modal fade" id="loanModal<?= $loan['id'] ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Detalles del Préstamo</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <dl class="row">
                                                        <dt class="col-sm-4">Socio:</dt>
                                                        <dd class="col-sm-8">
                                                            <?= htmlspecialchars($loan['first_name'] . ' ' . $loan['last_name']) ?>
                                                            <br><small class="text-muted"><?= $loan['member_code'] ?></small>
                                                        </dd>
                                                        
                                                        <dt class="col-sm-4">Libro:</dt>
                                                        <dd class="col-sm-8">
                                                            <strong><?= htmlspecialchars($loan['title']) ?></strong>
                                                            <br><small class="text-muted"><?= htmlspecialchars($loan['author']) ?></small>
                                                        </dd>
                                                        
                                                        <dt class="col-sm-4">Copia:</dt>
                                                        <dd class="col-sm-8"><code><?= $loan['copy_code'] ?></code></dd>
                                                        
                                                        <dt class="col-sm-4">Fecha Préstamo:</dt>
                                                        <dd class="col-sm-8"><?= date('d/m/Y', strtotime($loan['loan_date'])) ?></dd>
                                                        
                                                        <dt class="col-sm-4">Fecha Vencimiento:</dt>
                                                        <dd class="col-sm-8"><?= date('d/m/Y', strtotime($loan['due_date'])) ?></dd>
                                                        
                                                        <?php if ($loan['return_date']): ?>
                                                            <dt class="col-sm-4">Fecha Devolución:</dt>
                                                            <dd class="col-sm-8"><?= date('d/m/Y', strtotime($loan['return_date'])) ?></dd>
                                                        <?php endif; ?>
                                                        
                                                        <dt class="col-sm-4">Días de préstamo:</dt>
                                                        <dd class="col-sm-8"><?= $loan['loan_days'] ?> días</dd>
                                                        
                                                        <dt class="col-sm-4">Multa:</dt>
                                                        <dd class="col-sm-8">
                                                            <?php if ($loan['fine'] > 0): ?>
                                                                <span class="badge bg-danger">$<?= number_format($loan['fine'], 2) ?></span>
                                                            <?php else: ?>
                                                                <span class="badge bg-success">Sin multa</span>
                                                            <?php endif; ?>
                                                        </dd>
                                                        
                                                        <?php if (!empty($loan['notes'])): ?>
                                                            <dt class="col-sm-4">Notas:</dt>
                                                            <dd class="col-sm-8"><?= nl2br(htmlspecialchars($loan['notes'])) ?></dd>
                                                        <?php endif; ?>
                                                    </dl>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                                    <?php if ($loan['status'] == 'active'): ?>
                                                        <form method="POST" action="/loans/<?= $loan['id'] ?>/return" class="d-inline">
                                                            <button type="submit" class="btn btn-success">
                                                                Marcar como Devuelto
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="/loans?page=<?= $page - 1 ?><?= $queryString ?>">
                                Anterior
                            </a>
                        </li>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" href="/loans?page=<?= $i ?><?= $queryString ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link" href="/loans?page=<?= $page + 1 ?><?= $queryString ?>">
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
                        <h6 class="card-title mb-0">Total Préstamos</h6>
                        <p class="card-text h4 mb-0"><?= $totalLoans ?? 0 ?></p>
                    </div>
                    <i class="bi bi-arrow-left-right display-6 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Préstamos Activos</h6>
                        <p class="card-text h4 mb-0"><?= $activeLoans ?? 0 ?></p>
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
                        <h6 class="card-title mb-0">Préstamos Vencidos</h6>
                        <p class="card-text h4 mb-0"><?= $overdueLoans ?? 0 ?></p>
                    </div>
                    <i class="bi bi-exclamation-triangle display-6 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Multas Pendientes</h6>
                        <p class="card-text h4 mb-0">$<?= number_format($totalFines ?? 0, 2) ?></p>
                    </div>
                    <i class="bi bi-currency-dollar display-6 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>
<?php 
$content = ob_get_clean(); 
$title = 'Préstamos';
require_once __DIR__ . '/../layouts/app.php'; 
?>