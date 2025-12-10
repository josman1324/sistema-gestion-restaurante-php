<?php
// 1. Incluimos config y conexión
include('../php/config.php');
include('../php/conexion.php');

// 2. Protección de la página
if (!isset($_SESSION['id_cajero']) || $_SESSION['id_cargo'] != 2) {
    die("Acceso denegado.");
}

// 3. Recibir los datos del formulario
$id_comanda_original = $_POST['id_comanda_original'] ?? 0;
$numero_mesa = $_POST['numero_mesa'] ?? 0;
$id_mesero = !empty($_POST['id_mesero']) ? $_POST['id_mesero'] : null;
$items_a_mover_str = $_POST['items_a_mover'] ?? '';

$items_a_mover = !empty($items_a_mover_str) ? explode(',', $items_a_mover_str) : [];

// 4. Validar
if ($id_comanda_original <= 0 || empty($items_a_mover)) {
    die("Error: No se seleccionaron ítems para mover o la comanda es inválida. <a href='panel_caja.php'>Volver</a>");
}

// 5. ¡¡INICIAMOS TRANSACCIÓN!!
$conexion->autocommit(FALSE);

try {
    // 6. PASO 1: Crear la nueva comanda (Comanda B)
    $sql_nueva_comanda = "INSERT INTO comandas (id_trabajador, numero_mesa, total, estado) 
                          VALUES (?, ?, 0, 'En espera')";
    $stmt_nueva = $conexion->prepare($sql_nueva_comanda);
    $stmt_nueva->bind_param("ii", $id_mesero, $numero_mesa);
    $stmt_nueva->execute();
    
    $id_comanda_nueva = $conexion->insert_id;
    if ($id_comanda_nueva <= 0) {
        throw new Exception("No se pudo crear la nueva comanda.");
    }

    // 7. PASO 2: Mover los ítems
    // ¡¡AQUÍ ESTÁ LA CORRECCIÓN!!
    $sql_mover = "UPDATE comanda_detalle SET id_comanda = ? WHERE id_detalle = ?";
    $stmt_mover = $conexion->prepare($sql_mover);

    foreach ($items_a_mover as $id_detalle) {
        $stmt_mover->bind_param("ii", $id_comanda_nueva, $id_detalle);
        $stmt_mover->execute();
    }

    // 8. PASO 3: Recalcular y actualizar TOTALES
    
    // Total Comanda A (Original)
    $sql_sum_A = "SELECT SUM(subtotal) AS nuevo_total FROM comanda_detalle WHERE id_comanda = ?";
    $stmt_sum_A = $conexion->prepare($sql_sum_A);
    $stmt_sum_A->bind_param("i", $id_comanda_original);
    $stmt_sum_A->execute();
    $total_A = $stmt_sum_A->get_result()->fetch_assoc()['nuevo_total'] ?? 0;
    
    $sql_update_A = "UPDATE comandas SET total = ? WHERE id_comanda = ?";
    $stmt_update_A = $conexion->prepare($sql_update_A);
    $stmt_update_A->bind_param("di", $total_A, $id_comanda_original);
    $stmt_update_A->execute();

    // Total Comanda B (Nueva)
    $sql_sum_B = "SELECT SUM(subtotal) AS nuevo_total FROM comanda_detalle WHERE id_comanda = ?";
    $stmt_sum_B = $conexion->prepare($sql_sum_B);
    $stmt_sum_B->bind_param("i", $id_comanda_nueva);
    $stmt_sum_B->execute();
    $total_B = $stmt_sum_B->get_result()->fetch_assoc()['nuevo_total'] ?? 0;

    $sql_update_B = "UPDATE comandas SET total = ? WHERE id_comanda = ?";
    $stmt_update_B = $conexion->prepare($sql_update_B);
    $stmt_update_B->bind_param("di", $total_B, $id_comanda_nueva);
    $stmt_update_B->execute();

    // 9. PASO 4: ¡Confirmar todo!
    $conexion->commit();

    // 10. Redirigir al panel de caja
    header("Location: " . $base_url . "caja/panel_caja.php");
    exit;

} catch (Exception $e) {
    // 11. Si algo falló, revertir todo
    $conexion->rollback();
    die("Error fatal al dividir la cuenta: " . $e->getMessage() . ". <br>No se realizó ningún cambio. <a href='panel_caja.php'>Volver</a>");
}

$conexion->close();
?>