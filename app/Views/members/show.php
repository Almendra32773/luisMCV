<?php ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0"><?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) ?></h1>
        <p class="text-muted mb-0">Código: <?= htmlspecialchars($member['member_code']) ?></p>
    </div>
    <div>
        <a href="/members" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Volver
        </a>
        <a href="/members/<?= $member['id'] ?>/edit" class="btn btn-primary">
            <i class="bi bi-pencil me-2"></i>Editar
        </a>
        <a href="/loans/create?member_id=<?= $member['id'] ?>" class="btn btn-success">
            <i class="bi bi-plus-circle me-2"></i>Nuevo Préstamo
        </a>
    </div>
</div>

<div class="row">
    <!-- Información del socio -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body text-center">
                <div class="mb-4">
                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" 
                         style="width: 120px; height: 120px;">
                        <i class="bi bi-person display-4 text-white"></i>
                    </div>
                </div>
                
                <h5 class="card-title"><?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) ?></h5>
                <p class="card-text">
                    <span class="badge bg-info"><?= $member['member_code'] ?></span>
                </p>
                
                <div class="mt-4">
                    <?php if ($member['active']): ?>
                        <span class="badge bg-success fs-6 py-2 px-3">Socio Activo</span>
                    <?php else: ?>
                        <span class="badge bg-danger fs-6 py-2 px-3">Socio Inactivo</span>
                    <?php endif; ?>
                </div>
                
                <div class="mt-4">
                    <button class="btn btn-outline-primary w-100 mb-2">
                        <i class="bi bi-envelope me-2"></i>Enviar Email
                    </button>
                    <button class="btn btn-outline-secondary w-100">
                        <i class="bi bi-printer me-2"></i>Imprimir Carnet
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Estadísticas -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="bi bi-graph-up me-2"></i>Estadísticas
                </h5>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-8">Préstamos Activos:</dt>
                    <dd class="col-sm-4">
                        <span class="badge bg-<?= $activeLoansCount >= $member['max_loans'] ? 'danger' : 'success' ?>">
                            <?= $activeLoansCount ?>/<?= $member['max_loans'] ?>
                        </span>
                    </dd>
                    
                    <dt class="col-sm-8">Total Préstamos:</dt>
                    <dd class="col-sm-4"><?= $totalLoans ?></dd>
                    
                    <dt class="col-sm-8">Préstamos Vencidos:</dt>
                    <dd class="col-sm-4">
                        <?php if ($overdueLoansCount > 0): ?>
                            <span class="badge bg-danger"><?= $overdueLoansCount ?></span>
                        <?php else: ?>
                            <span class="badge bg-success">0</span>
                        <?php endif; ?>
                    </dd>
                    
                    <dt class="col-sm-8">Multas Pendientes:</dt>
                    <dd class="col-sm-4">
                        <?php if ($totalFines > 0): ?>
                            <span class="badge bg-warning">$<?= number_format($totalFines, 2) ?></span>
                        <?php else: ?>
                            <span class="badge bg-success">$0.00</span>
                        <?php endif; ?>
                    </dd>
                </dl>
            </div>
        </div>
    </div>
    
    <!-- Información detallada y préstamos -->
    <div class="col-lg-8">
        <!-- Información de contacto -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="bi bi-info-circle me-2"></i>Información de Contacto
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <dl>
                            <dt>Email:</dt>
                            <dd><?= htmlspecialchars($member['email']) ?></dd>
                            
                            <dt>Teléfono:</dt>
                            <dd><?= $member['phone'] ? htmlspecialchars($member['phone']) : '<span class="text-muted">No registrado</span>' ?></dd>
                            
                            <dt>Fecha de Nacimiento:</dt>
                            <dd>
                                <?= $member['birth_date'] ? date('d/m/Y', strtotime($member['birth_date'])) : '<span class="text-muted">No registrada</span>' ?>
                            </dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <dl>
                            <dt>Dirección:</dt>
                            <dd>
                                <?= $member['address'] ? nl2br(htmlspecialchars($member['address'])) : '<span class="text-muted">No registrada</span>' ?>
                            </dd>
                            
                            <dt>Fecha de Registro:</dt>
                            <dd><?= date('d/m/Y H:i', strtotime($member['created_at'])) ?></dd>
                            
                            <dt>Última Actualización:</dt>
                            <dd><?= date('d/m/Y H:i', strtotime($member['updated_at'])) ?></dd>
                        </dl>
                    </div>
                </div>
                
                <?php if (!empty($member['notes'])): ?>
                    <hr>
                    <h6>Notas:</h6>
                    <p class="text-justify"><?= nl2br(htmlspecialchars($member['notes'])) ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Préstamos activos -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-book me-2"></i>Préstamos Activos
                </h5>
                <span class="badge bg-primary"><?= count($activeLoans) ?></span>
            </div>
            <div class="card-body">
                <?php if (empty($activeLoans)): ?>
                    <p class="text-success text-center mb-0">
                        <i class="bi bi-check-circle me-2"></i>No tiene préstamos activos
                    </p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Libro</th>
                                    <th>Copia</th>
                                    <th>Fecha Préstamo</th>
                                    <th>Fecha Vencimiento</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($activeLoans as $loan): ?>
                                    <tr class="<?= strtotime($loan['due_date']) < time() ? 'table-warning' : '' ?>">
                                        <td>
                                            <a href="/books/<?= $loan['isbn'] ?>" class="text-decoration-none">
                                                <?= htmlspecialchars($loan['title']) ?>
                                            </a>
                                        </td>
                                        <td><code><?= $loan['copy_code'] ?></code></td>
                                        <td><?= date('d/m/Y', strtotime($loan['loan_date'])) ?></td>
                                        <td>
                                            <?= date('d/m/Y', strtotime($loan['due_date'])) ?>
                                            <?php if (strtotime($loan['due_date']) < time()): ?>
                                                <span class="badge bg-danger ms-1">Vencido</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $loan['status'] == 'active' ? 'success' : 'warning' ?>">
                                                <?= ucfirst($loan['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <form method="POST" 
                                                  action="/loans/<?= $loan['id'] ?>/return" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('¿Marcar como devuelto?');">
                                                <button type="submit" class="btn btn-sm btn-outline-success">
                                                    <i class="bi bi-check-circle"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Historial de préstamos -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="bi bi-clock-history me-2"></i>Historial de Préstamos
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($loanHistory)): ?>
                    <p class="text-muted text-center mb-0">No hay historial de préstamos</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Libro</th>
                                    <th>Fecha Préstamo</th>
                                    <th>Fecha Devolución</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($loanHistory as $loan): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($loan['title']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($loan['loan_date'])) ?></td>
                                        <td>
                                            <?= $loan['return_date'] ? date('d/m/Y', strtotime($loan['return_date'])) : '-' ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $loan['status'] == 'returned' ? 'secondary' : 'info' ?>">
                                                <?= ucfirst($loan['status']) ?>
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
<?php 
$content = ob_get_clean(); 
$title = 'Detalles del Socio';
require_once __DIR__ . '/../layouts/app.php'; 
?>