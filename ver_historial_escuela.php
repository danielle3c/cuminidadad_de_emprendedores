<?php 
include 'config.php'; 

// 1. Verificamos que el ID venga en la URL (evita el error de Undefined array key)
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Error: No se proporcionó un ID válido. <a href='index.php'>Volver al buscador</a>");
}

$id = mysqli_real_escape_string($conexion, $_GET['id']);

// 2. Consulta corregida: Solo usamos la tabla 'escuela_verano'
$sql = "SELECT * FROM escuela_verano WHERE id_escuela = '$id'";
$res = mysqli_query($conexion, $sql);
$datos = mysqli_fetch_assoc($res);

// Si el ID no existe en la tabla
if (!$datos) {
    die("Error: No existen registros para el ID #$id en la Escuela de Verano. <a href='index.php'>Volver</a>");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Evaluación - <?php echo $datos['nombre_emprendedor']; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7fe; color: #2b3674; padding: 20px; }
        .container { max-width: 850px; margin: auto; background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 3px solid #f1c40f; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 1.8rem; }
        
        .section-title { background: #f8fafd; padding: 10px 15px; border-left: 5px solid #55b83e; font-weight: bold; margin-top: 30px; margin-bottom: 15px; text-transform: uppercase; font-size: 0.9rem; }
        
        .grid-data { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .data-box { border: 1px solid #e0e5f2; padding: 15px; border-radius: 12px; }
        .data-box label { display: block; font-size: 0.7rem; color: #a3aed0; text-transform: uppercase; font-weight: bold; }
        .data-box span { font-size: 1.1rem; font-weight: 600; }

        .notas-row { display: flex; gap: 15px; margin-top: 10px; }
        .nota-card { flex: 1; background: #2b3674; color: white; padding: 15px; border-radius: 15px; text-align: center; }
        .nota-card big { display: block; font-size: 1.5rem; font-weight: 800; }
        .nota-card small { font-size: 0.6rem; opacity: 0.8; text-transform: uppercase; }

        .text-block { background: #fffdf0; border: 1px dashed #f1c40f; padding: 20px; border-radius: 15px; line-height: 1.5; font-style: italic; }
        
        .btn-print { background: #55b83e; color: white; border: none; padding: 12px 25px; border-radius: 10px; cursor: pointer; font-weight: bold; }
        
        @media print {
            .no-print { display: none; }
            body { background: white; padding: 0; }
            .container { box-shadow: none; border: none; max-width: 100%; }
        }
    </style>
</head>
<body>

<div class="no-print" style="margin-bottom: 20px; text-align: right;">
    <a href="index.php" style="margin-right: 20px; color: #707eae; text-decoration: none;"><i class="fas fa-arrow-left"></i> Volver al buscador</a>
    <button class="btn-print" onclick="window.print()"><i class="fas fa-print"></i> IMPRIMIR PDF</button>
</div>

<div class="container">
    <div class="header">
        <div>
            <h1>Ficha de Evaluación Individual</h1>
            <span>Escuela de Verano para Emprendedores 2026</span>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 1.5rem; font-weight: 800; color: #f1c40f;">ID #<?php echo $datos['id_escuela']; ?></div>
        </div>
    </div>

    <div class="section-title">Información del Participante</div>
    <div class="grid-data">
        <div class="data-box">
            <label>Nombre del Emprendedor</label>
            <span><?php echo htmlspecialchars($datos['nombre_emprendedor']); ?></span>
        </div>
        <div class="data-box">
            <label>Emprendimiento / Negocio</label>
            <span><?php echo htmlspecialchars($datos['nombre_negocio']); ?></span>
        </div>
    </div>

    <div class="section-title">Calificaciones Obtenidas</div>
    <div class="notas-row">
        <div class="nota-card"><big><?php echo $datos['nota_general']; ?></big><small>Nota General</small></div>
        <div class="nota-card"><big><?php echo $datos['nota_modulos']; ?></big><small>Módulos</small></div>
        <div class="nota-card"><big><?php echo $datos['nota_funcionarios']; ?></big><small>Atención</small></div>
        <div class="nota-card"><big><?php echo $datos['nota_espacio']; ?></big><small>Espacio/Lugar</small></div>
    </div>

    <div class="section-title">Opinión sobre la actividad</div>
    <div class="text-block">
        "<?php echo nl2br(htmlspecialchars($datos['opinion_texto'])); ?>"
    </div>

    <div class="section-title">Sugerencias de mejora</div>
    <p><?php echo nl2br(htmlspecialchars($datos['mejoras_texto'])); ?></p>

    <div class="section-title">Interés en futuras capacitaciones</div>
    <div class="data-box" style="background: #f0fff4; border-color: #55b83e;">
        <span><?php echo htmlspecialchars($datos['capacitacion_interes']); ?></span>
    </div>

    <div class="section-title">Críticas o comentarios adicionales</div>
    <p style="color: #4a5568; font-size: 0.9rem;"><?php echo nl2br(htmlspecialchars($datos['critica_adicional'])); ?></p>

    <div style="margin-top: 50px; text-align: center; border-top: 1px solid #eee; padding-top: 20px; font-size: 0.8rem; color: #a3aed0;">
        Corporación Municipal de La Granja - Departamento de Fomento Productivo
    </div>
</div>

</body>
</html>