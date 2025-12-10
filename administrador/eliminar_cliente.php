<?php
include('../php/config.php');
include('../php/conexion.php');

// Protección de Admin
if (!isset($_SESSION['admin']) || $_SESSION['id_cargo'] != 1) {
    die("Acceso denegado.");
}

$id_a_borrar = $_GET['id'] ?? 0;
if ($id_a_borrar <= 0) {
    die("ID de cliente no válido.");
}

try {
    // 1. Intentar borrar al cliente
    $sql = "DELETE FROM clientes WHERE id_cliente = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_a_borrar);
    $stmt->execute();
    $stmt->close();
    
    // 2. Si todo sale bien, volvemos
    header("Location: " . $base_url . "administrador/gestion_clientes.php");
    exit;

} catch (mysqli_sql_exception $e) {
    // 3. ¡CAPTURA DE ERROR! (Error de Foreign Key)
    if ($e->getCode() == 1451) {
        // Mostramos un error amigable
        include('../includes/header.php');
        echo "<div class='alert alert-danger m-5' role='alert'>
                <h4 class='alert-heading'>¡No se puede borrar!</h4>
                <p>Este cliente (ID: $id_a_borrar) no se puede eliminar porque ya está asociado a comandas o pedidos anteriores.</p>
                <p>Si quieres bloquear su acceso, te recomendamos editarlo y cambiar su contraseña.</p>
                <hr>
                <a href='gestion_clientes.php' class='btn btn-danger'>Volver</a>
              </div>";
        include('../includes/footer.php');
    } else {
        // Otro error de SQL
        die("Error de base de datos: " . $e->getMessage());
    }
}
$conexion->close();
?>