<?php
// Incluimos config.php PRIMERO
include('../php/config.php'); 
include('../php/conexion.php');

// 1. Validar Sesión
if (!isset($_SESSION['id_cajero']) || $_SESSION['id_cargo'] != 2) {
    header("Location: " . $base_url . "login_universal.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 2. (Esta es tu lógica original, que es perfecta)
    $id_comanda = $_POST['id_comanda'];
    $metodo = $_POST['metodo_pago'];
    $total = $_POST['total'];
    $id_cajero = $_SESSION['id_cajero'];
    $fecha = date('Y-m-d H:i:s');
    
    // Si es efectivo y el monto recibido es 0 o vacío, se asume pago exacto.
    $monto_recibido = !empty($_POST['monto_recibido']) ? $_POST['monto_recibido'] : $total;
    
    // Si el método no es efectivo, el total pagado es el total exacto.
    $total_pagado = ($metodo === 'Efectivo') ? $monto_recibido : $total;

    // 3. (Tu consulta segura, que es excelente)
    $stmt = $conexion->prepare("INSERT INTO pagos (id_comanda, id_cajero, metodo_pago, total_pagado, fecha_pago) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('iisds', $id_comanda, $id_cajero, $metodo, $total_pagado, $fecha);
    $stmt->execute();
    
    // 4. (Opcional pero recomendado) Actualizar el estado de la comanda
    // Esto es útil si tuvieras un estado 'Entregado' o 'Pagado' en la tabla 'comandas'
    // $stmt_update = $conexion->prepare("UPDATE comandas SET estado = 'Pagado' WHERE id_comanda = ?");
    // $stmt_update->bind_param('i', $id_comanda);
    // $stmt_update->execute();

    // 5. Redirigir de forma segura usando $base_url
    header('Location: ' . $base_url . 'caja/panel_caja.php');
    exit();
}
?>