<?php ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Registrar Nuevo Socio</h1>
        <p class="text-muted mb-0">Complete el formulario para registrar un nuevo socio</p>
    </div>
    <a href="/members" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Volver
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="/members">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?= \Core\Csrf::get() ?>">
            
            <div class="row">
                <!-- Información personal -->
                <div class="col-md-6 mb-3">
                    <label for="first_name" class="form-label">Nombre <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="first_name" name="first_name" 
                           value="<?= $_POST['first_name'] ?? '' ?>" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="last_name" class="form-label">Apellido <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="last_name" name="last_name" 
                           value="<?= $_POST['last_name'] ?? '' ?>" required>
                </div>

                <!-- Contacto -->
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email" 
                           value="<?= $_POST['email'] ?? '' ?>" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">Teléfono</label>
                    <input type="tel" class="form-control" id="phone" name="phone" 
                           value="<?= $_POST['phone'] ?? '' ?>">
                </div>

                <!-- Fecha de nacimiento -->
                <div class="col-md-6 mb-3">
                    <label for="birth_date" class="form-label">Fecha de Nacimiento</label>
                    <input type="date" class="form-control" id="birth_date" name="birth_date" 
                           value="<?= $_POST['birth_date'] ?? '' ?>">
                </div>

                <!-- Límite de préstamos -->
                <div class="col-md-6 mb-3">
                    <label for="max_loans" class="form-label">Límite de Préstamos</label>
                    <select class="form-select" id="max_loans" name="max_loans">
                        <option value="3" <?= ($_POST['max_loans'] ?? 5) == 3 ? 'selected' : '' ?>>3 libros</option>
                        <option value="5" <?= ($_POST['max_loans'] ?? 5) == 5 ? 'selected' : '' ?>>5 libros</option>
                        <option value="10" <?= ($_POST['max_loans'] ?? 5) == 10 ? 'selected' : '' ?>>10 libros</option>
                        <option value="15" <?= ($_POST['max_loans'] ?? 5) == 15 ? 'selected' : '' ?>>15 libros</option>
                    </select>
                    <div class="form-text">Número máximo de libros que puede tener prestados simultáneamente</div>
                </div>

                <!-- Dirección -->
                <div class="col-md-12 mb-3">
                    <label for="address" class="form-label">Dirección</label>
                    <textarea class="form-control" id="address" name="address" 
                              rows="2"><?= $_POST['address'] ?? '' ?></textarea>
                </div>

                <!-- Notas -->
                <div class="col-md-12 mb-4">
                    <label for="notes" class="form-label">Notas adicionales</label>
                    <textarea class="form-control" id="notes" name="notes" 
                              rows="3" placeholder="Información adicional sobre el socio..."><?= $_POST['notes'] ?? '' ?></textarea>
                </div>
            </div>

            <!-- Buttons -->
            <div class="d-flex justify-content-end gap-2">
                <button type="reset" class="btn btn-outline-secondary">
                    <i class="bi bi-eraser me-2"></i>Limpiar
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-2"></i>Guardar Socio
                </button>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript para generación de código -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const firstNameInput = document.getElementById('first_name');
    const lastNameInput = document.getElementById('last_name');
    
    function generateMemberCode() {
        const firstName = firstNameInput.value.trim();
        const lastName = lastNameInput.value.trim();
        
        if (firstName && lastName) {
            const initials = (firstName.charAt(0) + lastName.charAt(0)).toUpperCase();
            const timestamp = Date.now().toString().slice(-4);
            const memberCode = `MEM${initials}${timestamp}`;
            
            // Aquí podrías mostrar el código generado o guardarlo en un campo oculto
            console.log('Código generado:', memberCode);
        }
    }
    
    firstNameInput.addEventListener('blur', generateMemberCode);
    lastNameInput.addEventListener('blur', generateMemberCode);
});
</script>
<?php 
$content = ob_get_clean(); 
$title = 'Nuevo Socio';
require_once __DIR__ . '/../layouts/app.php'; 
?>