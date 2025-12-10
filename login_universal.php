<?php
include('includes/header.php'); // El header ahora define $base_url

// Si el usuario ya está logueado, no tiene sentido hacer login.
if ($is_logged_in) {
    header("Location: " . $panel_link); // $panel_link ya tiene la URL base
    exit;
}
?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-lg border-0">
            <div class="card-body p-4">
                
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($_GET['error']); ?>
                    </div>
                <?php endif; ?>

                <form action="<?php echo $base_url; ?>validar_login_universal.php" method="POST">
                    <div class="mb-3">
                        <label for="usuario" class="form-label fw-bold">Usuario o Correo Electrónico</label>
                        <input type="text" class="form-control" id="usuario" name="usuario" required>
                    </div>
                    <div class="mb-3">
                        <label for="clave" class="form-label fw-bold">Contraseña</label>
                        <input type="password" class="form-control" id="clave" name="clave" required>
                    </div>
                    
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-dark btn-lg">Entrar</button>
                    </div>
                </form>

                <div class="text-center mt-3">
                    <p>¿No tienes cuenta? <a href="<?php echo $base_url; ?>registro.php">Regístrate aquí</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>