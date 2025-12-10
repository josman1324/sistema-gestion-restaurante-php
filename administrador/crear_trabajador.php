<?php
include('../php/config.php');
include('../php/conexion.php'); // Necesitamos conexión para leer los cargos

// Protección de Admin
if (!isset($_SESSION['admin']) || $_SESSION['id_cargo'] != 1) {
    header("Location: " . $base_url . "login_universal.php");
    exit;
}

// Consultar los roles/cargos disponibles
$cargos = $conexion->query("SELECT id_cargo, nombre_cargo FROM cargos ORDER BY nombre_cargo");

include('../includes/header.php');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="display-5"><i class="bi bi-plus-circle"></i> Crear Nuevo Trabajador</h1>
    <a href="gestion_trabajadores.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form action="guardar_trabajador.php" method="POST">
                    <input type="hidden" name="id_trabajador" value="">

                    <div class="mb-3">
                        <label for="nombre_trabajador" class="form-label fw-bold">Nombre Completo</label>
                        <input type="text" class="form-control" id="nombre_trabajador" name="nombre_trabajador" required>
                    </div>
                    <div class="mb-3">
                        <label for="usuario" class="form-label fw-bold">Usuario (para iniciar sesión)</label>
                        <input type="text" class="form-control" id="usuario" name="usuario" required>
                    </div>
                    <div class="mb-3">
                        <label for="clave" class="form-label fw-bold">Contraseña</label>
                        <input type="password" class="form-control" id="clave" name="clave" required>
                    </div>
                    <div class="mb-3">
                        <label for="id_cargo" class="form-label fw-bold">Cargo</label>
                        <select class="form-select" id="id_cargo" name="id_cargo" required>
                            <option value="" disabled selected>Selecciona un rol...</option>
                            <?php while($cargo = $cargos->fetch_assoc()): ?>
                                <option value="<?php echo $cargo['id_cargo']; ?>"><?php echo htmlspecialchars($cargo['nombre_cargo']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-success btn-lg">Guardar Trabajador</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php 
$conexion->close();
include('../includes/footer.php'); 
?>