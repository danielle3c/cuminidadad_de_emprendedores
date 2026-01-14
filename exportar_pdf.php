<?php
include 'config.php';
// Si usas Composer: require 'vendor/autoload.php'; 
// Si no, descarga la librería e inclúyela:
// require_once 'dompdf/autoload.inc.php'; 

use Dompdf\Dompdf;

$buscar = isset($_GET['buscar']) ? mysqli_real_escape_string($conexion, $_GET['buscar']) : "";
$where = $buscar != "" ? "WHERE nombre_responsable LIKE '%$buscar%' OR nombre_carrito LIKE '%$buscar%'" : "";

$sql = "SELECT * FROM carritos $where ORDER BY created_at DESC";
$res = mysqli_query($conexion, $sql);

// Generar el HTML para el PDF
$html = '
<h2 style="text-align:center">Reporte de Carritos</h2>
<table width="100%" border="1" cellpadding="5" style="border-collapse: collapse; font-family: sans-serif; font-size: 12px;">
    <thead>
        <tr style="background-color: #f2f2f2;">
            <th>Fecha/Hora</th>
            <th>Responsable</th>
            <th>Carrito</th>
            <th>Asistencia</th>
        </tr>
    </thead>
    <tbody>';

while($row = mysqli_fetch_assoc($res)) {
    $fecha = date('d/m/Y H:i', strtotime($row['created_at']));
    $html .= '<tr>
        <td>'. $fecha .'</td>
        <td>'. $row['nombre_responsable'] .'</td>
        <td>'. $row['nombre_carrito'] .'</td>
        <td>'. $row['asistencia'] .'</td>
    </tr>';
}

$html .= '</tbody></table>';

// Inicializar Dompdf
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("Reporte_Carritos.pdf", array("Attachment" => false));
?>