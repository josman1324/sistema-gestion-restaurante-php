<?php
include('../php/config.php');
include('../php/conexion.php');

// Protección de Admin
if (!isset($_SESSION['admin']) || $_SESSION['id_cargo'] != 1) {
    die("Acceso denegado.");
}

// 1. Recibir todos los datos del formulario
$id_menu = $_POST['id_menu'] ?? 0;
$nombre = $_POST['nombre_menu'] ?? '';
$descripcion = $_POST['descripcion_menu'] ?? '';
$precio = $_POST['precio_menu'] ?? 0;
$foto = $_POST['foto_menu'] ?? '';
$dias_seleccionados = $_POST['dias'] ?? []; 

// 2. Validar datos básicos
if (empty($nombre) || $precio <= 0) {
    die("Error: El nombre y el precio son obligatorios.");
}

// Iniciamos Transacción
$conexion->autocommit(FALSE);

try {
    $id_menu_guardado = $id_menu;

    if ($id_menu > 0) {
        // --- ES UNA ACTUALIZACIÓN (UPDATE) ---
        $sql = "UPDATE menus SET nombre_menu = ?, descripcion_menu = ?, precio_menu = ?, foto_menu = ? 
                WHERE id_menu = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ssdsi", $nombre, $descripcion, $precio, $foto, $id_menu);
        $stmt->execute();
        $stmt->close();
        
    } else {
        // --- ES UNA CREACIÓN (INSERT) ---
        $sql = "INSERT INTO menus (nombre_menu, descripcion_menu, precio_menu, foto_menu) 
                VALUES (?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ssds", $nombre, $descripcion, $precio, $foto);
        $stmt->execute();
        $id_menu_guardado = $conexion->insert_id;
        $stmt->close();
    }

    // --- LÓGICA PARA GUARDAR LOS DÍAS (CORREGIDA) ---

    // 1. Borrar TODOS los días antiguos para este menú
    $sql_delete_dias = "DELETE FROM menu_dias WHERE id_menu = ?";
    $stmt_delete = $conexion->prepare($sql_delete_dias);
    $stmt_delete->bind_param("i", $id_menu_guardado);
    $stmt_delete->execute();
    $stmt_delete->close();

    // 2. Si el usuario seleccionó días, los insertamos
    if (!empty($dias_seleccionados)) {
        
        $sql_insert_dia = "INSERT INTO menu_dias (id_menu, id_menu_dia) VALUES (?, ?)";
        $stmt_dia = $conexion->prepare($sql_insert_dia);
        
        foreach ($dias_seleccionados as $id_dia_del_array) {
            
            // ¡¡AQUÍ ESTÁ LA CORRECCIÓN!!
            // Usamos la variable del foreach: $id_dia_del_array
            $stmt_dia->bind_param("ii", $id_menu_guardado, $id_dia_del_array); 
            $stmt_dia->execute();
        }
        $stmt_dia->close();
    }

    // ¡Confirmamos todos los cambios!
    $conexion->commit();
    
    // 5. Redirigir de vuelta al panel
    header("Location: " . $base_url . "administrador/gestion_menus.php");
    exit;

} catch (Exception $e) {
    // Si algo falló, revertimos todo
    $conexion->rollback();
    die("Error al guardar en la base de datos (se revirtieron los cambios): " . $e->getMessage());
}

$conexion->close();
?>