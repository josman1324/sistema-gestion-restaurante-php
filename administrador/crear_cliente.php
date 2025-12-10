<?php
include('../php/config.php');

// Protección de Admin
if (!isset($_SESSION['admin']) || $_SESSION['id_cargo'] != 1) {
    header("Location: " . $base_url . "login_universal.php");
    exit;
}

include('../includes/header.php');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="display-5"><i class="bi bi-plus-circle"></i> Crear Nuevo Cliente</h1>
    <a href="gestion_clientes.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form action="guardar_cliente.php" method="POST">
                    <input type="hidden" name="id_cliente" value="">

                    <div class="mb-3">
                        <label for="nombre_cliente" class="form-label fw-bold">Nombre Completo</label>
                        <input type="text" class="form-control" id="nombre_cliente" name="nombre_cliente" required>
                    </div>
                    <div class="mb-3">
                        <label for="email_cliente" class="form-label fw-bold">Correo Electrónico (Login)</label>
                        <input type="email" class="form-control" id="email_cliente" name="email_cliente" required>
                    </div>
                    <div class="mb-3">
                        <label for="telefono" class="form-label fw-bold">Teléfono (Opcional)</label>
                        <input type="tel" class="form-control" id="telefono" name="telefono">
                    </div>
                    <div class="mb-3">
                        <label for="clave" class="form-label fw-bold">Contraseña</label>
                        <input type="password" class="form-control" id="clave" name="clave" required>
                    </div>
                    
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-success btn-lg">Guardar Cliente</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php 
include('../includes/footer.php'); 
?>