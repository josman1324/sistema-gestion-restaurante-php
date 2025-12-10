<?php
include('../php/config.php');
include('../php/conexion.php');
require_once('../vendor/autoload.php'); // Carga la biblioteca PDF

// Protección de Admin
if (!isset($_SESSION['admin']) || $_SESSION['id_cargo'] != 1) { die("Acceso denegado."); }

// 1. Consulta a la BD (la que ya tenías)
$sql_top_menus = "SELECT m.nombre_menu, SUM(cd.cantidad) AS total_vendido, SUM(cd.subtotal) AS ingreso_total
                  FROM comanda_detalle cd
                  JOIN menus m ON cd.id_menu = m.id_menu
                  GROUP BY m.id_menu
                  ORDER BY total_vendido DESC
                  LIMIT 10"; // Aumentado a 10 para el reporte
$res_top_menus = $conexion->query($sql_top_menus);

// 2. Crear el PDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Info del documento
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Restaurante El Patio');
$pdf->SetTitle('Reporte Top Menús Vendidos');
$pdf->SetHeaderData(dirname(__FILE__) . '/../img/arbol.png', 30, 'Restaurante El Patio', "Reporte de Menús Más Vendidos (Histórico)");
$pdf->setFooterData(array(0,64,0), array(0,64,128));
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->AddPage();

// 3. Crear el HTML para el PDF
$html = '
<h1>Reporte de Menús Más Vendidos (Top 10)</h1>
<p>Este reporte muestra el total histórico de unidades vendidas por cada plato.</p>
<table border="1" cellpadding="5" cellspacing="0" style="width:100%;">
    <thead style="background-color:#f0f0f0; font-weight:bold;">
        <tr>
            <th>#</th>
            <th>Nombre del Menú</th>
            <th align="center">Unidades Vendidas</th>
            <th align="right">Ingreso Generado</th>
        </tr>
    </thead>
    <tbody>';

if ($res_top_menus->num_rows > 0) {
    $i = 1;
    while ($fila = $res_top_menus->fetch_assoc()) {
        $html .= '<tr>
                    <td align="center">' . $i++ . '</td>
                    <td>' . htmlspecialchars($fila['nombre_menu']) . '</td>
                    <td align="center">' . $fila['total_vendido'] . '</td>
                    <td align="right">$' . number_format($fila['ingreso_total'], 0, ',', '.') . '</td>
                  </tr>';
    }
} else {
    $html .= '<tr><td colspan="4" align="center">No se encontraron datos.</td></tr>';
}

$html .= '</tbody></table>';

// 4. Escribir y enviar el PDF
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('reporte_top_menus.pdf', 'I');

$conexion->close();
?>