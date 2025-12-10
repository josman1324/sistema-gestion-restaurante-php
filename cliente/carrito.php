<?php
// Incluimos el header. El header ya inicia la sesión.
include('../includes/header.php'); 
// No necesitamos la conexión a BD aquí, toda la info está en la sesión.

$carrito = $_SESSION['carrito'] ?? array();
$total_carrito = 0;
?>

<div classtext-center mb-4>
    <h1 class="display-5"><i class="bi bi-cart-check-fill"></i> Mi Carrito de Compras</h1>
    <p class="lead text-muted">Revisa tu pedido y prepárate para pagar.</p>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <?php if (empty($carrito)): ?>
            <div class="text-center p-5">
                <i class="bi bi-cart-x" style="font-size: 5rem; color: #aaa;"></i>
                <h3 class="mt-3">Tu carrito está vacío</h3>
                <p class="text-muted">Aún no has añadido ningún plato.</p>
                <a href="../index.php" class="btn btn-success btn-lg mt-3">
                    <i class="bi bi-arrow-left"></i> Volver al Menú
                </a>
            </div>

        <?php else: ?>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Producto</th>
                            <th scope="col" class="text-center">Cantidad</th>
                            <th scope="col" class="text-end">Precio Unitario</th>
                            <th scope="col" class="text-end">Subtotal</th>
                            <th scope="col" class="text-center">Quitar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($carrito as $item): ?>
                            <?php 
                                $subtotal = $item['precio'] * $item['cantidad'];
                                $total_carrito += $subtotal;
                            ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($item['nombre']); ?></strong>
                                </td>
                                <td class="text-center" style="width: 150px;">
                                    <form action="../carrito_acciones.php" method="GET" class="d-flex">
                                        <input type="hidden" name="accion" value="actualizar">
                                        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                        <input type="number" name="cantidad" class="form-control form-control-sm" value="<?php echo $item['cantidad']; ?>" min="1" max="10">
                                        <button type="submit" class="btn btn-light btn-sm ms-1" title="Actualizar"><i class="bi bi-arrow-repeat"></i></button>
                                    </form>
                                </td>
                                <td class="text-end">$<?php echo number_format($item['precio'], 0, ',', '.'); ?></td>
                                <td class="text-end fw-bold">$<?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                                <td class="text-center">
                                    <a href="../carrito_acciones.php?accion=eliminar&id=<?php echo $item['id']; ?>" class="btn btn-danger btn-sm" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <td colspan="3" class="text-end fs-5 fw-bold">TOTAL:</td>
                            <td colspan="2" class="text-start fs-5 fw-bold text-success">$<?php echo number_format($total_carrito, 0, ',', '.'); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="../carrito_acciones.php?accion=vaciar" class="btn btn-outline-danger">
                    <i class="bi bi-trash2"></i> Vaciar Carrito
                </a>
                
                <a href="checkout.php" class="btn btn-success btn-lg">
                    Continuar al Pago <i class="bi bi-arrow-right"></i>
                </a>
            </div>

        <?php endif; ?>

    </div>
</div>

<?php include('../includes/footer.php'); ?>