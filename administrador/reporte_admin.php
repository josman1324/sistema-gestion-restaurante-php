<?php
include('../php/config.php');
include('../php/conexion.php');

if (!isset($_SESSION['admin']) || $_SESSION['id_cargo'] != 1) {
    header("Location: " . $base_url . "login_universal.php");
    exit;
}

$hoy = date('Y-m-d');

// ¡¡AQUÍ LA CORRECCIÓN!!
$sql_total_hoy = "SELECT SUM(total_pagado) AS total_hoy FROM pagos 
                  WHERE DATE(CONVERT_TZ(fecha_pago, '+00:00', '-05:00')) = ?";
$stmt_hoy = $conexion->prepare($sql_total_hoy);
$stmt_hoy->bind_param("s", $hoy);
$stmt_hoy->execute();
$total_hoy = $stmt_hoy->get_result()->fetch_assoc()['total_hoy'] ?? 0;

include('../includes/header.php');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="display-5"><i class="bi bi-bar-chart-fill"></i> Reportes de Ventas</h1>
    <a href="opciones_admin.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
</div>

<div class="row g-4">
    <div class="col-md-12">
        <div class="card shadow-sm border-0 text-center text-success p-4">
            <div class="card-body">
                <i class="bi bi-cash-coin fs-1 mb-3"></i>
                <h5 class="card-title">Ventas de Hoy (<?php echo $hoy; ?>)</h5>
                <p class="display-4 fw-bold">$<?php echo number_format($total_hoy, 0, ',', '.'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-4">
                <h5 class="card-title text-center mb-3"><i class="bi bi-calendar-range"></i> Reporte de Ventas por Rango</h5>
                <form action="generar_reporte_rango.php" method="GET" target="_blank">
                    <div class="mb-3">
                        <label for="fecha_inicio" class="form-label fw-bold">Fecha Inicio:</label>
                        <input type="date" name="fecha_inicio" class="form-control" required value="<?php echo date('Y-m-01'); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="fecha_fin" class="form-label fw-bold">Fecha Fin:</label>
                        <input type="date" name="fecha_fin" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-danger"><i class="bi bi-file-earmark-pdf-fill"></i> Generar PDF</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-4">
                <h5 class="card-title text-center mb-3"><i class="bi bi-download"></i> Reportes Rápidos</h5>
                <div class="d-grid gap-3">
                    <a href="generar_reporte_top_menus.php" target="_blank" class="btn btn-primary">
                        <i class="bi bi-star-fill"></i> PDF Top Menús Vendidos (Histórico)
                    </a>
                    <a href="generar_reporte_semanal.php" target="_blank" class="btn btn-primary">
                        <i class="bi bi-calendar-week"></i> PDF Resumen Últimos 7 Días
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
$stmt_hoy->close();
$conexion->close();
include('../includes/footer.php'); 
?>