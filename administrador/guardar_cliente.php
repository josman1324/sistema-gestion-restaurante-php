<?php
include('../php/config.php');
include('../php/conexion.php');

// Protección de Admin
if (!isset($_SESSION['admin']) || $_SESSION['id_cargo'] != 1) {
    die("Acceso denegado.");
}

// 1. Recibir todos los datos del formulario
$id_cliente = $_POST['id_cliente'] ?? 0;
$nombre = $_POST['nombre_cliente'] ?? '';
$email = $_POST['email_cliente'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$clave = $_POST['clave'] ?? ''; // Puede estar vacía si es una edición

// 2. Validar datos básicos
if (empty($nombre) || empty($email)) {
    die("Error: Nombre y Email son obligatorios.");
}

// 3. Decidir si es CREAR (INSERT) o ACTUALIZAR (UPDATE)
try {
    if ($id_cliente > 0) {
        // --- ES UNA ACTUALIZACIÓN (UPDATE) ---
        
        if (empty($clave)) {
            // Si la clave está vacía, NO la actualizamos
            $sql = "UPDATE clientes SET nombre_cliente = ?, email_cliente = ?, telefono = ? 
                    WHERE id_cliente = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("sssi", $nombre, $email, $telefono, $id_cliente);
        } else {
            // Si hay clave nueva, la hasheamos y actualizamos
            $clave_hasheada = password_hash($clave, PASSWORD_DEFAULT);
            $sql = "UPDATE clientes SET nombre_cliente = ?, email_cliente = ?, telefono = ?, clave_cliente = ? 
                    WHERE id_cliente = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("ssssi", $nombre, $email, $telefono, $clave_hasheada, $id_cliente);
        }
        
    } else {
        // --- ES UNA CREACIÓN (INSERT) ---
        if (empty($clave)) {
            die("Error: La contraseña es obligatoria para un nuevo cliente.");
        }
        $clave_hasheada = password_hash($clave, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO clientes (nombre_cliente, email_cliente, telefono, clave_cliente) 
                VALUES (?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ssss", $nombre, $email, $telefono, $clave_hasheada);
    }
    
    // 4. Ejecutar la consulta
    $stmt->execute();
    $stmt->close();
    
    // 5. Redirigir de vuelta al panel
    header("Location: " . $base_url . "administrador/gestion_clientes.php");
    exit;

} catch (Exception $e) {
    // Manejar errores (ej. un 'email' duplicado)
    die("Error al guardar en la base de datos: " . $e->getMessage());
}

$conexion->close();
?>