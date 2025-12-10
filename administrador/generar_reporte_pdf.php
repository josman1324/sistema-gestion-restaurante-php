<?php
// 1. Incluimos config, conexión y EL AUTOLOAD DE COMPOSER
include('../php/config.php');
include('../php/conexion.php');
require_once('../vendor/autoload.php'); // Carga la biblioteca PDF!

// 2. Protección de Admin
if (!isset($_SESSION['admin']) || $_SESSION['id_cargo'] != 1) {
    die("Acceso denegado.");
}

// 3. Obtener la fecha
$filtro_fecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');

// 4. Obtener los datos de la base de datos (¡Seguro!)

// Total del día (Lee de 'pagos')
$sql_total = "SELECT SUM(total_pagado) AS total_dia FROM pagos 
              WHERE DATE(CONVERT_TZ(fecha_pago, '+00:00', '-05:00')) = ?";
$stmt_total = $conexion->prepare($sql_total);
$stmt_total->bind_param("s", $filtro_fecha);
$stmt_total->execute();
$total_dia = $stmt_total->get_result()->fetch_assoc()['total_dia'] ?? 0;
$stmt_total->close(); // Cerramos esta
 
// 5. ¡¡CONSULTA CORREGIDA!!
// Ahora lista los PAGOS, no las COMANDAS, para que todo cuadre.
$sql_pagos = "SELECT p.id_pago, p.metodo_pago, p.total_pagado, c.numero_mesa,
              CONVERT_TZ(p.fecha_pago, '+00:00', '-05:00') AS fecha_pagada_colombia
              FROM pagos p
              JOIN comandas c ON p.id_comanda = c.id_comanda
              WHERE DATE(CONVERT_TZ(p.fecha_pago, '+00:00', '-05:00')) = ?
              ORDER BY p.fecha_pago ASC";
$stmt_pagos = $conexion->prepare($sql_pagos);
$stmt_pagos->bind_param("s", $filtro_fecha);
$stmt_pagos->execute();
$result_pagos = $stmt_pagos->get_result();

// --- ¡AQUÍ COMIENZA LA MAGIA DE TCPDF! ---

// 6. Crear una nueva instancia de PDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// 7. Establecer información del documento
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Restaurante El Patio');
$pdf->SetTitle('Reporte de Ventas Pagadas - ' . $filtro_fecha);
$pdf->SetHeaderData(dirname(__FILE__) . '/../img/arbol.png', 30, 'Restaurante El Patio', "Reporte de Ventas Pagadas del " . $filtro_fecha);
$pdf->setFooterData(array(0,64,0), array(0,64,128));
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->AddPage();

// 8. ¡¡HTML CORREGIDO!!
// Ahora muestra la lista de pagos, igual que el reporte por rangos.
$html = '
<h1>Resumen de Ventas Pagadas</h1>
<p>Reporte generado para la fecha: <strong>' . $filtro_fecha . '</strong></p>
<table border="1" cellpadding="5" cellspacing="0" style="width:100%;">
    <thead style="background-color:#f0f0f0; font-weight:bold;">
        <tr>
            <th>ID Pago</th>
            <th>Hora (Local)</th>
            <th>Mesa</th>
            <th>Método</th>
            <th align="right">Total Pagado</th>
        </tr>
    </thead>
    <tbody>';

if ($result_pagos->num_rows > 0) {
    while ($fila = $result_pagos->fetch_assoc()) {
        $html .= '<tr>
                    <td>#' . $fila['id_pago'] . '</td>
                    <td>' . date("H:i A", strtotime($fila['fecha_pagada_colombia'])) . '</td>
                    <td>' . ($fila['numero_mesa'] == 0 ? 'Online' : $fila['numero_mesa']) . '</td>
                    <td>' . htmlspecialchars($fila['metodo_pago']) . '</td>
                    <td align="right">$' . number_format($fila['total_pagado'], 0, ',', '.') . '</td>
                  </tr>';
    }
} else {
    $html .= '<tr><td colspan="5" align="center">No se encontraron pagos para esta fecha.</td></tr>';
}

$html .= '</tbody>
    <tfoot>
        <tr style="background-color:#f0f0f0; font-weight:bold; font-size: 1.2em;">
            <td colspan="4" align="right">TOTAL PAGADO EN EL DÍA:</td>
            <td align="right">$' . number_format($total_dia, 0, ',', '.') . '</td>
        </tr>
    </tfoot>
</table>';

// 9. Escribir el HTML en el PDF
$pdf->writeHTML($html, true, false, true, false, '');

// 10. ¡¡AQUÍ ESTÁ LA CORRECCIÓN!! (Se eliminó la 'G' fantasma)
$pdf->Output('reporte_ventas_pagadas_' . $filtro_fecha . '.pdf', 'I');

// Cerramos conexiones
$stmt_pagos->close();
$conexion->close();
?>