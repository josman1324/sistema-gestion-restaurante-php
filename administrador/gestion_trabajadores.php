<?php
include('../php/config.php');
include('../php/conexion.php');

// Protección de Admin
if (!isset($_SESSION['admin']) || $_SESSION['id_cargo'] != 1) {
    header("Location: " . $base_url . "login_universal.php");
    exit;
}

// Consultar todos los trabajadores y unir con la tabla de cargos
$sql = "SELECT t.id_trabajador, t.nombre_trabajador, t.usuario, c.nombre_cargo 
        FROM trabajadores t
        JOIN cargos c ON t.id_cargo = c.id_cargo
        ORDER BY t.nombre_trabajador ASC";
$result = $conexion->query($sql);

include('../includes/header.php');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="display-5"><i class="bi bi-person-badge-fill"></i> Gestionar Trabajadores</h1>
    <div>
        <a href="crear_trabajador.php" class="btn btn-success"><i class="bi bi-plus-circle"></i> Crear Nuevo Trabajador</a>
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
                        <th>Usuario (Login)</th>
                        <th>Cargo</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($trabajador = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="fw-bold"><?php echo $trabajador['id_trabajador']; ?></td>
                                <td><?php echo htmlspecialchars($trabajador['nombre_trabajador']); ?></td>
                                <td><?php echo htmlspecialchars($trabajador['usuario']); ?></td>
                                <td>
                                    <?php 
                                    $cargo = htmlspecialchars($trabajador['nombre_cargo']);
                                    $badge = 'bg-secondary';
                                    if ($cargo == 'Administrador') $badge = 'bg-danger';
                                    if ($cargo == 'Caja') $badge = 'bg-success';
                                    if ($cargo == 'Mesero') $badge = 'bg-info text-dark';
                                    echo "<span class='badge $badge'>$cargo</span>";
                                    ?>
                                </td>
                                <td class="text-center">
                                    <a href="editar_trabajador.php?id=<?php echo $trabajador['id_trabajador']; ?>" class="btn btn-primary btn-sm" title="Editar">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="eliminar_trabajador.php?id=<?php echo $trabajador['id_trabajador']; ?>" class="btn btn-danger btn-sm" title="Eliminar" onclick="return confirm('¿Estás seguro de que quieres eliminar a este trabajador?');">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted p-4">No hay trabajadores registrados.</td>
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