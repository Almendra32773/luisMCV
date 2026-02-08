<?php ob_start(); ?>

<!-- Validación de la variable $categories -->
<?php $categories = $categories ?? []; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Categorías</h1>
        <p class="text-muted mb-0">Gestión de categorías de libros</p>
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
        <i class="bi bi-plus-circle me-2"></i>Nueva Categoría
    </button>
</div>

<div class="row">
    <?php foreach ($categories as $category): ?>
        <div class="col-md-4 col-lg-3 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h5 class="card-title mb-0"><?= htmlspecialchars($category['name']) ?></h5>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary" type="button" 
                                    data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <button class="dropdown-item" type="button" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editCategoryModal<?= $category['id'] ?>">
                                        <i class="bi bi-pencil me-2"></i>Editar
                                    </button>
                                </li>
                                <li>
                                    <button class="dropdown-item text-danger" type="button"
                                            onclick="deleteCategory(<?= $category['id'] ?>)">
                                        <i class="bi bi-trash me-2"></i>Eliminar
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <p class="card-text small text-muted">
                        <?= htmlspecialchars($category['description'] ?? 'Sin descripción') ?>
                    </p>
                    
                    <div class="mt-3">
                        <span class="badge bg-info">
                            <i class="bi bi-book me-1"></i>
                            <?= $category['book_count'] ?? 0 ?> libros
                        </span>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0">
                    <small class="text-muted">
                        Creada: <?= date('d/m/Y', strtotime($category['created_at'] ?? 'now')) ?>
                    </small>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Modal para crear categoría -->
<div class="modal fade" id="createCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nueva Categoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/categories">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= \Core\Csrf::get() ?>">
                    
                    <div class="mb-3">
                        <label for="category_name" class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="category_name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="category_description" class="form-label">Descripción</label>
                        <textarea class="form-control" id="category_description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Categoría</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modales para editar cada categoría -->
<?php foreach ($categories as $category): ?>
<div class="modal fade" id="editCategoryModal<?= $category['id'] ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Categoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/categories/<?= $category['id'] ?>/update">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= \Core\Csrf::get() ?>">
                    
                    <div class="mb-3">
                        <label for="edit_name_<?= $category['id'] ?>" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="edit_name_<?= $category['id'] ?>" 
                               name="name" value="<?= htmlspecialchars($category['name']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_description_<?= $category['id'] ?>" class="form-label">Descripción</label>
                        <textarea class="form-control" id="edit_description_<?= $category['id'] ?>"
                                  name="description" rows="3"><?= htmlspecialchars($category['description']) ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<script>
function deleteCategory(categoryId) {
    if (confirm('¿Eliminar esta categoría? Los libros mantendrán esta categoría asignada.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/categories/${categoryId}/delete`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = 'csrf_token';
        csrfToken.value = '<?= \Core\Csrf::get() ?>';
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php 
$content = ob_get_clean(); 
$title = 'Categorías';
require_once __DIR__ . '/../layouts/app.php'; 
?>