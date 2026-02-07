<?php ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Editar Libro</h1>
        <p class="text-muted mb-0">ISBN: <?= htmlspecialchars($book['isbn']) ?></p>
    </div>
    <a href="/books" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Volver
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="/books/<?= $book['isbn'] ?>/update">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?= \Core\Csrf::get() ?>">
            
            <div class="row">
                <!-- ISBN (readonly) -->
                <div class="col-md-6 mb-3">
                    <label for="isbn" class="form-label">ISBN</label>
                    <input type="text" class="form-control bg-light" id="isbn" 
                           value="<?= htmlspecialchars($book['isbn']) ?>" readonly>
                    <div class="form-text">El ISBN no se puede modificar</div>
                </div>

                <!-- Título -->
                <div class="col-md-6 mb-3">
                    <label for="title" class="form-label">Título <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="title" name="title" 
                           value="<?= htmlspecialchars($book['title']) ?>" required>
                </div>

                <!-- Autor -->
                <div class="col-md-6 mb-3">
                    <label for="author" class="form-label">Autor <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="author" name="author" 
                           value="<?= htmlspecialchars($book['author']) ?>" required>
                </div>

                <!-- Editorial -->
                <div class="col-md-6 mb-3">
                    <label for="publisher" class="form-label">Editorial <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="publisher" name="publisher" 
                           value="<?= htmlspecialchars($book['publisher']) ?>" required>
                </div>

                <!-- Año de publicación -->
                <div class="col-md-3 mb-3">
                    <label for="publication_year" class="form-label">Año de publicación</label>
                    <input type="number" class="form-control" id="publication_year" name="publication_year" 
                           value="<?= $book['publication_year'] ?>" min="1000" max="<?= date('Y') ?>">
                </div>

                <!-- Número de páginas -->
                <div class="col-md-3 mb-3">
                    <label for="pages" class="form-label">Número de páginas</label>
                    <input type="number" class="form-control" id="pages" name="pages" 
                           value="<?= $book['pages'] ?>" min="1">
                </div>

                <!-- Copias disponibles -->
                <div class="col-md-3 mb-3">
                    <label for="available_copies" class="form-label">Copias disponibles</label>
                    <input type="number" class="form-control" id="available_copies" name="available_copies" 
                           value="<?= $book['available_copies'] ?>" min="0" max="<?= $book['total_copies'] ?>" required>
                </div>

                <!-- Copias totales -->
                <div class="col-md-3 mb-3">
                    <label for="total_copies" class="form-label">Copias totales <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="total_copies" name="total_copies" 
                           value="<?= $book['total_copies'] ?>" min="1" max="100" required>
                </div>

                <!-- Categorías -->
                <div class="col-md-12 mb-3">
                    <label class="form-label">Categorías</label>
                    <div class="row">
                        <?php foreach ($categories as $category): ?>
                            <div class="col-md-3 col-6 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                           name="categories[]" value="<?= $category['id'] ?>"
                                           id="category_<?= $category['id'] ?>"
                                           <?= in_array($category['id'], $book['category_ids']) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="category_<?= $category['id'] ?>">
                                        <?= htmlspecialchars($category['name']) ?>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Sinopsis -->
                <div class="col-md-12 mb-4">
                    <label for="synopsis" class="form-label">Sinopsis</label>
                    <textarea class="form-control" id="synopsis" name="synopsis" 
                              rows="4"><?= htmlspecialchars($book['synopsis']) ?></textarea>
                </div>
            </div>

            <!-- Buttons -->
            <div class="d-flex justify-content-end gap-2">
                <a href="/books/<?= $book['isbn'] ?>" class="btn btn-outline-info">
                    <i class="bi bi-eye me-2"></i>Ver Detalles
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-2"></i>Actualizar Libro
                </button>
            </div>
        </form>
    </div>
</div>
<?php 
$content = ob_get_clean(); 
$title = 'Editar Libro';
require_once __DIR__ . '/../layouts/app.php'; 
?>