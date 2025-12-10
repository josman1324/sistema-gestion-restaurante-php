<?php
// 1. Incluimos config y conexión
include('../php/config.php');
include('../php/conexion.php');

// 2. Protección de la página (Mesero)
if (!isset($_SESSION['id_trabajador']) || $_SESSION['id_cargo'] != 3) {
    header("Location: " . $base_url . "login_universal.php");
    exit;
}

// 3. Obtener los IDs y la ACCIÓN de la URL
$id_detalle = $_GET['id_detalle'] ?? 0;
$id_comanda = $_GET['id_comanda'] ?? 0;
$accion = $_GET['accion'] ?? ''; // 'sumar', 'restar', 'eliminar'

if ($id_detalle <= 0 || $id_comanda <= 0 || empty($accion)) {
    die("Error: Datos no válidos. <a href='panel.php'>Volver</a>");
}

// 4. ¡¡INICIAMOS TRANSACCIÓN!!
$conexion->autocommit(FALSE);

try {
    // PASO A: Obtener el ítem actual y su precio unitario
    $sql_item = "SELECT cd.cantidad, m.precio_menu 
                 FROM comanda_detalle cd
                 JOIN menus m ON cd.id_menu = m.id_menu
                 WHERE cd.id_detalle = ? LIMIT 1";
    $stmt_item = $conexion->prepare($sql_item);
    $stmt_item->bind_param("i", $id_detalle);
    $stmt_item->execute();
    $item = $stmt_item->get_result()->fetch_assoc();
    
    if (!$item) {
        throw new Exception("No se encontró el ítem.");
    }

    $precio_unitario = $item['precio_menu'];
    $cantidad_actual = $item['cantidad'];
    $monto_a_ajustar = 0; // Cuánto vamos a sumar/restar al TOTAL

    // PASO B: Decidir qué hacer según la acción
    if ($accion == 'sumar') {
        // Sumar 1
        $sql_update_item = "UPDATE comanda_detalle SET cantidad = cantidad + 1, subtotal = subtotal + ? WHERE id_detalle = ?";
        $stmt_update_item = $conexion->prepare($sql_update_item);
        $stmt_update_item->bind_param("di", $precio_unitario, $id_detalle);
        $monto_a_ajustar = $precio_unitario;

    } elseif ($accion == 'restar' && $cantidad_actual > 1) {
        // Restar 1 (solo si hay más de 1)
        $sql_update_item = "UPDATE comanda_detalle SET cantidad = cantidad - 1, subtotal = subtotal - ? WHERE id_detalle = ?";
        $stmt_update_item = $conexion->prepare($sql_update_item);
        $stmt_update_item->bind_param("di", $precio_unitario, $id_detalle);
        $monto_a_ajustar = -$precio_unitario; // Restamos

    } elseif ($accion == 'restar' && $cantidad_actual == 1) {
        // La cantidad es 1 y se pide restar, ¡así que borramos!
        $sql_update_item = "DELETE FROM comanda_detalle WHERE id_detalle = ?";
        $stmt_update_item = $conexion->prepare($sql_update_item);
        $stmt_update_item->bind_param("i", $id_detalle);
        $monto_a_ajustar = -$precio_unitario; // Restamos

    } elseif ($accion == 'eliminar') {
        // Borrar toda la línea, sin importar la cantidad
        $sql_update_item = "DELETE FROM comanda_detalle WHERE id_detalle = ?";
        $stmt_update_item = $conexion->prepare($sql_update_item);
        $stmt_update_item->bind_param("i", $id_detalle);
        $monto_a_ajustar = -($precio_unitario * $cantidad_actual); // Restamos el subtotal completo
    }

    // Ejecutamos la acción del paso B
    if (isset($stmt_update_item)) {
        $stmt_update_item->execute();
        $stmt_update_item->close();
    } else {
        throw new Exception("Acción no válida.");
    }

    // PASO C: Actualizar (sumar o restar) el 'total' en la comanda principal
    $sql_update_total = "UPDATE comandas SET total = total + ? WHERE id_comanda = ?";
    $stmt_update_total = $conexion->prepare($sql_update_total);
    $stmt_update_total->bind_param("di", $monto_a_ajustar, $id_comanda);
    $stmt_update_total->execute();
    $stmt_update_total->close();

    // PASO D: Confirmamos los cambios
    $conexion->commit();

    // 5. Redirigir de vuelta a la página de edición
    header("Location: " . $base_url . "mesero/editar_comanda.php?id=" . $id_comanda);
    exit;

} catch (Exception $e) {
    $conexion->rollback();
    die("Error al actualizar la cantidad: " . $e->getMessage() . ". <a href='editar_comanda.php?id=" . $id_comanda . "'>Volver</a>");
}

$conexion->close();
?>