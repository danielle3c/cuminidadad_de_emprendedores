<?php
include 'config.php';

// 1. Cargar configuración visual
$res_conf = mysqli_query($conexion, "SELECT * FROM configuraciones WHERE id = 1");
$cfg = mysqli_fetch_assoc($res_conf);

// 2. Función para contar respuestas rápidamente
function contarRespuesta($columna, $valor, $con) {
    $res = mysqli_query($con, "SELECT COUNT(*) as total FROM encuesta_2026 WHERE $columna = '$valor'");
    $data = mysqli_fetch_assoc($res);
    return $data['total'];
}

// Obtener totales
$preguntas = [
    'conoce_corporacion' => '¿Conoce la Corporación?',
    'contacto_municipalidad' => '¿Contacto con Municipalidad?',
    'interes_iniciativas' => '¿Interés en Iniciativas?',
    'participar_video' => '¿Participar en Video?'
];
?>

<!DOCTYPE html>
<html lang="es" data-theme="<?php echo $cfg['tema_color']; ?>">
<head>
    <meta charset="UTF-8">
    <title>Estadísticas Encuesta 2026</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --bg: #f4f7fe; --card: #ffffff; --text: #2b3674; --primary: #55b83e; --border: #e0e5f2; }
        [data-theme="dark"] { --bg: #0b1437; --card: #111c44; --text: #ffffff; --primary: #2ecc71; --border: #1b254b; }
        body { font-family: 'DM Sans', sans-serif; background: var(--bg); color: var(--text); padding: 30px; }
        .grid-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
        .stat-card { background: var(--card); padding: 20px; border-radius: 20px; border: 1px solid var(--border); text-align: center; }
        .bar-container { background: #eee; border-radius: 10px; height: 25px; margin: 10px 0; overflow: hidden; display: flex; }
        .bar-si { background: #55b83e; height: 100%; color: white; font-size: 0.8rem; display: flex; align-items: center; justify-content: center; }
        .bar-no { background: #ef4444; height: 100%; color: white; font-size: 0.8rem; display: flex; align-items: center; justify-content: center; }
        .legend { display: flex; justify-content: center; gap: 15px; font-size: 0.8rem; margin-top: 5px; }
    </style>
</head>
<body>

    <a href="index.php" style="text-decoration: none; color: var(--text); font-weight: bold;">
        <i class="fas fa-arrow-left"></i> Volver al Panel
    </a>

    <h1 style="margin-top: 20px;">Estadísticas Encuesta 2026</h1>
    <p>Resumen de indicadores basados en las planillas físicas.</p>

    <div class="grid-stats">
        <?php foreach ($preguntas as $columna => $titulo): 
            $si = contarRespuesta($columna, 'SI', $conexion);
            $no = contarRespuesta($columna, 'NO', $conexion);
            $total = $si + $no;
            $porc_si = ($total > 0) ? ($si / $total) * 100 : 0;
            $porc_no = ($total > 0) ? ($no / $total) * 100 : 0;
        ?>
            <div class="stat-card">
                <h4 style="margin-bottom: 15px;"><?php echo $titulo; ?></h4>
                <div style="font-size: 1.5rem; font-weight: 800;"><?php echo $total; ?> <small style="font-size: 0.8rem; color: gray;">respuestas</small></div>
                
                <div class="bar-container">
                    <div class="bar-si" style="width: <?php echo $porc_si; ?>%"><?php echo round($porc_si); ?>%</div>
                    <div class="bar-no" style="width: <?php echo $porc_no; ?>%"><?php echo round($porc_no); ?>%</div>
                </div>

                <div class="legend">
                    <span><i class="fas fa-circle" style="color: #55b83e;"></i> SÍ (<?php echo $si; ?>)</span>
                    <span><i class="fas fa-circle" style="color: #ef4444;"></i> NO (<?php echo $no; ?>)</span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</body>
</html>