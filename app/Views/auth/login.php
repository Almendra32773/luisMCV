<?php ob_start(); ?>
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow border-0">
            <div class="card-header bg-primary text-white text-center py-3">
                <h4 class="mb-0">
                    <i class="bi bi-box-arrow-in-right me-2"></i>
                    Iniciar Sesión
                </h4>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="/login">
                    <!-- CSRF Token -->
                    <input type="hidden" name="csrf_token" value="<?= \Core\Csrf::get() ?>">
                    
                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= $_POST['email'] ?? '' ?>" required 
                                   placeholder="usuario@ejemplo.com">
                        </div>
                    </div>
                    
                    <!-- Password -->
                    <div class="mb-4">
                        <label for="password" class="form-label">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input type="password" class="form-control" id="password" 
                                   name="password" required placeholder="••••••••">
                        </div>
                        <div class="form-text text-end">
                            <a href="#" class="text-decoration-none">¿Olvidaste tu contraseña?</a>
                        </div>
                    </div>
                    
                    <!-- Submit -->
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            Ingresar al Sistema
                        </button>
                    </div>
                    
                    <!-- Divider -->
                    <div class="text-center my-4">
                        <span class="text-muted">¿No tienes una cuenta?</span>
                    </div>
                    
                    <!-- Register Link -->
                    <div class="d-grid">
                        <a href="/register" class="btn btn-outline-primary">
                            <i class="bi bi-person-plus me-2"></i>
                            Crear Nueva Cuenta
                        </a>
                    </div>
                </form>
                
                <!-- Demo Credentials -->
                <div class="mt-4 pt-3 border-top">
                    <h6 class="text-center text-muted mb-3">
                        <i class="bi bi-info-circle me-2"></i>Credenciales de Prueba
                    </h6>
                    <div class="row g-2">
                        <div class="col-12">
                            <div class="alert alert-info alert-sm mb-0">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <i class="bi bi-person-badge"></i>
                                    </div>
                                    <div>
                                        <small class="d-block fw-bold">Administrador</small>
                                        <small>admin@biblioteca.com / 123456</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="alert alert-secondary alert-sm mb-0">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <i class="bi bi-person"></i>
                                    </div>
                                    <div>
                                        <small class="d-block fw-bold">Bibliotecario</small>
                                        <small>librarian@biblioteca.com / 123456</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php require_once __DIR__ . '/../layouts/guest.php'; ?>