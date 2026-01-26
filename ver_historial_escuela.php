<?php 
include 'config.php'; 

// 1. Validar la conexión y el ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Si no hay ID, redirigir al buscador para evitar el error de pantalla blanca
    header("Location: index.php");
    exit();
}

$id = mysqli_real_escape_string($conexion, $_GET['id']);

// 2. Consulta a la tabla correcta (escuela_verano)
$sql = "SELECT * FROM escuela_verano WHERE id_escuela = '$id'";
$res = mysqli_query($conexion, $sql);

// 3. Verificar si MySQL encontró el registro
if (mysqli_num_rows($res) == 0) {
    die("<div style='text-align:center; padding:50px; font-family:sans-serif;'>
            <h2>⚠️ Error de Registro</h2>
            <p>El ID #$id no existe en la base de datos de la Escuela de Verano.</p>
            <a href='index.php'>Volver al Buscador</a>
         </div>");
}

$datos = mysqli_fetch_assoc($res);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte PDF - <?php echo htmlspecialchars($datos['nombre_emprendedor']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'DM Sans', sans-serif; background: #f4f7fe; padding: 30px; color: #2b3674; }
        .report-container { max-width: 800px; margin: auto; background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #f1c40f; padding-bottom: 20px; }
        .section { margin-top: 30px; }
        .section-title { font-weight: 800; text-transform: uppercase; font-size: 0.8rem; color: #55b83e; border-left: 4px solid #55b83e; padding-left: 10px; margin-bottom: 15px; }
        .info-card { background: #f8fafd; padding: 20px; border-radius: 15px; }
        .nota-box { display: inline-block; background: #2b3674; color: white; padding: 15px; border-radius: 12px; text-align: center; margin-right: 10px; min-width: 60px; }
        .btn-print { background: #55b83e; color: white; border: none; padding: 12px 25px; border-radius: 10px; cursor: pointer; font-weight: bold; margin-bottom: 20px; }
        
        @media print { .no-print { display: none; } body { padding: 0; background: white; } .report-container { box-shadow: none; border: none; } }
    </style>
</head>
<body>

<div class="no-print" style="max-width: 800px; margin: auto; display: flex; justify-content: space-between;">
    <a href="index.php" style="text-decoration:none; color: #a3aed0;"><i class="fas fa-arrow-left"></i> Volver al Buscador</a>
    <button class="btn-print" onclick="window.print()"><i class="fas fa-print"></i> IMPRIMIR REPORTE</button>
</div>

<div class="report-container">
    <div class="header">
        <div>
            <h1 style="margin:0;">Ficha de Evaluación</h1>
            <p style="margin:0; color:#707eae;">Escuela de Verano 2026</p>
        </div>
        <div style="text-align: right;">
            <span style="font-weight: 800; color: #f1c40f; font-size: 1.5rem;">ID #<?php echo $datos['id_escuela']; ?></span>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Datos Generales</div>
        <div class="info-card">
            <p><strong>Emprendedor:</strong> <?php echo htmlspecialchars($datos['nombre_emprendedor']); ?></p>
            <p><strong>Negocio:</strong> <?php echo htmlspecialchars($datos['nombre_negocio']); ?></p>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Evaluación Cuantitativa</div>
        <div class="nota-box"><small>GENERAL</small><br><big><strong><?php echo $datos['nota_general']; ?></strong></big></div>
        <div class="nota-box"><small>MÓDULOS</small><br><big><strong><?php echo $datos['nota_modulos']; ?></strong></big></div>
        <div class="nota-box"><small>EQUIPO</small><br><big><strong><?php echo $datos['nota_funcionarios']; ?></strong></big></div>
        <div class="nota-box"><small>ESPACIO</small><br><big><strong><?php echo $datos['nota_espacio']; ?></strong></big></div>
    </div>

    <div class="section">
        <div class="section-title">Comentarios y Sugerencias</div>
        <p><strong>Opinión General:</strong><br> <em>"<?php echo nl2br(htmlspecialchars($datos['opinion_texto'])); ?>"</em></p>
        <p><strong>Mejoras sugeridas:</strong><br> <?php echo nl2br(htmlspecialchars($datos['mejoras_texto'])); ?></p>
        <p><strong>Interés en capacitaciones:</strong><br> <span style="color:#55b83e; font-weight:bold;"><?php echo htmlspecialchars($datos['capacitacion_interes']); ?></span></p>
    </div>

    <div style="margin-top: 50px; text-align: center; font-size: 0.7rem; color: #a3aed0; border-top: 1px solid #eee; padding-top: 20px;">
        Documento oficial generado por Comunidad de Emprendedores - Municipalidad de La Granja
    </div>
</div>

</body>
</html>