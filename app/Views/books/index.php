<?php ob_start(); ?>
<?php $books = $books ?? []; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Gestión de Libros</h1>
        <p class="text-muted mb-0">Administra el catálogo de libros de la biblioteca</p>
    </div>
    <a href="/books/create" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Nuevo Libro
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <!-- Search Bar -->
        <div class="row mb-4">
            <div class="col-md-8">
                <form method="GET" action="/books" class="input-group">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Buscar por título, autor o ISBN..." 
                           value="<?= htmlspecialchars($search ?? '') ?>">
                    <button type="submit" class="btn btn-outline-secondary">
                        <i class="bi bi-search"></i>
                    </button>
                    <?php if (!empty($search)): ?>
                        <a href="/books" class="btn btn-outline-danger">
                            <i class="bi bi-x-circle"></i>
                        </a>
                    <?php endif; ?>
                </form>
            </div>
            <div class="col-md-4 text-md-end mt-2 mt-md-0">
                <span class="text-muted">
                    Mostrando <?= count($books) ?> libro(s)
                </span>
            </div>
        </div>

        <!-- Books Table -->
        <?php if (empty($books)): ?>
            <div class="text-center py-5">
                <i class="bi bi-book display-1 text-muted mb-3"></i>
                <h4 class="text-muted">No se encontraron libros</h4>
                <?php if (!empty($search)): ?>
                    <p class="text-muted">Intenta con otros términos de búsqueda</p>
                <?php else: ?>
                    <p class="text-muted">Comienza agregando un nuevo libro al catálogo</p>
                    <a href="/books/create" class="btn btn-primary mt-2">
                        <i class="bi bi-plus-circle me-2"></i>Agregar Primer Libro
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="15%">ISBN</th>
                            <th width="30%">Título</th>
                            <th width="20%">Autor</th>
                            <th width="15%">Editorial</th>
                            <th width="10%">Copias</th>
                            <th width="10%" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($books as $book): ?>
                            <tr>
                                <td>
                                    <code><?= htmlspecialchars($book['isbn']) ?></code>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($book['title']) ?></strong>
                                    <?php if (!empty($book['categories'])): ?>
                                        <br>
                                        <small class="text-muted">
                                            <?= htmlspecialchars($book['categories']) ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($book['author']) ?></td>
                                <td><?= htmlspecialchars($book['publisher']) ?></td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="badge bg-success mb-1">
                                            <?= $book['available_copies'] ?> disp.
                                        </span>
                                        <span class="badge bg-secondary">
                                            <?= $book['total_copies'] ?> total
                                        </span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="/books/<?= $book['isbn'] ?>" 
                                           class="btn btn-outline-info" 
                                           data-bs-toggle="tooltip" title="Ver detalles">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="/books/<?= $book['isbn'] ?>/edit" 
                                           class="btn btn-outline-primary"
                                           data-bs-toggle="tooltip" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" 
                                              action="/books/<?= $book['isbn'] ?>/delete" 
                                              class="d-inline"
                                              onsubmit="return confirm('¿Eliminar este libro?');">
                                            <button type="submit" 
                                                    class="btn btn-outline-danger"
                                                    data-bs-toggle="tooltip" 
                                                    title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php 
$content = ob_get_clean(); 
$title = 'Libros';
require_once __DIR__ . '/../layouts/app.php'; 
?>