<?php
include('../php/config.php');
include('../php/conexion.php');
require_once('../vendor/autoload.php'); 

if (!isset($_SESSION['admin']) || $_SESSION['id_cargo'] != 1) { die("Acceso denegado."); }

$fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
$fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');

// ¡¡AQUÍ LA CORRECCIÓN!!
// Forzamos la conversión de zona horaria en el WHERE
$sql_total = "SELECT SUM(total_pagado) AS total_rango FROM pagos 
              WHERE DATE(CONVERT_TZ(fecha_pago, '+00:00', '-05:00')) BETWEEN ? AND ?";
$stmt_total = $conexion->prepare($sql_total);
$stmt_total->bind_param("ss", $fecha_inicio, $fecha_fin);
$stmt_total->execute();
$total_rango = $stmt_total->get_result()->fetch_assoc()['total_rango'] ?? 0;

// ¡¡Y AQUÍ!!
$sql_pagos = "SELECT p.id_pago, p.fecha_pago, p.metodo_pago, p.total_pagado, c.numero_mesa,
              CONVERT_TZ(p.fecha_pago, '+00:00', '-05:00') AS fecha_pagada_colombia
              FROM pagos p
              JOIN comandas c ON p.id_comanda = c.id_comanda
              WHERE DATE(CONVERT_TZ(p.fecha_pago, '+00:00', '-05:00')) BETWEEN ? AND ?
              ORDER BY p.fecha_pago ASC";
$stmt_pagos = $conexion->prepare($sql_pagos);
$stmt_pagos->bind_param("ss", $fecha_inicio, $fecha_fin);
$stmt_pagos->execute();
$result_pagos = $stmt_pagos->get_result();

// (El resto del código TCPDF es el mismo...)
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Restaurante El Patio');
$pdf->SetTitle('Reporte de Ventas por Rango');
$pdf->SetHeaderData(dirname(__FILE__) . '/../img/arbol.png', 30, 'Restaurante El Patio', "Reporte de Ventas por Rango\nDel $fecha_inicio al $fecha_fin");
$pdf->setFooterData(array(0,64,0), array(0,64,128));
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->AddPage();

$html = '
<h1>Resumen de Ventas por Rango</h1>
<p>Mostrando resultados desde <strong>' . $fecha_inicio . '</strong> hasta <strong>' . $fecha_fin . '</strong></p>
<table border="1" cellpadding="5" cellspacing="0" style="width:100%;">
    <thead style="background-color:#f0f0f0; font-weight:bold;">
        <tr>
            <th>ID Pago</th>
            <th>Fecha y Hora (Local)</th>
            <th>Mesa</th>
            <th>Método</th>
            <th align="right">Total</th>
        </tr>
    </thead>
    <tbody>';

if ($result_pagos->num_rows > 0) {
    while ($fila = $result_pagos->fetch_assoc()) {
        $html .= '<tr>
                    <td>#' . $fila['id_pago'] . '</td>
                    <td>' . date("d/m/Y H:i A", strtotime($fila['fecha_pagada_colombia'])) . '</td>
                    <td>' . ($fila['numero_mesa'] == 0 ? 'Online' : $fila['numero_mesa']) . '</td>
                    <td>' . htmlspecialchars($fila['metodo_pago']) . '</td>
                    <td align="right">$' . number_format($fila['total_pagado'], 0, ',', '.') . '</td>
                  </tr>';
    }
} else {
    $html .= '<tr><td colspan="5" align="center">No se encontraron pagos para este rango de fechas.</td></tr>';
}

$html .= '</tbody>
    <tfoot>
        <tr style="background-color:#f0f0f0; font-weight:bold; font-size: 1.2em;">
            <td colspan="4" align="right">TOTAL DEL PERIODO:</td>
            <td align="right">$' . number_format($total_rango, 0, ',', '.') . '</td>
        </tr>
    </tfoot>
</table>';

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('reporte_rango_' . $fecha_inicio . '_a_' . $fecha_fin . '.pdf', 'I');

$stmt_total->close();
$stmt_pagos->close();
$conexion->close();
?>