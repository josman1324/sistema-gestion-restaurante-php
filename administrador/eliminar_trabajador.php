<?php
include('../php/config.php');
include('../php/conexion.php');

// Protección de Admin
if (!isset($_SESSION['admin']) || $_SESSION['id_cargo'] != 1) {
    die("Acceso denegado.");
}

// 1. Obtener el ID del admin actual (para no borrarse a sí mismo)
$nombre_admin_actual = $_SESSION['admin'];
$sql_admin = "SELECT id_trabajador FROM trabajadores WHERE nombre_trabajador = ? LIMIT 1";
$stmt_admin = $conexion->prepare($sql_admin);
$stmt_admin->bind_param("s", $nombre_admin_actual);
$stmt_admin->execute();
$id_admin_actual = $stmt_admin->get_result()->fetch_assoc()['id_trabajador'];
$stmt_admin->close();

// 2. Obtener el ID a borrar
$id_a_borrar = $_GET['id'] ?? 0;
if ($id_a_borrar <= 0) {
    die("ID de trabajador no válido.");
}

// 3. ¡Verificación de seguridad!
if ($id_a_borrar == $id_admin_actual) {
    die("Error: No puedes eliminarte a ti mismo desde la aplicación.");
}

try {
    // 4. Intentar borrar al trabajador
    $sql = "DELETE FROM trabajadores WHERE id_trabajador = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_a_borrar);
    $stmt->execute();
    $stmt->close();
    
    // 5. Si todo sale bien, volvemos
    header("Location: " . $base_url . "administrador/gestion_trabajadores.php");
    exit;

} catch (mysqli_sql_exception $e) {
    // 6. ¡CAPTURA DE ERROR! (Error de Foreign Key)
    if ($e->getCode() == 1451) {
        // Mostramos un error amigable
        include('../includes/header.php');
        echo "<div class='alert alert-danger m-5' role='alert'>
                <h4 class='alert-heading'>¡No se puede borrar!</h4>
                <p>Este trabajador (ID: $id_a_borrar) no se puede eliminar porque ya está asociado a comandas o pagos anteriores.</p>
                <p>Si ya no trabaja aquí, te recomendamos editarlo y poner '(INACTIVO)' en su nombre.</p>
                <hr>
                <a href='gestion_trabajadores.php' class='btn btn-danger'>Volver</a>
              </div>";
        include('../includes/footer.php');
    } else {
        // Otro error de SQL
        die("Error de base de datos: " . $e->getMessage());
    }
}
$conexion->close();
?>