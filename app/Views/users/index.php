<?php ob_start(); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Usuarios del Sistema</h1>
            <p class="text-muted mb-0">Gestión de usuarios con acceso al sistema</p>
        </div>
        <?php if ($_SESSION['user']['role'] === 'admin'): ?>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                <i class="bi bi-person-plus me-2"></i>Nuevo Usuario
            </button>
        <?php endif; ?>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <?php if (empty($users)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-people display-1 text-muted mb-3"></i>
                    <h4 class="text-muted">No hay usuarios registrados</h4>
                    <p class="text-muted">Comienza agregando un nuevo usuario</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="30%">Nombre</th>
                                <th width="30%">Email</th>
                                <th width="20%">Rol</th>
                                <th width="10%">Estado</th>
                                <th width="10%" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($user->name) ?></strong>
                                        <br>
                                        <small class="text-muted">
                                            ID: <?= $user->id ?>
                                        </small>
                                    </td>
                                    <td><?= htmlspecialchars($user->email) ?></td>
                                    <td>
                                        <?php 
                                        $roleColors = [
                                            'admin' => 'danger',
                                            'librarian' => 'primary',
                                            'user' => 'success'
                                        ];
                                        $color = $roleColors[$user->role] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $color ?>">
                                            <?= ucfirst($user->role) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($user->active): ?>
                                            <span class="badge bg-success">Activo</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inactivo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="/users/<?= $user->id ?>" 
                                               class="btn btn-outline-info"
                                               data-bs-toggle="tooltip" title="Ver">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="/users/<?= $user->id ?>/edit" 
                                               class="btn btn-outline-primary"
                                               data-bs-toggle="tooltip" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <?php if ($_SESSION['user']['role'] === 'admin' && $_SESSION['user']['id'] != $user->id): ?>
                                                <form method="POST" 
                                                      action="/users/<?= $user->id ?>/delete" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('¿Desactivar este usuario?');">
                                                    <button type="submit" 
                                                            class="btn btn-outline-danger"
                                                            data-bs-toggle="tooltip" 
                                                            title="Desactivar">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
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
</div>

<!-- Modal para crear usuario (solo admin) -->
<?php if ($_SESSION['user']['role'] === 'admin'): ?>
<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nuevo Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/users">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= \Core\Csrf::get() ?>">
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="role" class="form-label">Rol</label>
                        <select class="form-select" id="role" name="role">
                            <option value="user">Usuario</option>
                            <option value="librarian">Bibliotecario</option>
                            <option value="admin">Administrador</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Activar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
<?php 
$content = ob_get_clean(); 
$title = 'Usuarios';
require_once __DIR__ . '/../layouts/app.php'; 
?>