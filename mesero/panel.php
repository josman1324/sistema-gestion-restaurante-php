<?php
// 1. Incluimos config y conexión
include('../php/config.php');
include('../php/conexion.php');

// 2. Protección de la página (Mesero)
if (!isset($_SESSION['id_trabajador']) || $_SESSION['id_cargo'] != 3) {
    header("Location: " . $base_url . "login_universal.php");
    exit;
}

// 3. ¡¡AQUÍ ESTÁ LA CORRECCIÓN!!
// Esta consulta ahora REVISA la tabla 'pagos'.
// Si una comanda tiene un 'id_pago', NO aparecerá en esta lista.
$sql = "SELECT c.id_comanda, c.numero_mesa, c.estado, c.total, c.fecha
        FROM comandas c
        LEFT JOIN pagos p ON c.id_comanda = p.id_comanda
        WHERE c.estado IN ('En espera', 'Entregado')
        AND p.id_pago IS NULL 
        ORDER BY c.fecha DESC";
$resultado = $conexion->query($sql);

// 4. Incluimos el header
include('../includes/header.php');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="display-5"><i class="bi bi-person-workspace"></i> Panel de Mesero</h1>
    <a href="<?php echo $base_url; ?>logout_universal.php" class="btn btn-danger"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-4">
                <h5 class="card-title text-success"><i class="bi bi-plus-circle-fill"></i> Crear Nueva Comanda</h5>
                <hr>
                <form action="crear_comanda.php" method="POST">
                    <div class="mb-3">
                        <label for="numero_mesa" class="form-label fw-bold">Número de Mesa</label>
                        <input type="number" class="form-control" id="numero_mesa" name="numero_mesa" required min="1">
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success btn-lg">Crear</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h5 class="card-title mb-3"><i class="bi bi-list-task"></i> Comandas Activas</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Mesa</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th class="text-end">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($resultado && $resultado->num_rows > 0): ?>
                                <?php while ($fila = $resultado->fetch_assoc()): ?>
                                    <tr>
                                        <td class="fw-bold">#<?php echo $fila['id_comanda']; ?></td>
                                        <td><?php echo $fila['numero_mesa']; ?></td>
                                        <td>$<?php echo number_format($fila['total'], 0, ',', '.'); ?></td>
                                        <td>
                                            <?php if ($fila['estado'] == 'En espera'): ?>
                                                <span class="badge bg-warning text-dark">En espera</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Entregado</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <a href="editar_comanda.php?id=<?php echo $fila['id_comanda']; ?>" class="btn btn-primary btn-sm">
                                                <i class="bi bi-pencil-fill"></i> Gestionar
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted p-4">No hay comandas activas sin pagar.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
$conexion->close();
include('../includes/footer.php'); 
?>