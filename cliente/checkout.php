<?php
// 1. Incluir config (Inicia sesión, carga $base_url)
include('../php/config.php');

// 2. ¡LÓGICA PRIMERO!
// Proteger la página. Si no es cliente, se va al login.
// (Esta es la línea 7 que daba el error)
if (!isset($_SESSION['id_cliente'])) {
    // Guardamos a dónde querían ir
    $_SESSION['redirect_url'] = $base_url . 'cliente/checkout.php';
    header("Location: " . $base_url . "login_universal.php");
    exit;
}

// Si el carrito está vacío, no pueden pagar.
if (empty($_SESSION['carrito'])) {
    header("Location: " . $base_url . "index.php");
    exit;
}

// 3. ¡PRESENTACIÓN DESPUÉS!
// Si llegamos aquí, el usuario es un cliente y tiene items.
// AHORA SÍ incluimos el HTML (que causaba el "output started").
include('../includes/header.php');

// 4. Lógica para calcular el total (la necesitamos para la vista)
$total_carrito = 0;
?>

<h1 class="display-5"><i class="bi bi-credit-card"></i> Finalizar Compra (Checkout)</h1>
<hr>

<div class="row g-5">
    <div class="col-md-6">
        <h4 class="mb-3">Resumen de tu Pedido</h4>
        <ul class="list-group mb-3">
            <?php 
            // Necesitamos la conexión solo para leer los precios
            include('../php/conexion.php'); 
            
            foreach ($_SESSION['carrito'] as $id_menu => $item) {
                $stmt = $conexion->prepare("SELECT nombre_menu, precio_menu FROM menus WHERE id_menu = ?");
                $stmt->bind_param("i", $id_menu);
                $stmt->execute();
                $menu = $stmt->get_result()->fetch_assoc();
                
                if ($menu) {
                    $nombre = $menu['nombre_menu'];
                    $precio = $menu['precio_menu'];
                    $cantidad = $item['cantidad'];
                    $subtotal = $precio * $cantidad;
                    $total_carrito += $subtotal;
                    ?>
                    <li class="list-group-item d-flex justify-content-between lh-sm">
                        <div>
                            <h6 class="my-0"><?php echo htmlspecialchars($nombre); ?></h6>
                            <small class="text-muted">Cantidad: <?php echo $cantidad; ?></small>
                        </div>
                        <span class="text-muted">$<?php echo number_format($subtotal, 0, ',', '.'); ?></span>
                    </li>
                <?php 
                }
                $stmt->close();
            } 
            $conexion->close();
            ?>
            
            <li class="list-group-item d-flex justify-content-between bg-light">
                <span class="fw-bold">Total (COP)</span>
                <strong class="fw-bold">$<?php echo number_format($total_carrito, 0, ',', '.'); ?></strong>
            </li>
        </ul>
    </div>

    <div class="col-md-6">
        <h4 class="mb-3">Información de Pago</h4>
        <div class="alert alert-info">
            <i class="bi bi-info-circle-fill"></i> Para este proyecto, simularemos el pago. Simplemente confirma tu pedido.
        </div>
        
        <form action="guardar_pedido.php" method="POST">
            <input type="hidden" name="total_pagado" value="<?php echo $total_carrito; ?>">
            <input type="hidden" name="id_cliente" value="<?php echo $_SESSION['id_cliente']; ?>">
            <input type="hidden" name="metodo_pago" value="Simulado (Online)">
            
            <h5 class="mb-3">Datos del Cliente</h5>
            <ul class="list-group mb-3">
                <li class="list-group-item">
                    <strong>Nombre:</strong> <?php echo htmlspecialchars($_SESSION['nombre_cliente']); ?>
                </li>
            </ul>

            <hr class="my-4">
            
            <div class="d-grid">
                <button class="btn btn-success btn-lg" type="submit">
                    <i class="bi bi-check-circle-fill"></i> Confirmar y Realizar Pedido
                </button>
            </div>
        </form>
    </div>
</div>

<?php
// 5. Incluir el footer
include('../includes/footer.php');
?>