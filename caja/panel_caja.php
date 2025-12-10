<?php
// 1. Incluimos el config (para la $base_url y la sesión) y la conexión
include('../php/config.php');
include('../php/conexion.php');

// 2. Protección de la página
if (!isset($_SESSION['id_cajero']) || $_SESSION['id_cargo'] != 2) {
    header("Location: " . $base_url . "login_universal.php");
    exit;
}

// 3. Obtener comandas con su estado de pago
$sql = "SELECT c.id_comanda, c.numero_mesa, c.total, c.fecha,
        IF(p.id_pago IS NULL, 'Pendiente', 'Pagado') AS estado_pago,
        t.nombre_trabajador AS mesero,
        p.metodo_pago
        FROM comandas c
        LEFT JOIN pagos p ON c.id_comanda = p.id_comanda
        LEFT JOIN trabajadores t ON c.id_trabajador = t.id_trabajador
        ORDER BY c.fecha DESC";
$resultado = $conexion->query($sql);

// 4. Incluimos el header (que abre el <body> y <main>)
include('../includes/header.php');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="display-5"><i class="bi bi-cash-register"></i> Panel de Caja</h1>
    <a href="<?php echo $base_url; ?>logout_universal.php" class="btn btn-danger"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <h5 class="card-title mb-3">Comandas por Cobrar</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID Comanda</th>
                        <th>Fecha</th>
                        <th>Mesero</th>
                        <th>Mesa</th>
                        <th class="text-end">Total</th>
                        <th>Estado</th>
                        <th class="text-center">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($resultado && $resultado->num_rows > 0): ?>
                        <?php while ($fila = $resultado->fetch_assoc()): ?>
                            <tr>
                                <td class="fw-bold">#<?php echo $fila['id_comanda']; ?></td>
                                <td><?php echo date("d/m/Y H:i", strtotime($fila['fecha'])); ?></td>
                                <td><?php echo htmlspecialchars($fila['mesero'] ?? 'Pedido Online'); ?></td>
                                <td><?php echo $fila['numero_mesa'] == 0 ? 'Online' : $fila['numero_mesa']; ?></td>
                                <td class="text-end">$<?php echo number_format($fila['total'], 0, ',', '.'); ?></td>
                                <td>
                                    <?php if ($fila['estado_pago'] == 'Pendiente'): ?>
                                        <span class="badge bg-warning text-dark">Pendiente</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Pagado</span>
                                    <?php endif; ?>
                                </td>
                                
                                <td class="text-center">
                                    <?php if ($fila['estado_pago'] == 'Pendiente'): ?>
                                        
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-success btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalPago"
                                                    data-id="<?php echo $fila['id_comanda']; ?>"
                                                    data-total="<?php echo $fila['total']; ?>"
                                                    data-mesa="<?php echo $fila['numero_mesa']; ?>"
                                                    title="Registrar Pago">
                                                <i class="bi bi-cash-coin"></i>
                                            </button>
                                            
                                            <a href="imprimir_factura.php?id=<?php echo $fila['id_comanda']; ?>" class="btn btn-secondary btn-sm" target="_blank" title="Imprimir Pre-cuenta">
                                                <i class="bi bi-printer"></i>
                                            </a>

                                            <a href="dividir_cuenta.php?id=<?php echo $fila['id_comanda']; ?>" class="btn btn-warning btn-sm" title="Dividir Cuenta">
                                                <i class="bi bi-scissors"></i>
                                            </a>
                                        </div>
                                        
                                    <?php else: // O sea, 'Pagado' ?>
                                    
                                        <a href="imprimir_factura.php?id=<?php echo $fila['id_comanda']; ?>" class="btn btn-primary btn-sm" target="_blank" title="Imprimir Factura">
                                            <i class="bi bi-printer"></i> Factura
                                        </a>
                                        
                                    <?php endif; ?>
                                </td>
                                </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted p-4">No hay comandas registradas.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalPago" tabindex="-1" aria-labelledby="modalPagoLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="modalPagoLabel">Registrar Pago Comanda</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="registrar_pago.php" method="POST">
        <div class="modal-body">
            <p>Comanda para la Mesa: <strong id="mesa_numero" class="fs-5"></strong></p>
            <p class="fs-4">Total a Pagar: <strong class="text-success">$<span id="total_mostrar"></span></strong></p>
            
            <input type="hidden" name="id_comanda" id="id_comanda">
            <input type="hidden" name="total" id="total">
            
            <div class="mb-3">
                <label for="metodo_pago" class="form-label fw-bold">Método de Pago</label>
                <select name="metodo_pago" id="metodo_pago" class="form-select">
                    <option value="Efectivo">Efectivo</option>
                    <option value="Tarjeta">Tarjeta</option>
                    <option value="Nequi/Daviplata">Nequi/Daviplata</option>
                </select>
            </div>

            <div id="efectivo_fields" style="display: block;">
                <div class="mb-3">
                    <label for="monto_recibido" class="form-label fw-bold">Monto Recibido</label>
                    <input type="number" name="monto_recibido" id="monto_recibido" class="form-control" placeholder="0" min="0">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Cambio:</label>
                    <input type="text" id="vuelto" class="form-control" value="$0" readonly>
                </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success w-100 py-2">💾 Registrar Pago</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
// Cargar datos en el modal
const modalPago = document.getElementById('modalPago')
modalPago.addEventListener('show.bs.modal', event => {
    const button = event.relatedTarget
    const id = button.getAttribute('data-id')
    const total = button.getAttribute('data-total')
    const mesa = button.getAttribute('data-mesa')

    document.getElementById('id_comanda').value = id
    document.getElementById('total').value = total
    document.getElementById('mesa_numero').textContent = (mesa == 0 ? 'Online' : mesa)
    document.getElementById('total_mostrar').textContent = new Intl.NumberFormat().format(total)
    
    // Reiniciar campos de pago
    document.getElementById('metodo_pago').value = 'Efectivo';
    document.getElementById('monto_recibido').value = '';
    document.getElementById('vuelto').value = '$0';
    document.getElementById('efectivo_fields').style.display = 'block';
})

// Mostrar campos si es efectivo
document.getElementById('metodo_pago').addEventListener('change', function() {
    const efectivoFields = document.getElementById('efectivo_fields')
    efectivoFields.style.display = this.value === 'Efectivo' ? 'block' : 'none'
})

// Calcular cambio
document.getElementById('monto_recibido').addEventListener('input', function() {
    const total = parseFloat(document.getElementById('total').value) || 0;
    const recibido = parseFloat(this.value) || 0;
    const cambio = recibido - total;
    document.getElementById('vuelto').value = new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP' }).format(Math.max(0, cambio));
})
</script>

<?php
// 5. Incluimos el footer (cierra todo)
include('../includes/footer.php'); 
?>