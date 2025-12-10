<?php
include('../php/config.php');
include('../php/conexion.php');
require_once('../vendor/autoload.php'); 

if (!isset($_SESSION['admin']) || $_SESSION['id_cargo'] != 1) { die("Acceso denegado."); }

// ¡¡AQUÍ LA CORRECCIÓN!!
$sql_ventas_dia = "SELECT 
                       DATE(CONVERT_TZ(fecha_pago, '+00:00', '-05:00')) AS fecha, 
                       SUM(total_pagado) AS total_ventas
                   FROM pagos
                   WHERE CONVERT_TZ(fecha_pago, '+00:00', '-05:00') >= DATE_SUB(CONVERT_TZ(NOW(), '+00:00', '-05:00'), INTERVAL 7 DAY)
                   GROUP BY DATE(CONVERT_TZ(fecha_pago, '+00:00', '-05:00'))
                   ORDER BY fecha ASC";
$res_ventas_dia = $conexion->query($sql_ventas_dia);

// (El resto del código TCPDF es el mismo...)
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Restaurante El Patio');
$pdf->SetTitle('Reporte Ventas Últimos 7 Días');
$pdf->SetHeaderData(dirname(__FILE__) . '/../img/arbol.png', 30, 'Restaurante El Patio', "Reporte de Ventas (Últimos 7 Días)");
$pdf->setFooterData(array(0,64,0), array(0,64,128));
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->AddPage();

$html = '
<h1>Reporte de Ventas (Últimos 7 Días)</h1>
<p>Este reporte muestra el total vendido por día durante la última semana.</p>
<table border="1" cellpadding="5" cellspacing="0" style="width:100%;">
    <thead style="background-color:#f0f0f0; font-weight:bold;">
        <tr>
            <th>Fecha (Local)</th>
            <th align="right">Total Ventas</th>
        </tr>
    </thead>
    <tbody>';

$total_semanal = 0;
if ($res_ventas_dia->num_rows > 0) {
    while ($fila = $res_ventas_dia->fetch_assoc()) {
        $html .= '<tr>
                    <td>' . date("d/m/Y", strtotime($fila['fecha'])) . '</td>
                    <td align="right">$' . number_format($fila['total_ventas'], 0, ',', '.') . '</td>
                  </tr>';
        $total_semanal += $fila['total_ventas'];
    }
} else {
    $html .= '<tr><td colspan="2" align="center">No se encontraron datos.</td></tr>';
}

$html .= '</tbody>
    <tfoot>
        <tr style="background-color:#f0f0f0; font-weight:bold; font-size: 1.2em;">
            <td align="right">TOTAL SEMANAL:</td>
            <td align="right">$' . number_format($total_semanal, 0, ',', '.') . '</td>
        </tr>
    </tfoot>
</table>';

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('reporte_semanal.pdf', 'I');

$conexion->close();
?>