<?php
// Incluimos el header. El header ya inicia la sesión.
include('../includes/header.php'); 
include('../php/conexion.php'); // Necesitamos la BD para ver pedidos antiguos

// Proteger la página. Solo para clientes logueados.
if (!isset($_SESSION['id_cliente'])) {
    // Si no es cliente, lo mandamos al login
    header("Location: ../login_universal.php");
    exit;
}

$id_cliente = $_SESSION['id_cliente'];
$nombre_cliente = $_SESSION['nombre_cliente'];

// Consultar los pedidos ANTERIORES de este cliente
$sql = "SELECT id_comanda, fecha, total, estado 
        FROM comandas 
        WHERE id_cliente = ? 
        ORDER BY fecha DESC";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_cliente);
$stmt->execute();
$pedidos = $stmt->get_result();
?>

<div class="row">
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body text-center p-4">
                <i class="bi bi-person-circle" style="font-size: 4rem; color: #6c757d;"></i>
                <h3 class="mt-3">Hola, <?php echo htmlspecialchars($nombre_cliente); ?></h3>
                <p class="text-muted">Bienvenido a tu panel de control.</p>
                <hr>
                <div class="d-grid gap-2">
                    <a href="../index.php" class="btn btn-success"><i class="bi bi-shop"></i> Ver Menú</a>
                    <a href="carrito.php" class="btn btn-primary"><i class="bi bi-cart"></i> Ver mi Carrito</a>
                    <a href="../logout_universal.php" class="btn btn-outline-danger"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h4 class="mb-3"><i class="bi bi-receipt"></i> Mis Pedidos Anteriores</h4>
                
                <?php if ($pedidos->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>ID Comanda</th>
                                    <th>Fecha</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($pedido = $pedidos->fetch_assoc()): ?>
                                    <tr>
                                        <td>#<?php echo $pedido['id_comanda']; ?></td>
                                        <td><?php echo date("d/m/Y H:i", strtotime($pedido['fecha'])); ?></td>
                                        <td>$<?php echo number_format($pedido['total'], 0, ',', '.'); ?></td>
                                        <td>
                                            <?php if ($pedido['estado'] == 'Pagado'): ?>
                                                <span class="badge bg-success">Pagado</span>
                                            <?php elseif ($pedido['estado'] == 'En espera'): ?>
                                                <span class="badge bg-warning text-dark">En espera</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary"><?php echo htmlspecialchars($pedido['estado']); ?></span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">No has realizado ningún pedido todavía.</p>
                <?php endif; ?>
                
            </div>
        </div>
    </div>
</div>

<?php
$stmt->close();
$conexion->close();
include('../includes/footer.php'); 
?>