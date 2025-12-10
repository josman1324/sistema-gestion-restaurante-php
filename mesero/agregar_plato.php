<?php
// 1. Incluimos config y conexión
include('../php/config.php');
include('../php/conexion.php');

// 2. Protección de la página (Mesero)
if (!isset($_SESSION['id_trabajador']) || $_SESSION['id_cargo'] != 3) {
    header("Location: " . $base_url . "login_universal.php");
    exit;
}

// 3. Verificar que se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 4. Obtener los datos del formulario
    $id_comanda = $_POST['id_comanda'] ?? 0;
    $id_menu = $_POST['id_menu'] ?? 0;
    $cantidad_a_agregar = $_POST['cantidad'] ?? 0;

    if ($id_comanda <= 0 || $id_menu <= 0 || $cantidad_a_agregar <= 0) {
        die("Error: Datos incompletos. <a href='panel.php'>Volver</a>");
    }

    // 5. ¡¡INICIAMOS TRANSACCIÓN!!
    $conexion->autocommit(FALSE);

    try {
        // PASO A: Obtener el precio del menú
        $sql_precio = "SELECT precio_menu FROM menus WHERE id_menu = ? LIMIT 1";
        $stmt_precio = $conexion->prepare($sql_precio);
        $stmt_precio->bind_param("i", $id_menu);
        $stmt_precio->execute();
        $precio_unitario = $stmt_precio->get_result()->fetch_assoc()['precio_menu'] ?? 0;
        
        if ($precio_unitario <= 0) {
            throw new Exception("El plato seleccionado no tiene un precio válido.");
        }
        
        // Calcular el subtotal de lo que se AÑADE
        $subtotal_a_agregar = $precio_unitario * $cantidad_a_agregar;

        // PASO B: ¡LÓGICA INTELIGENTE!
        // ¿Este plato YA existe en esta comanda?
        $sql_check = "SELECT id_detalle, cantidad FROM comanda_detalle WHERE id_comanda = ? AND id_menu = ? LIMIT 1";
        $stmt_check = $conexion->prepare($sql_check);
        $stmt_check->bind_param("ii", $id_comanda, $id_menu);
        $stmt_check->execute();
        $item_existente = $stmt_check->get_result()->fetch_assoc();
        $stmt_check->close();

        if ($item_existente) {
            // SÍ EXISTE: Actualizamos la cantidad y el subtotal
            $id_detalle_existente = $item_existente['id_detalle'];
            $sql_update_item = "UPDATE comanda_detalle 
                                SET cantidad = cantidad + ?, subtotal = subtotal + ? 
                                WHERE id_detalle = ?";
            $stmt_update_item = $conexion->prepare($sql_update_item);
            $stmt_update_item->bind_param("idi", $cantidad_a_agregar, $subtotal_a_agregar, $id_detalle_existente);
            $stmt_update_item->execute();
            $stmt_update_item->close();

        } else {
            // NO EXISTE: Insertamos una nueva fila
            $sql_insert = "INSERT INTO comanda_detalle (id_comanda, id_menu, cantidad, subtotal) VALUES (?, ?, ?, ?)";
            $stmt_insert = $conexion->prepare($sql_insert);
            $stmt_insert->bind_param("iiid", $id_comanda, $id_menu, $cantidad_a_agregar, $subtotal_a_agregar);
            $stmt_insert->execute();
            $stmt_insert->close();
        }

        // PASO C: Actualizar el 'total' en la comanda principal
        $sql_update_total = "UPDATE comandas SET total = total + ? WHERE id_comanda = ?";
        $stmt_update_total = $conexion->prepare($sql_update_total);
        $stmt_update_total->bind_param("di", $subtotal_a_agregar, $id_comanda);
        $stmt_update_total->execute();
        $stmt_update_total->close();

        // PASO D: Confirmamos los cambios
        $conexion->commit();

        // 6. Redirigir de vuelta a la página de edición
        header("Location: " . $base_url . "mesero/editar_comanda.php?id=" . $id_comanda);
        exit;

    } catch (Exception $e) {
        $conexion->rollback();
        die("Error al agregar el plato: " . $e->getMessage() . ". <a href='editar_comanda.php?id=" . $id_comanda . "'>Volver</a>");
    }

    $conexion->close();

} else {
    header("Location: " . $base_url . "mesero/panel.php");
    exit;
}
?>