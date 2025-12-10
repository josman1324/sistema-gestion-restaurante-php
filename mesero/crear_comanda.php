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
    
    // 4. Obtener los datos (ID del mesero y número de mesa)
    $id_mesero = $_SESSION['id_trabajador'];
    $numero_mesa = $_POST['numero_mesa'] ?? 0;

    if ($numero_mesa <= 0) {
        die("Error: El número de mesa no es válido. <a href='panel.php'>Volver</a>");
    }

    // 5. Crear la comanda vacía en la base de datos
    // El total es 0 y el estado es 'En espera' por defecto
    $sql = "INSERT INTO comandas (id_trabajador, numero_mesa, total, estado) VALUES (?, ?, 0, 'En espera')";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $id_mesero, $numero_mesa);
    
    if ($stmt->execute()) {
        // 6. Si la comanda se crea con éxito, obtenemos su nuevo ID
        $id_nueva_comanda = $conexion->insert_id;
        
        // 7. Redirigimos al mesero a la página de EDICIÓN
        // para que pueda empezar a añadir platos a esta nueva comanda.
        header("Location: " . $base_url . "mesero/editar_comanda.php?id=" . $id_nueva_comanda);
        exit;
    } else {
        die("Error al crear la comanda en la base de datos: " . $conexion->error);
    }

    $stmt->close();
    $conexion->close();

} else {
    // Si alguien intenta acceder a este archivo directamente por URL
    header("Location: " . $base_url . "mesero/panel.php");
    exit;
}
?>