<?php
include('../php/config.php');
include('../php/conexion.php');

// Protección de Admin
if (!isset($_SESSION['admin']) || $_SESSION['id_cargo'] != 1) {
    header("Location: " . $base_url . "login_universal.php");
    exit;
}

// Consultar todos los clientes
$sql = "SELECT id_cliente, nombre_cliente, email_cliente, telefono FROM clientes ORDER BY nombre_cliente ASC";
$result = $conexion->query($sql);

include('../includes/header.php');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="display-5"><i class="bi bi-people-fill"></i> Gestionar Clientes</h1>
    <div>
        <a href="crear_cliente.php" class="btn btn-success"><i class="bi bi-plus-circle"></i> Crear Nuevo Cliente</a>
        <a href="opciones_admin.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nombre Completo</th>
                        <th>Email (Login)</th>
                        <th>Teléfono</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($cliente = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="fw-bold"><?php echo $cliente['id_cliente']; ?></td>
                                <td><?php echo htmlspecialchars($cliente['nombre_cliente']); ?></td>
                                <td><?php echo htmlspecialchars($cliente['email_cliente']); ?></td>
                                <td><?php echo htmlspecialchars($cliente['telefono'] ?? 'N/A'); ?></td>
                                <td class="text-center">
                                    <a href="editar_cliente.php?id=<?php echo $cliente['id_cliente']; ?>" class="btn btn-primary btn-sm" title="Editar">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="eliminar_cliente.php?id=<?php echo $cliente['id_cliente']; ?>" class="btn btn-danger btn-sm" title="Eliminar" onclick="return confirm('¿Estás seguro de que quieres eliminar a este cliente?');">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted p-4">No hay clientes registrados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php 
$conexion->close();
include('../includes/footer.php'); 
?>