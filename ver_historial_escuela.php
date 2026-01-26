<?php
include 'config.php';

$id = mysqli_real_escape_string($conexion, $_GET['id']);

$sql = "SELECT e.nombre, e.emprendimiento, n.*, o.* FROM emprendedores e
        JOIN evaluaciones_notas n ON e.id_emprendedor = n.id_emprendedor
        JOIN opiniones o ON e.id_emprendedor = o.id_emprendedor
        WHERE e.id_emprendedor = '$id'";

$res = mysqli_query($conexion, $sql);
$d = mysqli_fetch_assoc($res);

if (!$d) die("No hay datos para este ID.");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte - <?php echo $d['nombre']; ?></title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; padding: 40px; background: #f0f2f5; }
        .card { background: white; padding: 40px; border-radius: 15px; max-width: 800px; margin: auto; }
        .header { border-bottom: 3px solid #55b83e; margin-bottom: 20px; padding-bottom: 10px; }
        .stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin: 20px 0; }
        .stat-item { background: #111c44; color: white; padding: 15px; border-radius: 10px; text-align: center; }
        .text-block { background: #f8f9fa; padding: 15px; border-radius: 10px; margin-bottom: 15px; border-left: 5px solid #f1c40f; }
        h4 { margin-bottom: 5px; color: #55b83e; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>

<div class="card">
    <div class="header">
        <h1 style="margin:0;">Escuela de Verano 2026</h1>
        <p>Reporte de Evaluación Individual: <strong><?php echo $d['nombre']; ?></strong></p>
    </div>

    <h4>Información del Negocio</h4>
    <p><?php echo $d['emprendimiento']; ?></p>

    <h4>Resultados Cuantitativos (Notas)</h4>
    <div class="stat-grid">
        <div class="stat-item"><small>General</small><br><strong><?php echo $d['evaluacion_general']; ?></strong></div>
        <div class="stat-item"><small>Módulos</small><br><strong><?php echo $d['evaluacion_modulos']; ?></strong></div>
        <div class="stat-item"><small>Funcionarios</small><br><strong><?php echo $d['evaluacion_funcionarios']; ?></strong></div>
        <div class="stat-item"><small>Espacio</small><br><strong><?php echo $d['evaluacion_espacio']; ?></strong></div>
    </div>

    <h4>Opinión de la Actividad</h4>
    <div class="text-block">"<?php echo $d['opinion_general']; ?>"</div>

    <h4>Mejoras Sugeridas</h4>
    <div class="text-block"><?php echo $d['mejoras']; ?></div>

    <h4>Interés en Capacitaciones</h4>
    <p><strong><?php echo $d['capacitacion_deseada']; ?></strong></p>

    <?php if(!empty($d['critica_adicional'])): ?>
        <h4>Comentarios Adicionales</h4>
        <p style="color: #666; font-style: italic;"><?php echo $d['critica_adicional']; ?></p>
    <?php endif; ?>

    <button onclick="window.print()" class="no-print" style="margin-top:20px; padding:10px 20px; background:#55b83e; color:white; border:none; border-radius:5px; cursor:pointer;">Imprimir Reporte</button>
</div>
</body>
</html>