<?php
include('../php/config.php');
include('../php/conexion.php');

// Protección de Admin
if (!isset($_SESSION['admin']) || $_SESSION['id_cargo'] != 1) {
    header("Location: " . $base_url . "login_universal.php");
    exit;
}

// Consultar todos los menús
$sql = "SELECT id_menu, nombre_menu, precio_menu, descripcion_menu FROM menus ORDER BY nombre_menu ASC";
$result = $conexion->query($sql);

include('../includes/header.php');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="display-5"><i class="bi bi-pencil-fill"></i> Gestionar Menús</h1>
    <div>
        <a href="crear_menu.php" class="btn btn-success"><i class="bi bi-plus-circle"></i> Crear Nuevo Menú</a>
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
                        <th>Nombre del Menú</th>
                        <th>Descripción</th>
                        <th class="text-end">Precio</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($menu = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="fw-bold"><?php echo $menu['id_menu']; ?></td>
                                <td><?php echo htmlspecialchars($menu['nombre_menu']); ?></td>
                                <td><?php echo htmlspecialchars(substr($menu['descripcion_menu'], 0, 50)); ?>...</td>
                                <td class="text-end">$<?php echo number_format($menu['precio_menu'], 0, ',', '.'); ?></td>
                                <td class="text-center">
                                    <a href="editar_menu.php?id=<?php echo $menu['id_menu']; ?>" class="btn btn-primary btn-sm" title="Editar">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="eliminar_menu.php?id=<?php echo $menu['id_menu']; ?>" class="btn btn-danger btn-sm" title="Eliminar" onclick="return confirm('¿Estás seguro de que quieres eliminar este menú?');">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted p-4">No hay menús registrados.</td>
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