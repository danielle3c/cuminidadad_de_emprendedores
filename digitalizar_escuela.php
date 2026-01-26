<?php 
include 'config.php'; 

// 1. Validar que llegue el ID para evitar el "Undefined array key"
if (!isset($_GET['id'])) {
    die("Error: ID no especificado. <a href='index.php'>Volver al buscador</a>");
}

$id = mysqli_real_escape_string($conexion, $_GET['id']);

// 2. Consulta corregida a la tabla 'escuela_verano'
$sql = "SELECT * FROM escuela_verano WHERE id_escuela = '$id'";
$res = mysqli_query($conexion, $sql);
$datos = mysqli_fetch_assoc($res);

if (!$datos) {
    die("Error: No se encontraron datos para este participante.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Individual - Escuela de Verano</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f7fe; color: #2b3674; padding: 40px; }
        .report-card { background: white; max-width: 800px; margin: auto; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .header { border-bottom: 2px solid #f1c40f; padding-bottom: 20px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; }
        .badge { background: #f1c40f; color: #000; padding: 5px 15px; border-radius: 50px; font-weight: bold; font-size: 0.8rem; }
        
        .section-title { font-size: 1.1rem; font-weight: 800; margin-top: 25px; margin-bottom: 15px; color: #55b83e; text-transform: uppercase; border-left: 4px solid #55b83e; padding-left: 10px; }
        
        .grid-info { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .info-box { background: #f8fafd; padding: 15px; border-radius: 12px; }
        .info-box label { display: block; font-size: 0.75rem; color: #a3aed0; font-weight: bold; }
        .info-box span { font-size: 1rem; font-weight: 600; }

        .notas-container { display: flex; gap: 10px; margin-top: 10px; }
        .nota-circle { background: #2b3674; color: white; width: 50px; height: 50px; border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; }
        .nota-circle small { font-size: 0.5rem; }

        .comentario-box { background: #fffdf0; border: 1px solid #f1c40f; padding: 20px; border-radius: 15px; margin-top: 10px; line-height: 1.6; }
        
        @media print {
            .no-print { display: none; }
            body { padding: 0; background: white; }
            .report-card { box-shadow: none; border: none; }
        }
    </style>
</head>
<body>

<div class="no-print" style="max-width: 800px; margin: 0 auto 20px; display: flex; gap: 10px;">
    <a href="index.php" style="text-decoration:none; color: #707eae;"><i class="fas fa-arrow-left"></i> Volver</a>
    <button onclick="window.print()" style="margin-left:auto; background:#55b83e; color:white; border:none; padding:10px 20px; border-radius:10px; cursor:pointer;">
        <i class="fas fa-print"></i> Imprimir Reporte
    </button>
</div>

<div class="report-card">
    <div class="header">
        <div>
            <h1 style="margin:0;">Reporte de Evaluación</h1>
            <p style="margin:0; color:#707eae;">Escuela de Verano 2026</p>
        </div>
        <span class="badge">PARTICIPANTE #<?php echo $datos['id_escuela']; ?></span>
    </div>

    <div class="section-title">Datos del Emprendedor</div>
    <div class="grid-info">
        <div class="info-box">
            <label>Nombre Completo</label>
            <span><?php echo htmlspecialchars($datos['nombre_emprendedor']); ?></span>
        </div>
        <div class="info-box">
            <label>Negocio / Rubro</label>
            <span><?php echo htmlspecialchars($datos['nombre_negocio']); ?></span>
        </div>
    </div>

    <div class="section-title">Calificaciones (Escala 1 a 5)</div>
    <div class="notas-container">
        <div class="nota-circle"><span><?php echo $datos['nota_general']; ?></span><small>Gral.</small></div>
        <div class="nota-circle"><span><?php echo $datos['nota_modulos']; ?></span><small>Módulos</small></div>
        <div class="nota-circle"><span><?php echo $datos['nota_funcionarios']; ?></span><small>Equipo</small></div>
        <div class="nota-circle"><span><?php echo $datos['nota_espacio']; ?></span><small>Espacio</small></div>
    </div>

    <div class="section-title">Opinión General</div>
    <div class="comentario-box italic">
        "<?php echo nl2br(htmlspecialchars($datos['opinion_texto'])); ?>"
    </div>

    <div class="section-title">Mejoras Sugeridas</div>
    <div class="comentario-box">
        <?php echo nl2br(htmlspecialchars($datos['mejoras_texto'])); ?>
    </div>

    <div class="section-title">Interés en Capacitaciones</div>
    <div class="info-box" style="border-left: 5px solid #f1c40f;">
        <span><?php echo htmlspecialchars($datos['capacitacion_interes']); ?></span>
    </div>

    <div class="section-title">Críticas o Comentarios Adicionales</div>
    <p style="font-size: 0.9rem; color: #4a5568;">
        <?php echo nl2br(htmlspecialchars($datos['critica_adicional'])); ?>
    </p>

    <div style="margin-top: 50px; text-align: center; border-top: 1px solid #eee; padding-top: 20px;">
        <small style="color: #a3aed0;">Documento generado por el Sistema de Auditoría - Comunidad de Emprendedores</small>
    </div>
</div>

</body>
</html>