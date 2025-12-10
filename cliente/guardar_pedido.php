<?php
// Incluimos config.php PRIMERO
include('../php/config.php'); 
include('../php/conexion.php'); 

// 1. Validar que sea un cliente y que el carrito exista
if (!isset($_SESSION['id_cliente'])) {
    die("Error: Debes iniciar sesión como cliente.");
}

$carrito = $_SESSION['carrito'] ?? array();
$id_cliente = $_SESSION['id_cliente'];
$total_pagado = $_POST['total_pagado'] ?? 0;

if (empty($carrito) || $total_pagado <= 0) {
    die("Error: Tu carrito está vacío o el total es inválido. <a href='../index.php'>Volver al menú</a>");
}

// 2. ¡Usar Transacciones!
$conexion->autocommit(FALSE);

try {
    // 3. PASO 1: Insertar la comanda principal
    // (id_cliente = ?, id_trabajador = NULL, numero_mesa = 0, total = ?)
    $sql_comanda = "INSERT INTO comandas (id_cliente, id_trabajador, numero_mesa, total, estado) 
                    VALUES (?, NULL, 0, ?, 'Pagado')";
    $stmt_comanda = $conexion->prepare($sql_comanda);

    // ¡¡AQUÍ ESTÁ LA CORRECCIÓN!!
    // Mi 'bind_param' anterior era incorrecto.
    // 'i' = id_cliente (entero)
    // 'd' = total_pagado (double/decimal)
    $stmt_comanda->bind_param("id", $id_cliente, $total_pagado);
    
    $stmt_comanda->execute();

    // 4. PASO 2: Obtener el ID de la comanda
    $id_comanda = $conexion->insert_id;

    if ($id_comanda <= 0) {
        throw new Exception("No se pudo crear la comanda principal.");
    }

    // 5. PASO 3: Insertar cada item del carrito en 'comanda_detalle'
    $sql_detalle = "INSERT INTO comanda_detalle (id_comanda, id_menu, cantidad, subtotal) 
                    VALUES (?, ?, ?, ?)";
    $stmt_detalle = $conexion->prepare($sql_detalle);

    foreach ($carrito as $item) {
        $subtotal = $item['precio'] * $item['cantidad'];
        $stmt_detalle->bind_param("iiid", $id_comanda, $item['id'], $item['cantidad'], $subtotal);
        $stmt_detalle->execute();
    }
    
    // 6. PASO 4 (Simular Pago): Insertar en la tabla 'pagos'
    $sql_pago = "INSERT INTO pagos (id_comanda, id_cajero, metodo_pago, total_pagado, fecha_pago)
                 VALUES (?, NULL, 'Online', ?, NOW())";
    $stmt_pago = $conexion->prepare($sql_pago);
    $stmt_pago->bind_param("id", $id_comanda, $total_pagado);
    $stmt_pago->execute();


    // 7. PASO 5: Aplicar los cambios
    $conexion->commit();

    // 8. PASO 6: Limpiar el carrito
    unset($_SESSION['carrito']);

    // 9. Redirigir a la página de éxito
    header("Location: " . $base_url . "cliente/pago_exitoso.php?id_comanda=" . $id_comanda);
    exit;

} catch (Exception $e) {
    // 10. Si algo falló, revertir todo
    $conexion->rollback();
    // Mostramos el error real de MySQL
    die("Error al procesar tu pedido: " . $e->getMessage() . ". Por favor, inténtalo de nuevo.");
}

$conexion->close();
?>