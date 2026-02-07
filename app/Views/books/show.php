<?php ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0"><?= htmlspecialchars($book['title']) ?></h1>
        <p class="text-muted mb-0">ISBN: <?= htmlspecialchars($book['isbn']) ?></p>
    </div>
    <div>
        <a href="/books" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Volver
        </a>
        <a href="/books/<?= $book['isbn'] ?>/edit" class="btn btn-primary">
            <i class="bi bi-pencil me-2"></i>Editar
        </a>
    </div>
</div>

<div class="row">
    <!-- Información principal -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center mb-3 mb-md-0">
                        <div class="bg-light rounded p-4 mb-3">
                            <i class="bi bi-book display-1 text-muted"></i>
                        </div>
                        <div class="d-grid gap-2">
                            <span class="badge bg-success fs-6 py-2">
                                <i class="bi bi-check-circle me-1"></i>
                                <?= $book['available_copies'] ?> copias disponibles
                            </span>
                            <span class="badge bg-secondary fs-6 py-2">
                                <?= $book['total_copies'] ?> copias totales
                            </span>
                        </div>
                    </div>
                    
                    <div class="col-md-8">
                        <h5 class="card-title mb-3">Información del Libro</h5>
                        
                        <dl class="row">
                            <dt class="col-sm-4">Autor:</dt>
                            <dd class="col-sm-8"><?= htmlspecialchars($book['author']) ?></dd>
                            
                            <dt class="col-sm-4">Editorial:</dt>
                            <dd class="col-sm-8"><?= htmlspecialchars($book['publisher']) ?></dd>
                            
                            <?php if ($book['publication_year']): ?>
                            <dt class="col-sm-4">Año de publicación:</dt>
                            <dd class="col-sm-8"><?= $book['publication_year'] ?></dd>
                            <?php endif; ?>
                            
                            <?php if ($book['pages']): ?>
                            <dt class="col-sm-4">Número de páginas:</dt>
                            <dd class="col-sm-8"><?= $book['pages'] ?></dd>
                            <?php endif; ?>
                            
                            <dt class="col-sm-4">Idioma:</dt>
                            <dd class="col-sm-8"><?= $book['language'] ?? 'Español' ?></dd>
                            
                            <dt class="col-sm-4">Categorías:</dt>
                            <dd class="col-sm-8">
                                <?php if (!empty($book['categories'])): ?>
                                    <?php 
                                    $categories = explode(', ', $book['categories']);
                                    foreach ($categories as $cat): ?>
                                        <span class="badge bg-info me-1 mb-1"><?= htmlspecialchars($cat) ?></span>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span class="text-muted">Sin categorías asignadas</span>
                                <?php endif; ?>
                            </dd>
                            
                            <dt class="col-sm-4">Estado:</dt>
                            <dd class="col-sm-8">
                                <?php if ($book['active']): ?>
                                    <span class="badge bg-success">Activo</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inactivo</span>
                                <?php endif; ?>
                            </dd>
                            
                            <dt class="col-sm-4">Fecha de registro:</dt>
                            <dd class="col-sm-8">
                                <?= date('d/m/Y H:i', strtotime($book['created_at'])) ?>
                            </dd>
                        </dl>
                        
                        <?php if (!empty($book['synopsis'])): ?>
                        <hr>
                        <h6 class="mb-3">Sinopsis</h6>
                        <p class="text-justify"><?= nl2br(htmlspecialchars($book['synopsis'])) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Copias físicas -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="bi bi-copy me-2"></i>Copias Físicas
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($copies)): ?>
                    <p class="text-muted text-center mb-0">No hay copias registradas</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Estado</th>
                                    <th>Ubicación</th>
                                    <th>Registro</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($copies as $copy): ?>
                                    <tr>
                                        <td><code><?= $copy['copy_code'] ?></code></td>
                                        <td>
                                            <?php
                                            $statusColors = [
                                                'available' => 'success',
                                                'borrowed' => 'warning',
                                                'reserved' => 'info',
                                                'maintenance' => 'secondary',
                                                'lost' => 'danger'
                                            ];
                                            $color = $statusColors[$copy['status']] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?= $color ?>">
                                                <?= ucfirst($copy['status']) ?>
                                            </span>
                                        </td>
                                        <td><?= $copy['location'] ?? 'General Shelf' ?></td>
                                        <td><?= date('d/m/Y', strtotime($copy['created_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Sidebar - Préstamos activos -->
    <div class="col-lg-4">
        <!-- Préstamos activos -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="bi bi-arrow-left-right me-2"></i>Préstamos Activos
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($activeLoans)): ?>
                    <p class="text-success text-center mb-0">
                        <i class="bi bi-check-circle me-2"></i>No hay préstamos activos
                    </p>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($activeLoans as $loan): ?>
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between mb-2">
                                    <h6 class="mb-0">
                                        <?= htmlspecialchars($loan['first_name'] . ' ' . $loan['last_name']) ?>
                                    </h6>
                                    <small class="text-muted"><?= $loan['member_code'] ?></small>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        Préstamo: <?= date('d/m/Y', strtotime($loan['loan_date'])) ?>
                                    </small>
                                    <small class="text-muted">
                                        Vence: <?= date('d/m/Y', strtotime($loan['due_date'])) ?>
                                    </small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Acciones rápidas -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="bi bi-lightning me-2"></i>Acciones Rápidas
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="/loans/create?book_isbn=<?= $book['isbn'] ?>" 
                       class="btn btn-success" 
                       <?= $book['available_copies'] <= 0 ? 'disabled' : '' ?>>
                        <i class="bi bi-plus-circle me-2"></i>Nuevo Préstamo
                    </a>
                    
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" 
                            data-bs-target="#reserveModal">
                        <i class="bi bi-bookmark me-2"></i>Reservar Copia
                    </button>
                    
                    <form method="POST" action="/books/<?= $book['isbn'] ?>/delete" 
                          class="d-grid" 
                          onsubmit="return confirm('¿Eliminar este libro? Se marcará como inactivo.');">
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="bi bi-trash me-2"></i>Eliminar Libro
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Estadísticas -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="bi bi-graph-up me-2"></i>Estadísticas
                </h5>
            </div>
            <div class="card-body">
                <?php
                $totalLoans = \R::count('loan l JOIN copy c ON l.copy_id = c.id', 'c.isbn = ?', [$book['isbn']]);
                $activeLoansCount = \R::count('loan l JOIN copy c ON l.copy_id = c.id', 
                    'c.isbn = ? AND l.status = ?', [$book['isbn'], 'active']);
                $returnedLoans = \R::count('loan l JOIN copy c ON l.copy_id = c.id', 
                    'c.isbn = ? AND l.status = ?', [$book['isbn'], 'returned']);
                ?>
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="display-6 fw-bold"><?= $totalLoans ?></div>
                        <small class="text-muted">Total Préstamos</small>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="display-6 fw-bold"><?= $activeLoansCount ?></div>
                        <small class="text-muted">Préstamos Activos</small>
                    </div>
                    <div class="col-6">
                        <div class="display-6 fw-bold"><?= $returnedLoans ?></div>
                        <small class="text-muted">Devueltos</small>
                    </div>
                    <div class="col-6">
                        <div class="display-6 fw-bold"><?= $book['available_copies'] ?></div>
                        <small class="text-muted">Disponibles</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de reserva -->
<div class="modal fade" id="reserveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reservar Copia</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Seleccione un socio para reservar una copia de este libro:</p>
                <select class="form-select">
                    <option selected>Seleccionar socio...</option>
                    <!-- Aquí irían los socios desde la base de datos -->
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary">Confirmar Reserva</button>
            </div>
        </div>
    </div>
</div>
<?php 
$content = ob_get_clean(); 
$title = 'Detalles del Libro';
require_once __DIR__ . '/../layouts/app.php'; 
?>