<?php ob_start(); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Editar Usuario</h1>
            <p class="text-muted mb-0">ID: <?= $user->id ?></p>
        </div>
        <a href="/users/<?= $user->id ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Volver
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="POST" action="/users/<?= $user->id ?>/update">
                        <input type="hidden" name="csrf_token" value="<?= \Core\Csrf::get() ?>">
                        
                        <div class="row">
                            <!-- Información básica -->
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nombre <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?= htmlspecialchars($user->name) ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= htmlspecialchars($user->email) ?>" required>
                            </div>
                            
                            <!-- Cambiar contraseña (solo para propio usuario o admin) -->
                            <?php if ($_SESSION['user']['id'] == $user->id || $_SESSION['user']['role'] === 'admin'): ?>
                                <div class="col-md-12 mb-4">
                                    <div class="card">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">
                                                <i class="bi bi-key me-2"></i>Cambiar Contraseña
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="password" class="form-label">Nueva Contraseña</label>
                                                    <input type="password" class="form-control" id="password" name="password">
                                                    <div class="form-text">Dejar en blanco para no cambiar</div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                                                    <input type="password" class="form-control" id="password_confirmation" 
                                                           name="password_confirmation">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Rol (solo admin puede cambiar) -->
                            <?php if ($_SESSION['user']['role'] === 'admin' && $_SESSION['user']['id'] != $user->id): ?>
                                <div class="col-md-6 mb-3">
                                    <label for="role" class="form-label">Rol</label>
                                    <select class="form-select" id="role" name="role">
                                        <option value="user" <?= $user->role == 'user' ? 'selected' : '' ?>>Usuario</option>
                                        <option value="librarian" <?= $user->role == 'librarian' ? 'selected' : '' ?>>Bibliotecario</option>
                                        <option value="admin" <?= $user->role == 'admin' ? 'selected' : '' ?>>Administrador</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="active" class="form-label">Estado</label>
                                    <select class="form-select" id="active" name="active">
                                        <option value="1" <?= $user->active ? 'selected' : '' ?>>Activo</option>
                                        <option value="0" <?= !$user->active ? 'selected' : '' ?>>Inactivo</option>
                                    </select>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="/users/<?= $user->id ?>" class="btn btn-outline-secondary">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Información de ayuda -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>Información
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="bi bi-shield-check me-2"></i>Permisos</h6>
                        <ul class="mb-0 ps-3">
                            <li><strong>Administrador:</strong> Acceso total al sistema</li>
                            <li><strong>Bibliotecario:</strong> Gestionar libros, socios y préstamos</li>
                            <li><strong>Usuario:</strong> Acceso limitado a consultas</li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-warning">
                        <h6><i class="bi bi-exclamation-triangle me-2"></i>Advertencias</h6>
                        <ul class="mb-0 ps-3">
                            <li>No puedes desactivar tu propia cuenta</li>
                            <li>Los cambios se aplican inmediatamente</li>
                            <li>Revisa los datos antes de guardar</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const password = document.getElementById('password');
    const passwordConfirmation = document.getElementById('password_confirmation');
    
    function validatePasswords() {
        if (password.value && passwordConfirmation.value) {
            if (password.value !== passwordConfirmation.value) {
                passwordConfirmation.setCustomValidity('Las contraseñas no coinciden');
            } else {
                passwordConfirmation.setCustomValidity('');
            }
        }
    }
    
    password.addEventListener('input', validatePasswords);
    passwordConfirmation.addEventListener('input', validatePasswords);
});
</script>
<?php 
$content = ob_get_clean(); 
$title = 'Editar Usuario';
require_once __DIR__ . '/../layouts/app.php'; 
?>