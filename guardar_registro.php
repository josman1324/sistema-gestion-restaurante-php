<?php
// 1. Incluimos config.php PRIMERO (inicia la sesión y $base_url)
include(__DIR__ . '/php/config.php'); 
include('php/conexion.php'); 

// 2. Recibir datos del formulario
$nombre = $_POST['nombre'] ?? '';
$email = $_POST['email'] ?? ''; // Ahora este SÍ llegará
$telefono = $_POST['telefono'] ?? '';
$clave = $_POST['clave'] ?? '';
$confirmar_clave = $_POST['confirmar_clave'] ?? '';

// 3. Validación (Esta es la que te dio el error "Todos los campos...")
if (empty($nombre) || empty($email) || empty($clave)) {
    die("Error: Todos los campos (Nombre, Email, Contraseña) son obligatorios. <a href='registro.php'>Volver</a>");
}

if ($clave !== $confirmar_clave) {
    die("Error: Las contraseñas no coinciden. <a href='registro.php'>Volver</a>");
}

// 4. ¡SEGURIDAD! Hashear la contraseña
$clave_hasheada = password_hash($clave, PASSWORD_DEFAULT);

// 5. Verificar si el email YA existe
$sql_check = "SELECT id_cliente FROM clientes WHERE email_cliente = ?";
$stmt_check = $conexion->prepare($sql_check);
$stmt_check->bind_param("s", $email);
$stmt_check->execute();
$resultado_check = $stmt_check->get_result();

if ($resultado_check->num_rows > 0) {
    die("Error: Ese correo electrónico ya está registrado. <a href='login_universal.php'>Intenta iniciar sesión</a>");
}
$stmt_check->close();

// 6. Insertar el nuevo cliente
$sql_insert = "INSERT INTO clientes (nombre_cliente, email_cliente, telefono, clave_cliente) 
               VALUES (?, ?, ?, ?)";
$stmt_insert = $conexion->prepare($sql_insert);
$stmt_insert->bind_param("ssss", $nombre, $email, $telefono, $clave_hasheada);

if ($stmt_insert->execute()) {
    // Registro exitoso, iniciar sesión automáticamente
    $_SESSION['id_cliente'] = $conexion->insert_id; 
    $_SESSION['nombre_cliente'] = $nombre;
    
    header("Location: " . $base_url . "cliente/panel.php");
    exit;
} else {
    die("Error al registrar la cuenta: " . $conexion->error);
}

$stmt_insert->close();
$conexion->close();
?>