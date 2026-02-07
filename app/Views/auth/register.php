<?php ob_start(); ?>
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow border-0">
            <div class="card-header bg-success text-white text-center py-3">
                <h4 class="mb-0">
                    <i class="bi bi-person-plus me-2"></i>
                    Crear Nueva Cuenta
                </h4>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="/register">
                    <!-- CSRF Token -->
                    <input type="hidden" name="csrf_token" value="<?= \Core\Csrf::get() ?>">
                    
                    <div class="row">
                        <!-- Name -->
                        <div class="col-md-12 mb-3">
                            <label for="name" class="form-label">Nombre Completo</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-person"></i>
                                </span>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?= $_POST['name'] ?? '' ?>" required 
                                       placeholder="Juan Pérez">
                            </div>
                        </div>
                        
                        <!-- Email -->
                        <div class="col-md-12 mb-3">
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
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input type="password" class="form-control" id="password" 
                                       name="password" required placeholder="••••••••">
                            </div>
                            <div class="form-text">
                                Mínimo 6 caracteres
                            </div>
                        </div>
                        
                        <!-- Confirm Password -->
                        <div class="col-md-6 mb-3">
                            <label for="confirm_password" class="form-label">Confirmar Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-lock-fill"></i>
                                </span>
                                <input type="password" class="form-control" id="confirm_password" 
                                       name="confirm_password" required placeholder="••••••••">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Terms -->
                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="terms" required>
                            <label class="form-check-label" for="terms">
                                Acepto los <a href="#" class="text-decoration-none">términos y condiciones</a>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Submit -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="bi bi-person-plus me-2"></i>
                            Crear Cuenta
                        </button>
                        <a href="/login" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>
                            Volver al Login
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php require_once __DIR__ . '/../layouts/guest.php'; ?>