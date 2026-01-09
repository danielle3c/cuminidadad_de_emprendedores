<?php
include 'config.php';

$nombre = $_GET['nombre'] ?? '';
$id_ref = $_GET['id'] ?? 0;
$origen = $_GET['origen'] ?? '';

// 1. Configuración de colores
$res_conf = mysqli_query($conexion, "SELECT * FROM configuraciones WHERE id = 1");
$cfg = mysqli_fetch_assoc($res_conf);
?>

<!DOCTYPE html>
<html lang="es" data-theme="<?php echo $cfg['tema_color']; ?>">
<head>
    <meta charset="UTF-8">
    <title>Historial - <?php echo $nombre; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --bg: #f1f5f9; --text: #1e293b; --primary: #43b02a; --card: #ffffff; --border: #e2e8f0; }
        [data-theme="dark"] { --bg: #0f172a; --text: #f1f5f9; --primary: #2ecc71; --card: #1e293b; --border: #334155; }
        
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); padding: 20px; }
        .header { max-width: 800px; margin: 0 auto 30px; display: flex; align-items: center; gap: 20px; }
        .btn-back { text-decoration: none; color: var(--text); font-size: 1.5rem; }

        /* Estilo del Timeline */
        .timeline { max-width: 800px; margin: auto; position: relative; }
        .timeline::before { content: ''; position: absolute; left: 31px; top: 0; bottom: 0; width: 2px; background: var(--border); }
        
        .timeline-item { position: relative; margin-bottom: 30px; padding-left: 80px; animation: fadeIn 0.5s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateX(-10px); } to { opacity: 1; transform: translateX(0); } }

        .timeline-icon { 
            position: absolute; left: 15px; top: 0; width: 35px; height: 35px; 
            border-radius: 50%; display: flex; align-items: center; justify-content: center; 
            color: white; font-size: 0.9rem; z-index: 2; border: 4px solid var(--bg);
        }
        .icon-carrito { background: #3b82f6; } /* Azul */
        .icon-credito { background: #f59e0b; } /* Naranja */

        .timeline-content { background: var(--card); padding: 20px; border-radius: 15px; border: 1px solid var(--border); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .timeline-date { font-size: 0.8rem; opacity: 0.6; font-weight: 700; display: block; margin-bottom: 5px; }
        .timeline-title { font-size: 1.1rem; font-weight: 800; margin: 0; }
        .timeline-desc { font-size: 0.9rem; margin-top: 8px; opacity: 0.8; }
        
        .badge-info { padding: 3px 8px; border-radius: 5px; background: var(--bg); font-size: 0.75rem; font-weight: 700; }
    </style>
</head>
<body>

<div class="header">
    <a href="index.php" class="btn-back"><i class="fas fa-arrow-left"></i></a>
    <div>
        <h1 style="margin:0;"><?php echo $nombre; ?></h1>
        <p style="margin:0; opacity:0.6;">Hoja de vida y trayectoria en el sistema</p>
    </div>
</div>

<div class="timeline">
    <?php
    $nom_sql = mysqli_real_escape_string($conexion, $nombre);
    
    // CONSULTA UNIFICADA DE EVENTOS
    // Combinamos Carritos (asistencias) y Créditos en una sola lista cronológica
    $sql = "SELECT 'asistencia' as tipo, nombre_carrito as titulo, asistencia as detalle, created_at as fecha 
            FROM carritos WHERE nombre_responsable LIKE '%$nom_sql%'
            UNION
            SELECT 'credito' as tipo, 'Solicitud de Crédito' as titulo, CONCAT('Monto: $', monto) as detalle, fecha_registro as fecha 
            FROM creditos WHERE personas_idpersonas = $id_ref
            ORDER BY fecha DESC";

    $res = mysqli_query($conexion, $sql);

    if(mysqli_num_rows($res) > 0):
        while($ev = mysqli_fetch_assoc($res)):
            $es_carrito = ($ev['tipo'] == 'asistencia');
    ?>
        <div class="timeline-item">
            <div class="timeline-icon <?php echo $es_carrito ? 'icon-carrito' : 'icon-credito'; ?>">
                <i class="fas <?php echo $es_carrito ? 'fa-shopping-cart' : 'fa-hand-holding-usd'; ?>"></i>
            </div>
            <div class="timeline-content">
                <span class="timeline-date"><?php echo date('d \d\e M, Y - H:i', strtotime($ev['fecha'])); ?></span>
                <h3 class="timeline-title"><?php echo $ev['titulo']; ?></h3>
                <p class="timeline-desc">
                    <span class="badge-info"><?php echo $ev['detalle']; ?></span>
                </p>
            </div>
        </div>
    <?php 
        endwhile;
    else:
        echo "<p style='text-align:center; opacity:0.5;'>No hay eventos registrados para este perfil.</p>";
    endif; 
    ?>
</div>

</body>
</html>