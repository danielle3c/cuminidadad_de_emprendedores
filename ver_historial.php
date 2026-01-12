<?php
include 'config.php';
$nombre = mysqli_real_escape_string($conexion, $_GET['nombre']);
$res_conf = mysqli_query($conexion, "SELECT * FROM configuraciones WHERE id = 1");
$cfg = mysqli_fetch_assoc($res_conf);

// Consulta para unir ambas tablas en un historial cronológico
$sql = "SELECT 'asistencia' as tipo, nombre_carrito as titulo, asistencia as detalle, created_at as fecha 
        FROM carritos WHERE nombre_responsable LIKE '%$nombre%'
        UNION
        SELECT 'credito' as tipo, 'Crédito Otorgado' as titulo, CONCAT('Monto: $', monto_inicial) as detalle, created_at as fecha 
        FROM creditos cr 
        JOIN emprendedores e ON cr.emprendedores_idemprendedores = e.idemprendedores
        JOIN personas p ON e.personas_idpersonas = p.idpersonas
        WHERE CONCAT(p.nombres, ' ', p.apellidos) LIKE '%$nombre%'
        ORDER BY fecha DESC";
$res = mysqli_query($conexion, $sql);
?>
<!DOCTYPE html>
<html lang="es" data-theme="<?php echo $cfg['tema_color']; ?>">
<head>
    <meta charset="UTF-8">
    <title>Historial - <?php echo $nombre; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .timeline { max-width: 800px; margin: 20px auto; position: relative; padding-left: 50px; }
        .timeline::before { content: ''; position: absolute; left: 20px; top: 0; bottom: 0; width: 2px; background: #cbd5e1; }
        .t-item { background: white; padding: 15px; border-radius: 12px; margin-bottom: 20px; border: 1px solid #e2e8f0; position: relative; }
        .t-dot { position: absolute; left: -40px; top: 15px; width: 20px; height: 20px; border-radius: 50%; border: 4px solid var(--bg); }
    </style>
</head>
<body style="background:#f1f5f9; font-family: sans-serif; padding:20px;">
    <a href="carritos.php" style="text-decoration:none; color:var(--primary); font-weight:bold;"><i class="fas fa-arrow-left"></i> Volver</a>
    <div class="timeline">
        <h2>Actividad de <?php echo htmlspecialchars($nombre); ?></h2>
        <?php while($row = mysqli_fetch_assoc($res)): ?>
            <div class="t-item">
                <div class="t-dot" style="background: <?php echo ($row['tipo'] == 'asistencia') ? '#43b02a' : '#3498db'; ?>;"></div>
                <small style="color:#64748b; font-weight:bold;"><?php echo date('d M, Y - H:i', strtotime($row['fecha'])); ?></small>
                <h4 style="margin:5px 0;"><?php echo $row['titulo']; ?></h4>
                <p style="margin:0; font-size:0.9rem; opacity:0.8;"><?php echo $row['detalle']; ?></p>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>