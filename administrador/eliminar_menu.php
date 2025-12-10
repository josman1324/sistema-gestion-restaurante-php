<?php
include('../php/config.php');
include('../php/conexion.php');

// Protección de Admin
if (!isset($_SESSION['admin']) || $_SESSION['id_cargo'] != 1) {
    die("Acceso denegado.");
}

$id_menu = $_GET['id'] ?? 0;
if ($id_menu <= 0) {
    die("ID de menú no válido.");
}

try {
    // ¡BORRADO SEGURO!
    // 1. Primero borramos la relación en 'menu_dias' (los días que se sirve)
    // Usamos ON DELETE CASCADE en la BD sería mejor, pero esto funciona.
    $sql_dias = "DELETE FROM menu_dias WHERE id_menu = ?";
    $stmt_dias = $conexion->prepare($sql_dias);
    $stmt_dias->bind_param("i", $id_menu);
    $stmt_dias->execute();
    $stmt_dias->close();
    
    // 2. Ahora intentamos borrar el menú principal
    $sql = "DELETE FROM menus WHERE id_menu = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_menu);
    $stmt->execute();
    $stmt->close();
    
    // 3. Si todo sale bien, volvemos
    header("Location: " . $base_url . "administrador/gestion_menus.php");
    exit;

} catch (mysqli_sql_exception $e) {
    // 4. ¡CAPTURA DE ERROR!
    // Si $e->getCode() es 1451, es un error de Foreign Key.
    if ($e->getCode() == 1451) {
        // Mostramos un error amigable en vez de romper la web
        include('../includes/header.php');
        echo "<div class='alert alert-danger m-5' role='alert'>
                <h4 class='alert-heading'>¡No se puede borrar!</h4>
                <p>Este menú (ID: $id_menu) no se puede eliminar porque ya está asociado a comandas y pedidos anteriores.</p>
                <p>Si ya no quieres venderlo, te recomendamos editarlo y poner '(INACTIVO)' en el nombre.</p>
                <hr>
                <a href='gestion_menus.php' class='btn btn-danger'>Volver</a>
              </div>";
        include('../includes/footer.php');
    } else {
        // Otro error de SQL
        die("Error de base de datos: " . $e->getMessage());
    }
}
$conexion->close();
?>