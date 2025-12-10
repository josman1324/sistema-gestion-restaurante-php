<?php
// 1. Incluimos config (para la $base_url y la sesión)
include('../php/config.php');

// 2. Protección de la página
if (!isset($_SESSION['admin']) || $_SESSION['id_cargo'] != 1) {
    header("Location: " . $base_url . "login_universal.php");
    exit;
}

// 3. Incluimos el header
include('../includes/header.php');
?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm border-0 text-center">
            <div class="card-body p-5">
                <img src="<?php echo $base_url; ?>img/arbol.png" alt="Logo Restaurante" style="width: 90px; margin-bottom: 20px;">
                <h2 class="display-6">Panel de Admin</h2>
                <p class="lead text-muted">Bienvenido, <?php echo htmlspecialchars($_SESSION['admin']); ?>.</p>
                <hr>
                <div class="d-grid gap-3 mt-4">
                    <a href="panel_admin.php" class="btn btn-success btn-lg">
                        <i class="bi bi-eye-fill"></i> Ver Comandas del Día
                    </a>
                    <a href="reporte_admin.php" class="btn btn-primary btn-lg">
                        <i class="bi bi-bar-chart-fill"></i> Ver Reportes de Ventas
                    </a>
                    <a href="gestion_menus.php" class="btn btn-warning btn-lg">
                        <i class="bi bi-pencil-fill"></i> Gestionar Menús
                    </a>
                    <a href="gestion_trabajadores.php" class="btn btn-info btn-lg">
                        <i class="bi bi-person-badge-fill"></i> Gestionar Trabajadores
                    </a>
                    
                    <a href="gestion_clientes.php" class="btn btn-secondary btn-lg">
                        <i class="bi bi-people-fill"></i> Gestionar Clientes
                    </a>
                    
                    <a href="<?php echo $base_url; ?>logout_universal.php" class="btn btn-outline-danger mt-3">
                        <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>