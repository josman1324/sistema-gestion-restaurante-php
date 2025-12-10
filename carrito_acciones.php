<?php
/*
|--------------------------------------------------------------------------
| ACCIONES DEL CARRITO (EL QUE FALTABA)
|--------------------------------------------------------------------------
|
| Este archivo maneja las peticiones de la PÁGINA del carrito
| (actualizar cantidad, eliminar, vaciar).
|
*/

// Incluimos config.php PRIMERO (inicia la sesión)
include(__DIR__ . '/php/config.php'); 
// No necesitamos la BD para esto, solo la sesión.

// Inicializar el carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = array();
}

$accion = $_GET['accion'] ?? 'ver'; 
$id_menu = $_GET['id'] ?? 0;
$cantidad = $_GET['cantidad'] ?? 1;
$id_menu = intval($id_menu);

switch ($accion) {
    case 'agregar':
        // Esta acción no debería usarse aquí, pero la dejamos por si acaso
        if ($id_menu > 0) {
            if (isset($_SESSION['carrito'][$id_menu])) {
                $_SESSION['carrito'][$id_menu]['cantidad'] += $cantidad;
            }
            // (Falta lógica para consultar BD si se añade desde aquí,
            // pero el index.php no usa este archivo)
        }
        break;

    case 'actualizar':
        if ($id_menu > 0 && isset($_SESSION['carrito'][$id_menu])) {
            $_SESSION['carrito'][$id_menu]['cantidad'] = intval($cantidad);
            // Si la cantidad es 0 o menos, eliminarlo
            if ($_SESSION['carrito'][$id_menu]['cantidad'] <= 0) {
                unset($_SESSION['carrito'][$id_menu]);
            }
        }
        break;

    case 'eliminar':
        if ($id_menu > 0 && isset($_SESSION['carrito'][$id_menu])) {
            unset($_SESSION['carrito'][$id_menu]);
        }
        break;
    
    case 'vaciar':
        $_SESSION['carrito'] = array();
        break;
}

// Después de CUALQUIER acción, volvemos a la página del carrito
// Usamos la $base_url de config.php para la redirección
header("Location: " . $base_url . "cliente/carrito.php");
exit;
?>