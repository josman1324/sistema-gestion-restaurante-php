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
    $nuevo_estado = $_POST['nuevo_estado'] ?? '';

    // 5. Validar datos
    if ($id_comanda <= 0 || ($nuevo_estado != 'Entregado' && $nuevo_estado != 'En espera')) {
        die("Error: Datos no válidos. <a href='panel.php'>Volver</a>");
    }

    // 6. Actualizar el estado en la base de datos
    $sql = "UPDATE comandas SET estado = ? WHERE id_comanda = ?";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("si", $nuevo_estado, $id_comanda);
    
    if ($stmt->execute()) {
        // 7. Si todo sale bien, redirigir de vuelta a la página de edición
        header("Location: " . $base_url . "mesero/editar_comanda.php?id=" . $id_comanda);
        exit;
    } else {
        die("Error al actualizar el estado: " . $conexion->error);
    }

    $stmt->close();
    $conexion->close();

} else {
    // Si alguien intenta acceder a este archivo directamente por URL
    header("Location: " . $base_url . "mesero/panel.php");
    exit;
}
?>