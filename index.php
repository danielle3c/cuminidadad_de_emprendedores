<?php 
include 'config.php'; 

$res_conf = mysqli_query($conexion, "SELECT * FROM configuraciones WHERE id = 1");
$cfg = mysqli_fetch_assoc($res_conf);
?>

<!DOCTYPE html>
<html lang="es" data-theme="<?php echo $cfg['tema_color']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscador Inteligente - <?php echo $cfg['nombre_sistema']; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { 
            --bg: #f8fafc; --card: #ffffff; --text: #1e293b; --primary: #43b02a; 
            --primary-soft: #dcfce7; --border: #e2e8f0; --accent: #0f172a;
        }
        [data-theme="dark"] { 
            --bg: #0f172a; --card: #1e293b; --text: #f1f5f9; --primary: #2ecc71; 
            --primary-soft: rgba(46, 204, 113, 0.1); --border: #334155; 
        }

        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); margin: 0; }
        .nav-bar { background: var(--card); padding: 1rem 2rem; display: flex; justify-content: space-between; border-bottom: 1px solid var(--border); }
        .nav-logo { font-weight: 800; color: var(--primary); text-decoration: none; }
        .container { max-width: 1100px; margin: 40px auto; padding: 0 20px; }

        /* Buscador */
        .hero-search { text-align: center; margin-bottom: 40px; }
        .search-wrapper { position: relative; max-width: 600px; margin: auto; }
        .search-wrapper input { 
            width: 100%; padding: 18px 25px; border: 2px solid var(--border); border-radius: 50px; 
            background: var(--card); color: var(--text); font-size: 1.1rem; box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            box-sizing: border-box; outline: none;
        }

        /* Tarjeta Maestra Mejorada */
        .master-card { background: var(--card); border-radius: 24px; margin-bottom: 30px; border: 1px solid var(--border); overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        
        .card-main-info { padding: 25px; display: flex; align-items: center; gap: 20px; }
        .profile-pic { width: 70px; height: 70px; border-radius: 18px; object-fit: cover; }

        /* Grid de Estadísticas Históricas */
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); background: var(--bg); gap: 1px; border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); }
        .stat-item { background: var(--card); padding: 20px; text-align: center; }
        .stat-item i { color: var(--primary); margin-bottom: 8px; font-size: 1.2rem; }
        .stat-value { display: block; font-size: 1.3rem; font-weight: 800; }
        .stat-label { font-size: 0.75rem; text-transform: uppercase; opacity: 0.6; letter-spacing: 0.5px; }

        /* Botón de Historial */
        .card-actions { padding: 15px; background: var(--card); display: flex; justify-content: center; }
        .btn-historial { 
            background: var(--accent); color: white; padding: 12px 30px; border-radius: 12px; 
            text-decoration: none; font-weight: 700; font-size: 0.9rem; transition: 0.3s;
            display: flex; align-items: center; gap: 10px;
        }
        .btn-historial:hover { transform: scale(1.05); background: var(--primary); }

        .badge { padding: 4px 10px; border-radius: 6px; font-size: 0.65rem; font-weight: 800; }
        .badge-warning { background: #fef9c3; color: #854d0e; }
        .badge-success { background: var(--primary-soft); color: var(--primary); }
    </style>
</head>
<body>

<div class="container">
    <div class="hero-search">
        <h1>Centro de Control</h1>
        <form method="GET" class="search-wrapper">
            <input type="text" name="buscar" placeholder="Escribe nombre o RUT..." value="<?php echo $_GET['buscar'] ?? ''; ?>" autofocus autocomplete="off">
        </form>
    </div>

    <?php if (isset($_GET['buscar']) && !empty(trim($_GET['buscar']))): 
        $busqueda = mysqli_real_escape_string($conexion, $_GET['buscar']);
        
        $sql = "SELECT idpersonas as id, nombres, apellidos, rut, 'persona' as origen FROM personas 
                WHERE nombres LIKE '%$busqueda%' OR apellidos LIKE '%$busqueda%' OR rut LIKE '%$busqueda%'
                UNION
                SELECT id as id, nombre_responsable as nombres, '' as apellidos, '' as rut, 'carrito' as origen FROM carritos 
                WHERE nombre_responsable LIKE '%$busqueda%' GROUP BY nombre_responsable";
        
        $res = mysqli_query($conexion, $sql);

        while ($p = mysqli_fetch_assoc($res)): 
            $nombre_query = mysqli_real_escape_string($conexion, $p['nombres']);
            
            // 1. Contar asistencias totales (Vez que vino al carrito)
            $q_asistencias = mysqli_query($conexion, "SELECT COUNT(*) as total FROM carritos WHERE nombre_responsable LIKE '%$nombre_query%' AND asistencia = 'SÍ VINO'");
            $total_asistencias = mysqli_fetch_assoc($q_asistencias)['total'];

            // 2. Contar créditos totales (Si es persona registrada)
            $total_creditos = 0;
            if($p['origen'] == 'persona') {
                $id_p = $p['id'];
                $q_cred = mysqli_query($conexion, "SELECT COUNT(*) as total FROM creditos WHERE personas_idpersonas = $id_p");
                $total_creditos = mysqli_fetch_assoc($q_cred)['total'];
            }
        ?>
            <div class="master-card">
                <div class="card-main-info">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($p['nombres']); ?>&background=random" class="profile-pic">
                    <div>
                        <span class="badge <?php echo $p['origen'] == 'persona' ? 'badge-success' : 'badge-warning'; ?>">
                            <?php echo $p['origen'] == 'persona' ? 'CLIENTE VERIFICADO' : 'REGISTRO CARRITO'; ?>
                        </span>
                        <h2 style="margin:5px 0;"><?php echo $p['nombres']." ".$p['apellidos']; ?></h2>
                        <small style="opacity:0.6">ID: <?php echo !empty($p['rut']) ? $p['rut'] : 'Sin Identificación'; ?></small>
                    </div>
                </div>

                <div class="stats-grid">
                    <div class="stat-item">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="stat-value"><?php echo $total_asistencias; ?></span>
                        <span class="stat-label">Visitas</span>
                    </div>
                    <div class="stat-item">
                        <i class="fas fa-hand-holding-usd"></i>
                        <span class="stat-value"><?php echo $total_creditos; ?></span>
                        <span class="stat-label">Créditos</span>
                    </div>
                    <div class="stat-item">
                        <i class="fas fa-star"></i>
                        <span class="stat-value"><?php echo ($total_asistencias > 5) ? 'VIP' : 'Normal'; ?></span>
                        <span class="stat-label">Nivel</span>
                    </div>
                </div>

                <div class="card-actions">
                    <a href="ver_historial.php?nombre=<?php echo urlencode($p['nombres']); ?>&id=<?php echo $p['id']; ?>&origen=<?php echo $p['origen']; ?>" class="btn-historial">
                        <i class="fas fa-history"></i> VER HISTORIAL COMPLETO
                    </a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>

</body>
</html>