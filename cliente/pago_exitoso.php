<?php
include('../includes/header.php'); 

$id_comanda = $_GET['id_comanda'] ?? 0;
?>

<div class="row justify-content-center">
    <div class="col-md-7 text-center">
        <div class="card shadow-sm border-0">
            <div class="card-body p-5">
                <i class="bi bi-check-circle-fill text-success" style="font-size: 6rem;"></i>
                <h1 class="display-5 mt-3">¡Pedido Recibido!</h1>
                <p class="lead text-muted">Tu pago fue exitoso y tu pedido ya está en preparación.</p>
                
                <?php if ($id_comanda > 0): ?>
                    <h4 class="mt-4">Tu número de comanda es: <span class="badge bg-dark">#<?php echo intval($id_comanda); ?></span></h4>
                <?php endif; ?>

                <div class="d-flex justify-content-center gap-3 mt-5">
                    <a href="../index.php" class="btn btn-outline-success btn-lg">
                        <i class="bi bi-shop"></i> Seguir Comprando
                    </a>
                    <a href="panel.php" class="btn btn-success btn-lg">
                        <i class="bi bi-receipt"></i> Ver Mis Pedidos
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>