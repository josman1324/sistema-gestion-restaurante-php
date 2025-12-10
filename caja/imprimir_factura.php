<?php
// 1. Incluimos config (para la sesión) y conexión
include('../php/config.php');
include('../php/conexion.php');

// 2. Protección de la página (solo cajeros)
if (!isset($_SESSION['id_cajero']) || $_SESSION['id_cargo'] != 2) {
    header("Location: " . $base_url . "login_universal.php");
    exit;
}

// 3. Obtener el ID de la comanda de la URL
$id_comanda = $_GET['id'] ?? 0;
if ($id_comanda <= 0) {
    die("Error: ID de comanda no válido.");
}

// 4. Obtener la información principal de la comanda
$sql_main = "SELECT c.*, p.metodo_pago, p.fecha_pago,
             t.nombre_trabajador as mesero,
             cl.nombre_cliente
             FROM comandas c
             LEFT JOIN pagos p ON c.id_comanda = p.id_comanda
             LEFT JOIN trabajadores t ON c.id_trabajador = t.id_trabajador
             LEFT JOIN clientes cl ON c.id_cliente = cl.id_cliente
             WHERE c.id_comanda = ?";
$stmt_main = $conexion->prepare($sql_main);
$stmt_main->bind_param("i", $id_comanda);
$stmt_main->execute();
$comanda = $stmt_main->get_result()->fetch_assoc();

if (!$comanda) {
    die("Comanda no encontrada.");
}

// 5. Obtener los items (detalles) de la comanda
$sql_items = "SELECT cd.cantidad, m.nombre_menu, cd.subtotal
              FROM comanda_detalle cd
              JOIN menus m ON cd.id_menu = m.id_menu
              WHERE cd.id_comanda = ?";
$stmt_items = $conexion->prepare($sql_items);
$stmt_items->bind_param("i", $id_comanda);
$stmt_items->execute();
$items = $stmt_items->get_result();

// ----- ¡NUEVA LÓGICA! -----
// Verificamos si la comanda está pagada
$esta_pagado = $comanda['metodo_pago'] !== NULL;
// ----------------------------

$atendido_por = $comanda['mesero'] ?? 'Pedido Online';
$cliente_nombre = $comanda['nombre_cliente'] ?? 'Cliente General';
$mesa_display = $comanda['numero_mesa'] == 0 ? 'Online' : $comanda['numero_mesa'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comanda #<?php echo $id_comanda; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f0f0;
            font-family: 'Arial', sans-serif;
        }
        .factura-container {
            width: 80mm; /* Ancho de ticket de impresora térmica */
            max-width: 400px;
            margin: 20px auto;
            background: #fff;
            padding: 25px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .factura-header {
            text-align: center;
            border-bottom: 2px dashed #ccc;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        .factura-header img {
            width: 60px;
            margin-bottom: 10px;
        }
        .factura-header h4 {
            margin: 0;
            font-weight: bold;
        }
        .factura-body p {
            margin-bottom: 5px;
            font-size: 0.9em;
        }
        .factura-body strong {
            min-width: 100px;
            display: inline-block;
        }
        .items-table {
            width: 100%;
            font-size: 0.9em;
            margin-top: 15px;
        }
        .items-table th {
            text-align: left;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }
        .items-table td {
            padding: 5px 0;
        }
        .total-row td {
            border-top: 2px solid #000;
            font-weight: bold;
            font-size: 1.1em;
            padding-top: 10px;
        }
        .factura-footer {
            text-align: center;
            font-size: 0.85em;
            margin-top: 20px;
        }
        
        /* Estilos para ocultar botones al imprimir */
        @media print {
            body {
                background-color: #fff;
            }
            .no-print {
                display: none !important;
            }
            .factura-container {
                margin: 0;
                box-shadow: none;
                width: 100%;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

    <div class="container text-center py-3 no-print">
        <a href="panel_caja.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver al Panel
        </a>
        <button onclick="window.print()" class="btn btn-success">
            <i class="bi bi-printer-fill"></i> Imprimir
        </button>
    </div>

    <div class="factura-container" id="factura">
        <div class="factura-header">
            <img src="<?php echo $base_url; ?>img/arbol.png" alt="Logo">
            
            <h4><?php echo $esta_pagado ? 'Factura de Venta' : 'Pre-Cuenta'; ?></h4>
            <p>Restaurante El Patio</p>
        </div>

        <div class="factura-body">
            <p><strong>Comanda #</strong> <?php echo $id_comanda; ?></p>
            
            <?php if ($esta_pagado): ?>
                <p><strong>Fecha Pago:</strong> <?php echo date("d/m/Y H:i", strtotime($comanda['fecha_pago'])); ?></p>
                <p><strong>Pago:</strong> <?php echo htmlspecialchars($comanda['metodo_pago']); ?></p>
            <?php else: ?>
                <p><strong>Fecha Pedido:</strong> <?php echo date("d/m/Y H:i", strtotime($comanda['fecha'])); ?></p>
                <p><strong>Pago:</strong> <span class="fw-bold text-danger">PENDIENTE DE PAGO</span></p>
            <?php endif; ?>
            <p><strong>Cliente:</strong> <?php echo htmlspecialchars($cliente_nombre); ?></p>
            <p><strong>Mesa:</strong> <?php echo $mesa_display; ?></p>
            <p><strong>Atendió:</strong> <?php echo htmlspecialchars($atendido_por); ?></p>

            <table class="items-table">
                <thead>
                    <tr>
                        <th>Cant.</th>
                        <th>Producto</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($item = $items->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $item['cantidad']; ?></td>
                            <td><?php echo htmlspecialchars($item['nombre_menu']); ?></td>
                            <td class="text-end">$<?php echo number_format($item['subtotal'], 0, ',', '.'); ?></td>
                        </tr>
                    <?php endwhile; ?>
                    
                    <tr class="total-row">
                        <td colspan="2">TOTAL:</td>
                        <td class="text-end">$<?php echo number_format($comanda['total'], 0, ',', '.'); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="factura-footer">
            <p>¡Gracias por preferirnos!</p>
            <p>Restaurante El Patio</p>
        </div>
    </div>

</body>
</html>