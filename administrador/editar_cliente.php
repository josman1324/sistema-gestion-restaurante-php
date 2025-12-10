<?php
include('../php/config.php');
include('../php/conexion.php');

// Protección de Admin
if (!isset($_SESSION['admin']) || $_SESSION['id_cargo'] != 1) {
    header("Location: " . $base_url . "login_universal.php");
    exit;
}

$id_cliente = $_GET['id'] ?? 0;
if ($id_cliente <= 0) {
    die("ID de cliente no válido.");
}

// Cargar los datos del cliente
$sql = "SELECT * FROM clientes WHERE id_cliente = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_cliente);
$stmt->execute();
$cliente = $stmt->get_result()->fetch_assoc();

if (!$cliente) {
    die("Cliente no encontrado.");
}

include('../includes/header.php');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="display-5"><i class="bi bi-pencil-square"></i> Editar Cliente</h1>
    <a href="gestion_clientes.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form action="guardar_cliente.php" method="POST">
                    <input type="hidden" name="id_cliente" value="<?php echo $cliente['id_cliente']; ?>">

                    <div class="mb-3">
                        <label for="nombre_cliente" class="form-label fw-bold">Nombre Completo</label>
                        <input type="text" class="form-control" id="nombre_cliente" name="nombre_cliente" value="<?php echo htmlspecialchars($cliente['nombre_cliente']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email_cliente" class="form-label fw-bold">Correo Electrónico (Login)</label>
                        <input type="email" class="form-control" id="email_cliente" name="email_cliente" value="<?php echo htmlspecialchars($cliente['email_cliente']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="telefono" class="form-label fw-bold">Teléfono (Opcional)</labe.l>
                        <input type="tel" class="form-control" id="telefono" name="telefono" value="<?php echo htmlspecialchars($cliente['telefono']); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="clave" class="form-label fw-bold">Nueva Contraseña</label>
                        <input type="password" class="form-control" id="clave" name="clave">
                        <small class="form-text text-muted">Dejar en blanco para no cambiar la contraseña actual.</small>
                    </div>
                    
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-success btn-lg">Actualizar Cliente</button>
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