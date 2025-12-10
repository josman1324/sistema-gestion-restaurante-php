<?php
include('../php/config.php');
include('../php/conexion.php');

// Protección de Admin
if (!isset($_SESSION['admin']) || $_SESSION['id_cargo'] != 1) {
    die("Acceso denegado.");
}

// 1. Recibir todos los datos del formulario
$id_trabajador = $_POST['id_trabajador'] ?? 0;
$nombre = $_POST['nombre_trabajador'] ?? '';
$usuario = $_POST['usuario'] ?? '';
$clave = $_POST['clave'] ?? ''; // Puede estar vacía si es una edición
$id_cargo = $_POST['id_cargo'] ?? 0;

// 2. Validar datos básicos
if (empty($nombre) || empty($usuario) || $id_cargo <= 0) {
    die("Error: Nombre, Usuario y Cargo son obligatorios.");
}

// 3. Decidir si es CREAR (INSERT) o ACTUALIZAR (UPDATE)
try {
    if ($id_trabajador > 0) {
        // --- ES UNA ACTUALIZACIÓN (UPDATE) ---
        
        if (empty($clave)) {
            // Si la clave está vacía, NO la actualizamos
            $sql = "UPDATE trabajadores SET nombre_trabajador = ?, usuario = ?, id_cargo = ? 
                    WHERE id_trabajador = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("ssii", $nombre, $usuario, $id_cargo, $id_trabajador);
        } else {
            // Si hay clave nueva, la hasheamos y actualizamos
            $clave_hasheada = password_hash($clave, PASSWORD_DEFAULT);
            $sql = "UPDATE trabajadores SET nombre_trabajador = ?, usuario = ?, clave = ?, id_cargo = ? 
                    WHERE id_trabajador = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("sssii", $nombre, $usuario, $clave_hasheada, $id_cargo, $id_trabajador);
        }
        
    } else {
        // --- ES UNA CREACIÓN (INSERT) ---
        if (empty($clave)) {
            die("Error: La contraseña es obligatoria para un nuevo trabajador.");
        }
        $clave_hasheada = password_hash($clave, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO trabajadores (nombre_trabajador, usuario, clave, id_cargo) 
                VALUES (?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("sssi", $nombre, $usuario, $clave_hasheada, $id_cargo);
    }
    
    // 4. Ejecutar la consulta
    $stmt->execute();
    $stmt->close();
    
    // 5. Redirigir de vuelta al panel
    header("Location: " . $base_url . "administrador/gestion_trabajadores.php");
    exit;

} catch (Exception $e) {
    // Manejar errores (ej. un 'usuario' duplicado)
    die("Error al guardar en la base de datos: " . $e->getMessage());
}

$conexion->close();
?>