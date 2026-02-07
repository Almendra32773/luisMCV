<?php ob_start(); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Perfil de Usuario</h1>
            <p class="text-muted mb-0">ID: <?= $user->id ?></p>
        </div>
        <div>
            <a href="/users" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
            <?php if ($_SESSION['user']['id'] == $user->id || $_SESSION['user']['role'] === 'admin'): ?>
                <a href="/users/<?= $user->id ?>/edit" class="btn btn-primary">
                    <i class="bi bi-pencil me-2"></i>Editar
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center">
                    <div class="mb-4">
                        <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" 
                             style="width: 120px; height: 120px;">
                            <i class="bi bi-person display-4 text-white"></i>
                        </div>
                    </div>
                    
                    <h5 class="card-title"><?= htmlspecialchars($user->name) ?></h5>
                    <p class="card-text">
                        <?php 
                        $roleColors = [
                            'admin' => 'danger',
                            'librarian' => 'primary',
                            'user' => 'success'
                        ];
                        $color = $roleColors[$user->role] ?? 'secondary';
                        ?>
                        <span class="badge bg-<?= $color ?> fs-6">
                            <?= ucfirst($user->role) ?>
                        </span>
                    </p>
                    
                    <div class="mt-4">
                        <?php if ($user->active): ?>
                            <span class="badge bg-success fs-6 py-2 px-3">Usuario Activo</span>
                        <?php else: ?>
                            <span class="badge bg-danger fs-6 py-2 px-3">Usuario Inactivo</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Estadísticas -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-graph-up me-2"></i>Información
                    </h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-6">Registro:</dt>
                        <dd class="col-sm-6">
                            <?= date('d/m/Y', strtotime($user->created_at)) ?>
                        </dd>
                        
                        <dt class="col-sm-6">Actualización:</dt>
                        <dd class="col-sm-6">
                            <?= date('d/m/Y', strtotime($user->updated_at)) ?>
                        </dd>
                        
                        <dt class="col-sm-6">Sesión activa:</dt>
                        <dd class="col-sm-6">
                            <?php if ($_SESSION['user']['id'] == $user->id): ?>
                                <span class="badge bg-success">Sí</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">No</span>
                            <?php endif; ?>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <!-- Información detallada -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>Información Detallada
                    </h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">ID:</dt>
                        <dd class="col-sm-9"><?= $user->id ?></dd>
                        
                        <dt class="col-sm-3">Nombre:</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars($user->name) ?></dd>
                        
                        <dt class="col-sm-3">Email:</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars($user->email) ?></dd>
                        
                        <dt class="col-sm-3">Rol:</dt>
                        <dd class="col-sm-9">
                            <span class="badge bg-<?= $color ?>">
                                <?= ucfirst($user->role) ?>
                            </span>
                        </dd>
                        
                        <dt class="col-sm-3">Estado:</dt>
                        <dd class="col-sm-9">
                            <?php if ($user->active): ?>
                                <span class="badge bg-success">Activo</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Inactivo</span>
                            <?php endif; ?>
                        </dd>
                    </dl>
                    
                    <?php if ($_SESSION['user']['id'] == $user->id): ?>
                        <hr>
                        <div class="d-grid gap-2">
                            <a href="/users/<?= $user->id ?>/edit?section=password" 
                               class="btn btn-outline-warning">
                                <i class="bi bi-key me-2"></i>Cambiar Contraseña
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Actividad reciente -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history me-2"></i>Última Actividad
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-0">
                        <i class="bi bi-calendar me-2"></i>
                        Última actualización: <?= date('d/m/Y H:i', strtotime($user->updated_at)) ?>
                    </p>
                    <p class="text-muted mb-0">
                        <i class="bi bi-calendar-plus me-2"></i>
                        Fecha de registro: <?= date('d/m/Y H:i', strtotime($user->created_at)) ?>
                    </p>
                    
                    <?php if ($_SESSION['user']['id'] == $user->id): ?>
                        <hr>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Tu sesión actual:</strong><br>
                            IP: <?= $_SERVER['REMOTE_ADDR'] ?? 'Desconocida' ?><br>
                            Navegador: <?= $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido' ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php 
$content = ob_get_clean(); 
$title = 'Perfil de Usuario';
require_once __DIR__ . '/../layouts/app.php'; 
?>