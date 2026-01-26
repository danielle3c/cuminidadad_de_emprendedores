<?php 
include 'config.php'; 

// Obtener ID del alumno
$id = mysqli_real_escape_string($conexion, $_GET['id']);

// Consulta para unir las 3 tablas de la escuela
$sql = "SELECT e.*, n.*, o.* FROM emprendedores e
        LEFT JOIN evaluaciones_notas n ON e.id = n.id_emprendedor
        LEFT JOIN opiniones o ON e.id = o.id_emprendedor
        WHERE e.id = '$id'";

$res = mysqli_query($conexion, $sql);
$alumno = mysqli_fetch_assoc($res);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte_Escuela_<?php echo $id; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary: #55b83e; --text: #2b3674; --border: #e0e5f2; }
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7fe; color: var(--text); padding: 30px; }
        
        .report-card { background: white; max-width: 850px; margin: auto; padding: 50px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        
        /* Cabecera del Reporte */
        .header-report { display: flex; justify-content: space-between; border-bottom: 3px solid var(--primary); padding-bottom: 20px; margin-bottom: 30px; }
        .logo-area h2 { margin: 0; color: var(--primary); font-weight: 800; }
        
        /* Cuadrícula de Notas */
        .grid-notas { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin: 30px 0; }
        .nota-box { border: 1px solid var(--border); padding: 15px; border-radius: 12px; text-align: center; }
        .nota-val { font-size: 2rem; font-weight: 800; color: var(--primary); display: block; }
        .nota-lab { font-size: 0.7rem; text-transform: uppercase; color: #a3aed0; }

        /* Opiniones */
        .section-box { margin-bottom: 25px; }
        .section-box h4 { margin-bottom: 10px; border-left: 5px solid var(--primary); padding-left: 10px; }
        .text-area { background: #f8fafc; padding: 20px; border-radius: 12px; font-style: italic; line-height: 1.6; }

        /* Botones (No se imprimen) */
        .no-print { max-width: 850px; margin: 0 auto 20px; display: flex; justify-content: space-between; }
        .btn { padding: 12px 25px; border-radius: 10px; border: none; font-weight: bold; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-print { background: var(--text); color: white; }
        .btn-back { background: #e0e5f2; color: var(--text); }

        /* Configuración de Impresión */
        @media print {
            .no-print { display: none; }
            body { background: white; padding: 0; }
            .report-card { box-shadow: none; width: 100%; max-width: 100%; padding: 20px; }
            .nota-box { border: 1px solid #eee; }
        }
    </style>
</head>
<body>

<div class="no-print">
    <a href="index.php" class="btn btn-back"><i class="fas fa-arrow-left"></i> Volver</a>
    <button onclick="window.print()" class="btn btn-print"><i class="fas fa-file-pdf"></i> IMPRIMIR PDF</button>
</div>

<div class="report-card">
    <div class="header-report">
        <div class="logo-area">
            <h2>ESCUELA DE VERANO</h2>
            <p style="margin:0; font-weight:bold;">Reporte Individual de Evaluación 2026</p>
        </div>
        <div style="text-align: right;">
            <p style="margin:0;">ID Alumno: <strong>#<?php echo $alumno['id']; ?></strong></p>
            <p style="margin:0; color: #a3aed0;">Fecha: <?php echo date('d/m/Y'); ?></p>
        </div>
    </div>

    <div class="info-personal">
        <h1 style="margin: 0; font-size: 2.2rem;"><?php echo htmlspecialchars($alumno['nombre']); ?></h1>
        <p style="font-size: 1.2rem; color: #707eae;"><i class="fas fa-store"></i> <?php echo htmlspecialchars($alumno['emprendimiento']); ?></p>
    </div>

    <div class="grid-notas">
        <div class="nota-box">
            <span class="nota-val"><?php echo $alumno['evaluacion_general']; ?></span>
            <span class="nota-lab">Evaluación General</span>
        </div>
        <div class="nota-box">
            <span class="nota-val"><?php echo $alumno['evaluacion_modulos']; ?></span>
            <span class="nota-lab">Módulos</span>
        </div>
        <div class="nota-box">
            <span class="nota-val"><?php echo $alumno['evaluacion_funcionarios']; ?></span>
            <span class="nota-lab">Funcionarios</span>
        </div>
        <div class="nota-box">
            <span class="nota-val"><?php echo $alumno['evaluacion_espacio']; ?></span>
            <span class="nota-lab">Infraestructura</span>
        </div>
    </div>

    <div class="section-box">
        <h4><i class="far fa-comment-dots"></i> Opinión del Emprendedor</h4>
        <div class="text-area">
            "<?php echo nl2br(htmlspecialchars($alumno['opinion_general'] ?? 'Sin observaciones')); ?>"
        </div>
    </div>

    <div class="section-box">
        <h4><i class="fas fa-tools"></i> Sugerencias de Mejora</h4>
        <div class="text-area">
            <?php echo nl2br(htmlspecialchars($alumno['mejoras'] ?? 'No registra sugerencias')); ?>
        </div>
    </div>

    <div class="section-box">
        <h4><i class="fas fa-graduation-cap"></i> Interés en Futuras Capacitaciones</h4>
        <div style="background: #e0f2fe; padding: 15px; border-radius: 12px; color: #0369a1; font-weight: bold;">
            <?php echo htmlspecialchars($alumno['capacitacion_deseada'] ?? 'No especificado'); ?>
        </div>
    </div>

    <div style="margin-top: 80px; text-align: center; border-top: 1px solid #eee; padding-top: 20px;">
        <div style="display: flex; justify-content: space-around;">
            <div style="width: 200px; border-top: 1px solid #000; padding-top: 5px; font-size: 0.8rem;">Firma Encargado</div>
            <div style="width: 200px; border-top: 1px solid #000; padding-top: 5px; font-size: 0.8rem;">Timbre Recepción</div>
        </div>
    </div>
</div>

</body>
</html>