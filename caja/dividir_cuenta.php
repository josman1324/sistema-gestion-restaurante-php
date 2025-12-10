<?php
// 1. Incluimos config y conexión
include('../php/config.php');
include('../php/conexion.php');

// 2. Protección de la página
if (!isset($_SESSION['id_cajero']) || $_SESSION['id_cargo'] != 2) {
    header("Location: " . $base_url . "login_universal.php");
    exit;
}

// 3. Obtener el ID de la comanda de la URL
$id_comanda_original = $_GET['id'] ?? 0;
if ($id_comanda_original <= 0) {
    die("Error: ID de comanda no válido.");
}

// 4. Obtener datos de la comanda
$sql_comanda = "SELECT * FROM comandas WHERE id_comanda = ?";
$stmt_comanda = $conexion->prepare($sql_comanda);
$stmt_comanda->bind_param("i", $id_comanda_original);
$stmt_comanda->execute();
$comanda = $stmt_comanda->get_result()->fetch_assoc();

if (!$comanda) {
    die("Comanda no encontrada.");
}

// 5. Obtener los items (detalles) de la comanda
// ¡¡AQUÍ ESTÁ LA CORRECCIÓN!! (Línea 34)
$sql_items = "SELECT cd.id_detalle, cd.cantidad, m.nombre_menu, cd.subtotal
              FROM comanda_detalle cd
              JOIN menus m ON cd.id_menu = m.id_menu
              WHERE cd.id_comanda = ?";
$stmt_items = $conexion->prepare($sql_items);
$stmt_items->bind_param("i", $id_comanda_original);
$stmt_items->execute();
$items = $stmt_items->get_result();

include('../includes/header.php');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="display-5"><i class="bi bi-scissors"></i> Dividir Comanda #<?php echo $id_comanda_original; ?></h1>
    <a href="panel_caja.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
</div>

<div class="alert alert-info">
    <i class="bi bi-info-circle-fill"></i> Mueve los ítems que deseas enviar a una **nueva cuenta** (Comanda B). Los ítems que dejes aquí permanecerán en la cuenta original (Comanda A).
</div>

<form action="procesar_division.php" method="POST">
    <input type="hidden" name="id_comanda_original" value="<?php echo $id_comanda_original; ?>">
    <input type="hidden" name="id_mesero" value="<?php echo $comanda['id_trabajador']; ?>">
    <input type="hidden" name="numero_mesa" value="<?php echo $comanda['numero_mesa']; ?>">

    <div class="row g-4">
        
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Cuenta Original (Comanda A)</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group" id="cuenta-original">
                        <?php while ($item = $items->fetch_assoc()): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center" 
                                data-id-detalle="<?php echo $item['id_detalle']; ?>"
                                data-subtotal="<?php echo $item['subtotal']; ?>">
                                
                                <span>
                                    <span class="badge bg-dark me-2"><?php echo $item['cantidad']; ?>x</span>
                                    <?php echo htmlspecialchars($item['nombre_menu']); ?>
                                </span>
                                
                                <span class="fw-bold me-3">$<?php echo number_format($item['subtotal'], 0, ',', '.'); ?></span>
                                
                                <button type="button" class="btn btn-warning btn-sm btn-mover" onclick="moverItem(this, 'nueva-cuenta')">
                                    Mover <i class="bi bi-arrow-right-short"></i>
                                </button>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
                <div class="card-footer bg-light">
                    <h4 class="text-end mb-0">Total: <span id="total-original" class="text-success fw-bold">$0</span></h4>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Nueva Cuenta (Comanda B)</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group" id="nueva-cuenta">
                        </ul>
                </div>
                <div class="card-footer bg-light">
                    <h4 class="text-end mb-0">Total: <span id="total-nuevo" class="text-success fw-bold">$0</span></h4>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="items_a_mover" id="items_a_mover" value="">

    <div class="text-center mt-4 d-grid">
        <button type="submit" class="btn btn-success btn-lg" id="btn-confirmar-division">
            <i class="bi bi-check-circle-fill"></i> Confirmar División
        </button>
    </div>
</form>

<script>
    const listaOriginal = document.getElementById('cuenta-original');
    const listaNueva = document.getElementById('nueva-cuenta');
    const totalOriginalEl = document.getElementById('total-original');
    const totalNuevoEl = document.getElementById('total-nuevo');
    const inputItemsAMover = document.getElementById('items_a_mover');

    function moverItem(boton, destinoId) {
        const itemLi = boton.closest('li');
        const destinoUl = document.getElementById(destinoId);
        
        if (destinoId === 'nueva-cuenta') {
            boton.innerHTML = '<i class="bi bi-arrow-left-short"></i> Devolver';
            boton.classList.remove('btn-warning');
            boton.classList.add('btn-info');
            boton.setAttribute('onclick', "moverItem(this, 'cuenta-original')");
        } else {
            boton.innerHTML = 'Mover <i class="bi bi-arrow-right-short"></i>';
            boton.classList.remove('btn-info');
            boton.classList.add('btn-warning');
            boton.setAttribute('onclick', "moverItem(this, 'nueva-cuenta')");
        }
        
        destinoUl.appendChild(itemLi);
        recalcularTodo();
    }

    function recalcularTodo() {
        let totalOriginal = 0;
        let totalNuevo = 0;
        let itemsParaMover = [];

        listaOriginal.querySelectorAll('li').forEach(item => {
            totalOriginal += parseFloat(item.dataset.subtotal);
        });

        listaNueva.querySelectorAll('li').forEach(item => {
            totalNuevo += parseFloat(item.dataset.subtotal);
            // El JavaScript usa 'idDetalle' (camelCase) que viene de 'data-id-detalle'
            // Esto ya era correcto, ¡qué bien!
            itemsParaMover.push(item.dataset.idDetalle);
        });

        totalOriginalEl.textContent = '$' + new Intl.NumberFormat().format(totalOriginal);
        totalNuevoEl.textContent = '$' + new Intl.NumberFormat().format(totalNuevo);
        inputItemsAMover.value = itemsParaMover.join(',');
    }

    document.addEventListener('DOMContentLoaded', recalcularTodo);
</script>

<?php 
$conexion->close();
include('../includes/footer.php'); 
?>