<?php
/*
|--------------------------------------------------------------------------
| AJAX - CARRITO DE COMPRAS (VERSIÓN A PRUEBA DE BALAS)
|--------------------------------------------------------------------------
*/

// 1. Ocultar errores "Warning" que pueden romper el JSON
error_reporting(0);

// 2. Incluir archivos (que ya inician la sesión)
include(__DIR__ . '/php/config.php');
include(__DIR__ . '/php/conexion.php');

// 3. Inicializar carrito
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = array();
}

// 4. Obtener datos
$id_menu = $_POST['id_menu'] ?? 0;
$cantidad = $_POST['cantidad'] ?? 1;
$id_menu = intval($id_menu);
$cantidad = intval($cantidad);

$response = []; // Usaremos un array para la respuesta

if ($id_menu <= 0 || $cantidad <= 0) {
    $response = ['status' => 'error', 'message' => 'Datos inválidos'];
} else {
    // 5. Lógica de la base de datos
    $sql = "SELECT nombre_menu, precio_menu FROM menus WHERE id_menu = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_menu);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $producto = $resultado->fetch_assoc();
        
        // Añadir a la sesión
        if (isset($_SESSION['carrito'][$id_menu])) {
            $_SESSION['carrito'][$id_menu]['cantidad'] += $cantidad;
        } else {
            $_SESSION['carrito'][$id_menu] = [
                'id' => $id_menu,
                'nombre' => $producto['nombre_menu'],
                'precio' => $producto['precio_menu'],
                'cantidad' => $cantidad
            ];
        }
        
        // Calcular total de items
        $total_items = 0;
        foreach ($_SESSION['carrito'] as $item) {
            $total_items += $item['cantidad'];
        }

        // Preparar respuesta de éxito
        $response = [
            'status' => 'success',
            'message' => '¡' . htmlspecialchars($producto['nombre_menu']) . ' añadido al carrito!',
            'nuevo_total_items' => $total_items
        ];
        
    } else {
        // Preparar respuesta de error
        $response = ['status' => 'error', 'message' => 'Producto no encontrado'];
    }

    $stmt->close();
    $conexion->close();
}

// --- ¡EL ARREGLO MÁGICO! ---

// 6. Limpiar cualquier "basura" (BOM, espacios) que se haya impreso antes.
if (ob_get_length()) {
    ob_clean();
}

// 7. Establecer la cabecera JSON
header('Content-Type: application/json');

// 8. Imprimir el JSON y NADA MÁS
echo json_encode($response);

exit; 
