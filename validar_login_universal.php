<?php
// Incluimos config.php PRIMERO
// Esto inicia la sesión y nos da la $base_url
include(__DIR__ . '/php/config.php'); 
include('php/conexion.php');

$usuario_input = $_POST['usuario'] ?? '';
$clave_input = $_POST['clave'] ?? '';

if (empty($usuario_input) || empty($clave_input)) {
    header("Location: " . $base_url . "login_universal.php?error=Campos vacíos");
    exit;
}

// --- PASO 1: TRABAJADOR ---
$sql_trabajador = "SELECT id_trabajador, nombre_trabajador, usuario, clave, id_cargo FROM trabajadores WHERE usuario = ?";
$stmt = $conexion->prepare($sql_trabajador);
$stmt->bind_param("s", $usuario_input);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    $trabajador = $resultado->fetch_assoc();
    
    // (Lógica temporal insegura)
    if ($clave_input == $trabajador['clave']) {
        switch ($trabajador['id_cargo']) {
            case 1: // Admin
                $_SESSION['admin'] = $trabajador['nombre_trabajador'];
                $_SESSION['id_cargo'] = $trabajador['id_cargo'];
                header("Location: " . $base_url . "administrador/opciones_admin.php");
                exit;
            case 2: // Caja
                $_SESSION['cajero'] = $trabajador['nombre_trabajador'];
                $_SESSION['id_cajero'] = $trabajador['id_trabajador'];
                $_SESSION['id_cargo'] = $trabajador['id_cargo'];
                header("Location: " . $base_url . "caja/panel_caja.php");
                exit;
            case 3: // Mesero
                $_SESSION['id_trabajador'] = $trabajador['id_trabajador'];
                $_SESSION['nombre_usuario'] = $trabajador['nombre_trabajador'];
                $_SESSION['id_cargo'] = $trabajador['id_cargo'];
                header("Location: " . $base_url . "mesero/panel.php");
                exit;
        }
    }
}

// --- PASO 2: CLIENTE ---
$sql_cliente = "SELECT id_cliente, nombre_cliente, clave_cliente FROM clientes WHERE email_cliente = ?";
$stmt_cliente = $conexion->prepare($sql_cliente);
$stmt_cliente->bind_param("s", $usuario_input);
$stmt_cliente->execute();
$resultado_cliente = $stmt_cliente->get_result();

if ($resultado_cliente->num_rows > 0) {
    $cliente = $resultado_cliente->fetch_assoc();
    if (password_verify($clave_input, $cliente['clave_cliente'])) {
        $_SESSION['id_cliente'] = $cliente['id_cliente'];
        $_SESSION['nombre_cliente'] = $cliente['nombre_cliente'];
        header("Location: " . $base_url . "cliente/panel.php");
        exit;
    }
}

// --- PASO 3: FALLO ---
header("Location: " . $base_url . "login_universal.php?error=Usuario o contraseña incorrectos");
exit;
?>