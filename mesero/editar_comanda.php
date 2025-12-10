<?php
// 1. Incluimos config y conexión
include('../php/config.php');
include('../php/conexion.php');
if (!isset($_SESSION['id_trabajador']) || $_SESSION['id_cargo'] != 3) {
    header("Location: " . $base_url . "login_universal.php");
    exit;
}
$id_comanda = $_GET['id'] ?? 0;
if ($id_comanda <= 0) { die("ID de comanda no válido."); }
$sql_comanda = "SELECT * FROM comandas WHERE id_comanda = ?";
$stmt_comanda = $conexion->prepare($sql_comanda);
$stmt_comanda->bind_param("i", $id_comanda);
$stmt_comanda->execute();
$comanda = $stmt_comanda->get_result()->fetch_assoc();
if (!$comanda) { die("Comanda no encontrada."); }

// 5. ¡¡AQUÍ ESTÁ LA LÓGICA DE DÍAS CORREGIDA!!
// Usamos date('w') que devuelve 0=Domingo, 1=Lunes, 2=Martes...
// Le sumamos 1 para que coincida con el estándar de la BD (1=Domingo, 2=Lunes, 3=Martes...)
$dia_actual_numero = date('w') + 1; 

// 6. Consultar los MENÚS DISPONIBLES para HOY
$sql_menus = "SELECT m.id_menu, m.nombre_menu, m.precio_menu
              FROM menus m
              JOIN menu_dias md ON m.id_menu = md.id_menu
              WHERE md.id_menu_dia = ?"; // Comparamos con el número del día
$stmt_menus = $conexion->prepare($sql_menus);
$stmt_menus->bind_param("i", $dia_actual_numero);
$stmt_menus->execute();
$menus_disponibles = $stmt_menus->get_result();

// (El resto del PHP es el mismo...)
$sql_items = "SELECT cd.id_detalle, m.nombre_menu, cd.cantidad, cd.subtotal
              FROM comanda_detalle cd
              JOIN menus m ON cd.id_menu = m.id_menu
              WHERE cd.id_comanda = ?";
$stmt_items = $conexion->prepare($sql_items);
$stmt_items->bind_param("i", $id_comanda);
$stmt_items->execute();
$items_comanda = $stmt_items->get_result();
include('../includes/header.php');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="display-5">Gestionar Comanda #<?php echo $id_comanda; ?></h1>
    <a href="panel.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Volver al Panel</a>
</div>

<div class="row g-4">
    <div class="col-md-5">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h5 class="card-title text-success"><i class="bi bi-plus-circle-fill"></i> Añadir Plato</h5>
                <hr>
                <form action="agregar_plato.php" method="POST">
                    <input type="hidden" name="id_comanda" value="<?php echo $id_comanda; ?>">
                    <div class="mb-3">
                        <label for="id_menu" class="form-label fw-bold">Plato del Día (<?php echo date('l'); ?>)</label>
                        <select class="form-select" name="id_menu" id="id_menu" required>
                            <option value="" disabled selected>Selecciona un plato...</option>
                            <?php if ($menus_disponibles->num_rows > 0): ?>
                                <?php while($menu = $menus_disponibles->fetch_assoc()): ?>
                                    <option value="<?php echo $menu['id_menu']; ?>">
                                        <?php echo htmlspecialchars($menu['nombre_menu']); ?> - $<?php echo number_format($menu['precio_menu'], 0, ',', '.'); ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <option value="" disabled>No hay platos asignados para hoy.</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="cantidad" class="form-label fw-bold">Cantidad</label>
                        <input type="number" class="form-control" id="cantidad" name="cantidad" value="1" min="1" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success btn-lg">Añadir a Comanda</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h5 class="card-title mb-3"><i class="bi bi-list-task"></i> Platos en la Comanda</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Plato</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-end">Subtotal</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($items_comanda->num_rows > 0): ?>
                                <?php while ($item = $items_comanda->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['nombre_menu']); ?></td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <a href="actualizar_cantidad.php?id_detalle=<?php echo $item['id_detalle']; ?>&id_comanda=<?php echo $id_comanda; ?>&accion=restar" class="btn btn-warning btn-sm">
                                                    <i class="bi bi-dash"></i>
                                                </a>
                                                <span class="btn btn-light btn-sm disabled"><?php echo $item['cantidad']; ?></span>
                                                <a href="actualizar_cantidad.php?id_detalle=<?php echo $item['id_detalle']; ?>&id_comanda=<?php echo $id_comanda; ?>&accion=sumar" class="btn btn-success btn-sm">
                                                    <i class="bi bi-plus"></i>
                                                </a>
                                            </div>
                                        </td>
                                        <td class="text-end">$<?php echo number_format($item['subtotal'], 0, ',', '.'); ?></td>
                                        <td class="text-center">
                                            <a href="actualizar_cantidad.php?id_detalle=<?php echo $item['id_detalle']; ?>&id_comanda=<?php echo $id_comanda; ?>&accion=eliminar" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que quieres eliminar TODOS los <?php echo htmlspecialchars($item['nombre_menu']); ?>?');">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted p-4">Aún no hay platos en esta comanda.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <hr>
                <div class="text-end">
                    <h3 class="mb-0">Total: <span class="text-success fw-bold">$<?php echo number_format($comanda['total'], 0, ',', '.'); ?></span></h3>
                </div>
                <?php if ($comanda['estado'] == 'En espera'): ?>
                <form action="actualizar_estado.php" method="POST" class="mt-3 text-end">
                    <input type="hidden" name="id_comanda" value="<?php echo $id_comanda; ?>">
                    <input type="hidden" name="nuevo_estado" value="Entregado">
                    <button type="submit" class="btn btn-info">Marcar como Entregado</button>
                </form>
                <?php else: ?>
                    <p class="text-end mt-3"><span class="badge bg-success fs-6">Comanda Entregada</span></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php 
$conexion->close();
include('../includes/footer.php'); 
?>