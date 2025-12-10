<?php
// 1. Incluimos el header.php (que define $base_url e inicia la sesión)
include('includes/header.php'); 

// 2. Si el usuario ya está logueado, no tiene sentido registrarse.
if ($is_logged_in) {
    header("Location: " . $panel_link); // $panel_link ya tiene la URL base
    exit;
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-success text-white text-center">
                <h4 class="mb-0"><i class="bi bi-person-plus-fill"></i> Crea tu cuenta</h4>
            </div>
            <div class="card-body p-4">
                <p class="text-center text-muted">Regístrate para hacer pedidos más rápido.</p>
                
                <form action="<?php echo $base_url; ?>guardar_registro.php" method="POST">
                    
                    <div class="mb-3">
                        <label for="nombre" class="form-label fw-bold">Nombre Completo</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold">Correo Electrónico</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="telefono" class="form-label fw-bold">Teléfono (Opcional)</label>
                        <input type="tel" class="form-control" id="telefono" name="telefono">
                    </div>
                    
                    <div class="mb-3">
                        <label for="clave" class="form-label fw-bold">Contraseña</label>
                        <input type="password" class="form-control" id="clave" name="clave" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirmar_clave" class="form-label fw-bold">Confirmar Contraseña</label>
                        <input type="password" class="form-control" id="confirmar_clave" name="confirmar_clave" required>
                    </div>
                    
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-success btn-lg">Registrarme</button>
                    </div>
                </form>

                <div class="text-center mt-3">
                    <p>¿Ya tienes cuenta? <a href="<?php echo $base_url; ?>login_universal.php">Inicia Sesión</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>