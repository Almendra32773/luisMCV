<?php ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Agregar Nuevo Libro</h1>
        <p class="text-muted mb-0">Completa el formulario para registrar un nuevo libro</p>
    </div>
    <a href="/books" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Volver
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="/books">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?= \Core\Csrf::get() ?>">
            
            <div class="row">
                <!-- ISBN -->
                <div class="col-md-6 mb-3">
                    <label for="isbn" class="form-label">ISBN <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="isbn" name="isbn" 
                           value="<?= $_POST['isbn'] ?? '' ?>" required 
                           placeholder="978-XXX-XXXXX-XX-X" maxlength="17">
                    <div class="form-text">Formato: 13 dígitos con guiones opcionales</div>
                </div>

                <!-- Título -->
                <div class="col-md-6 mb-3">
                    <label for="title" class="form-label">Título <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="title" name="title" 
                           value="<?= $_POST['title'] ?? '' ?>" required>
                </div>

                <!-- Autor -->
                <div class="col-md-6 mb-3">
                    <label for="author" class="form-label">Autor <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="author" name="author" 
                           value="<?= $_POST['author'] ?? '' ?>" required>
                </div>

                <!-- Editorial -->
                <div class="col-md-6 mb-3">
                    <label for="publisher" class="form-label">Editorial <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="publisher" name="publisher" 
                           value="<?= $_POST['publisher'] ?? '' ?>" required>
                </div>

                <!-- Año de publicación -->
                <div class="col-md-3 mb-3">
                    <label for="publication_year" class="form-label">Año de publicación</label>
                    <input type="number" class="form-control" id="publication_year" name="publication_year" 
                           value="<?= $_POST['publication_year'] ?? '' ?>" min="1000" max="<?= date('Y') ?>">
                </div>

                <!-- Número de páginas -->
                <div class="col-md-3 mb-3">
                    <label for="pages" class="form-label">Número de páginas</label>
                    <input type="number" class="form-control" id="pages" name="pages" 
                           value="<?= $_POST['pages'] ?? '' ?>" min="1">
                </div>

                <!-- Copias totales -->
                <div class="col-md-3 mb-3">
                    <label for="total_copies" class="form-label">Copias <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="total_copies" name="total_copies" 
                           value="<?= $_POST['total_copies'] ?? 1 ?>" min="1" max="100" required>
                    <div class="form-text">Número de copias físicas</div>
                </div>

                <!-- Idioma -->
                <div class="col-md-3 mb-3">
                    <label for="language" class="form-label">Idioma</label>
                    <select class="form-select" id="language" name="language">
                        <option value="Español" selected>Español</option>
                        <option value="Inglés">Inglés</option>
                        <option value="Francés">Francés</option>
                        <option value="Alemán">Alemán</option>
                        <option value="Portugués">Portugués</option>
                        <option value="Italiano">Italiano</option>
                    </select>
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
                                           id="category_<?= $category['id'] ?>">
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
                              rows="4" placeholder="Breve descripción del libro..."><?= $_POST['synopsis'] ?? '' ?></textarea>
                </div>
            </div>

            <!-- Buttons -->
            <div class="d-flex justify-content-end gap-2">
                <button type="reset" class="btn btn-outline-secondary">
                    <i class="bi bi-eraser me-2"></i>Limpiar
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-2"></i>Guardar Libro
                </button>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript para validación -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Validar ISBN
        const isbnInput = document.getElementById('isbn');
        isbnInput.addEventListener('blur', function() {
            let isbn = this.value.replace(/[-\s]/g, '');
            if (isbn.length !== 13 && isbn.length !== 10 && isbn.length > 0) {
                alert('El ISBN debe tener 10 o 13 dígitos');
                this.focus();
            }
        });
    });
</script>
<?php 
$content = ob_get_clean(); 
$title = 'Nuevo Libro';
require_once __DIR__ . '/../layouts/app.php'; 
?>