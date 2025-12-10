<?php
include('../php/config.php');
include('../php/conexion.php');

if (!isset($_SESSION['admin']) || $_SESSION['id_cargo'] != 1) {
    header("Location: " . $base_url . "login_universal.php");
    exit;
}

$filtro_fecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');

// ¡¡AQUÍ LA CORRECCIÓN!! (Para la tabla)
$sql = "SELECT 
            c.id_comanda, c.fecha, c.numero_mesa, c.total, c.estado AS estado_cocina,
            t.nombre_trabajador AS mesero, cl.nombre_cliente,
            IF(p.id_pago IS NULL, 'Pendiente', 'Pagado') AS estado_pago,
            CONVERT_TZ(c.fecha, '+00:00', '-05:00') AS fecha_comanda_colombia
        FROM comandas c 
        LEFT JOIN trabajadores t ON c.id_trabajador = t.id_trabajador 
        LEFT JOIN clientes cl ON c.id_cliente = cl.id_cliente
        LEFT JOIN pagos p ON c.id_comanda = p.id_comanda
        WHERE DATE(CONVERT_TZ(c.fecha, '+00:00', '-05:00')) = ?
        ORDER BY c.fecha DESC";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $filtro_fecha);
$stmt->execute();
$result = $stmt->get_result();

// ¡¡Y AQUÍ!! (Para el total)
$sql_total = "SELECT SUM(total_pagado) AS total_dia FROM pagos 
              WHERE DATE(CONVERT_TZ(fecha_pago, '+00:00', '-05:00')) = ?";
$stmt_total = $conexion->prepare($sql_total);
$stmt_total->bind_param("s", $filtro_fecha);
$stmt_total->execute();
$total_dia = $stmt_total->get_result()->fetch_assoc()['total_dia'] ?? 0;

include('../includes/header.php');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="display-5"><i class="bi bi-eye-fill"></i> Comandas del Día</h1>
    <a href="opciones_admin.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form method="GET" action="panel_admin.php" class="d-flex align-items-end">
            <div class="flex-grow-1 me-2">
                <label for="fecha" class="form-label fw-bold">Filtrar por fecha:</label>
                <input type="date" name="fecha" id="fecha" class="form-control" value="<?php echo htmlspecialchars($filtro_fecha); ?>">
            </div>
            <button type="submit" class="btn btn-success me-2" title="Filtrar"><i class="bi bi-search"></i></button>
            <a href="generar_reporte_pdf.php?fecha=<?php echo htmlspecialchars($filtro_fecha); ?>" target="_blank" class="btn btn-danger" title="Descargar PDF del Día"><i class="bi bi-file-earmark-pdf-fill"></i></a>
        </form>
    </div>
</div>

<div class="alert alert-success fs-4 text-center">
    💰 Total Pagado el <?php echo htmlspecialchars($filtro_fecha); ?>: 
    <strong>$<?php echo number_format($total_dia, 0, ',', '.'); ?></strong>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th> <th>Hora (Local)</th> <th>Mesa</th> <th>Cliente/Mesero</th>
                        <th>Estado Cocina</th> <th>Estado Pago</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($fila = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="fw-bold">#<?php echo $fila['id_comanda']; ?></td>
                                <td><?php echo date("H:i A", strtotime($fila['fecha_comanda_colombia'])); ?></td>
                                <td><?php echo $fila['numero_mesa'] == 0 ? 'Online' : $fila['numero_mesa']; ?></td>
                                <td><?php echo htmlspecialchars($fila['nombre_cliente'] ?? $fila['mesero'] ?? 'N/A'); ?></td>
                                <td>
                                    <?php if ($fila['estado_cocina'] == 'En espera'): ?>
                                        <span class="badge bg-warning text-dark">En espera</span>
                                    <?php else: ?>
                                        <span class="badge bg-success"><?php echo htmlspecialchars($fila['estado_cocina']); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($fila['estado_pago'] == 'Pendiente'): ?>
                                        <span class="badge bg-warning text-dark">Pendiente</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Pagado</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">$<?php echo number_format($fila['total'], 0, ',', '.'); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted p-4">No se encontraron comandas para esta fecha.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php 
$stmt->close();
$stmt_total->close();
$conexion->close();
include('../includes/footer.php'); 
?>