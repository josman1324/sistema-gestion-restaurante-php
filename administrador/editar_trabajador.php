<?php
include('../php/config.php');
include('../php/conexion.php');

// Protección de Admin
if (!isset($_SESSION['admin']) || $_SESSION['id_cargo'] != 1) {
    header("Location: " . $base_url . "login_universal.php");
    exit;
}

$id_trabajador = $_GET['id'] ?? 0;
if ($id_trabajador <= 0) {
    die("ID de trabajador no válido.");
}

// Cargar los datos del trabajador
$sql = "SELECT * FROM trabajadores WHERE id_trabajador = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_trabajador);
$stmt->execute();
$trabajador = $stmt->get_result()->fetch_assoc();

if (!$trabajador) {
    die("Trabajador no encontrado.");
}

// Consultar los roles/cargos disponibles
$cargos = $conexion->query("SELECT id_cargo, nombre_cargo FROM cargos ORDER BY nombre_cargo");

include('../includes/header.php');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="display-5"><i class="bi bi-pencil-square"></i> Editar Trabajador</h1>
    <a href="gestion_trabajadores.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form action="guardar_trabajador.php" method="POST">
                    <input type="hidden" name="id_trabajador" value="<?php echo $trabajador['id_trabajador']; ?>">

                    <div class="mb-3">
                        <label for="nombre_trabajador" class="form-label fw-bold">Nombre Completo</label>
                        <input type="text" class="form-control" id="nombre_trabajador" name="nombre_trabajador" value="<?php echo htmlspecialchars($trabajador['nombre_trabajador']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="usuario" class="form-label fw-bold">Usuario (para iniciar sesión)</label>
                        <input type="text" class="form-control" id="usuario" name="usuario" value="<?php echo htmlspecialchars($trabajador['usuario']); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="clave" class="form-label fw-bold">Nueva Contraseña</label>
                        <input type="password" class="form-control" id="clave" name="clave">
                        <small class="form-text text-muted">Dejar en blanco para no cambiar la contraseña actual.</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="id_cargo" class="form-label fw-bold">Cargo</label>
                        <select class="form-select" id="id_cargo" name="id_cargo" required>
                            <option value="" disabled>Selecciona un rol...</option>
                            <?php while($cargo = $cargos->fetch_assoc()): ?>
                                <option value="<?php echo $cargo['id_cargo']; ?>" <?php echo ($cargo['id_cargo'] == $trabajador['id_cargo']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cargo['nombre_cargo']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-success btn-lg">Actualizar Trabajador</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php 
$stmt->close();
$conexion->close();
include('../includes/footer.php'); 
?>