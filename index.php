<?php 
include 'config.php'; 

// 1. Cargar configuración
$res_conf = mysqli_query($conexion, "SELECT * FROM configuraciones WHERE id = 1");
$cfg = mysqli_fetch_assoc($res_conf);
?>

<!DOCTYPE html>
<html lang="es" data-theme="<?php echo $cfg['tema_color']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control - <?php echo $cfg['nombre_sistema']; ?></title>
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

        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); margin: 0; padding-bottom: 50px; }
        
        .container { max-width: 1000px; margin: 30px auto; padding: 0 20px; }

        /* GRID DE ACCESOS DIRECTOS (Lo que se te había perdido) */
        .menu-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); 
            gap: 15px; 
            margin-bottom: 40px; 
        }
        .menu-item { 
            background: var(--card); 
            padding: 20px; 
            border-radius: 20px; 
            text-align: center; 
            text-decoration: none; 
            color: var(--text); 
            border: 1px solid var(--border);
            transition: 0.3s;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }
        .menu-item:hover { transform: translateY(-5px); border-color: var(--primary); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
        .menu-item i { font-size: 2rem; margin-bottom: 10px; display: block; color: var(--primary); }
        .menu-item span { font-weight: 700; font-size: 0.9rem; }

        /* BUSCADOR */
        .search-section { background: var(--card); padding: 30px; border-radius: 24px; border: 1px solid var(--border); margin-bottom: 30px; text-align: center; }
        .search-wrapper { position: relative; max-width: 600px; margin: 20px auto 0; }
        .search-wrapper input { 
            width: 100%; padding: 15px 25px; border: 2px solid var(--border); border-radius: 50px; 
            background: var(--bg); color: var(--text); font-size: 1rem; outline: none; transition: 0.3s;
        }
        .search-wrapper input:focus { border-color: var(--primary); }

        /* TARJETAS DE RESULTADOS */
        .master-card { background: var(--card); border-radius: 24px; margin-bottom: 20px; border: 1px solid var(--border); overflow: hidden; }
        .card-main-info { padding: 20px; display: flex; align-items: center; gap: 15px; }
        .profile-pic { width: 60px; height: 60px; border-radius: 15px; }
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); background: var(--bg); gap: 1px; border-top: 1px solid var(--border); }
        .stat-item { background: var(--card); padding: 12px; text-align: center; }
        .stat-value { display: block; font-weight: 800; }
        .stat-label { font-size: 0.65rem; text-transform: uppercase; opacity: 0.6; }
        .btn-historial { background: var(--accent); color: white; padding: 10px; width: 100%; display: block; text-align: center; text-decoration: none; font-weight: 700; font-size: 0.8rem; }
    </style>
</head>
<body>

<div class="container">
    <h2 style="margin-bottom: 20px; letter-spacing: -1px;">Panel Principal</h2>

    <div class="menu-grid">
        <a href="personas.php" class="menu-item">
            <i class="fas fa-users"></i>
            <span>Personas</span>
        </a>
        <a href="lista_carritos.php" class="menu-item">
            <i class="fas fa-shopping-basket"></i>
            <span>Carritos</span>
        </a>
        <a href="creditos.php" class="menu-item">
            <i class="fas fa-hand-holding-usd"></i>
            <span>Créditos</span>
        </a>
        <a href="cobranzas.php" class="menu-item">
            <i class="fas fa-receipt"></i>
            <span>Cobranzas</span>
        </a>
        <a href="configuraciones.php" class="menu-item">
            <i class="fas fa-cog"></i>
            <span>Ajustes</span>
        </a>
    </div>

    <div class="search-section">
        <h3 style="margin:0;">Buscador de Trayectoria</h3>
        <p style="opacity:0.6; font-size:0.9rem;">Encuentra perfiles para ver sus deudas y asistencias</p>
        <form method="GET" class="search-wrapper">
            <input type="text" name="buscar" placeholder="Nombre o RUT..." value="<?php echo htmlspecialchars($_GET['buscar'] ?? ''); ?>" autocomplete="off">
        </form>
    </div>

    <?php 
    if (isset($_GET['buscar']) && !empty(trim($_GET['buscar']))): 
        $busqueda = mysqli_real_escape_string($conexion, $_GET['buscar']);
        $sql = "SELECT idpersonas as id, nombres, apellidos, rut, 'persona' as origen FROM personas 
                WHERE nombres LIKE '%$busqueda%' OR apellidos LIKE '%$busqueda%' OR rut LIKE '%$busqueda%'
                UNION
                SELECT id as id, nombre_responsable as nombres, '' as apellidos, '' as rut, 'carrito' as origen FROM carritos 
                WHERE nombre_responsable LIKE '%$busqueda%' GROUP BY nombre_responsable";
        $res = mysqli_query($conexion, $sql);

        while ($p = mysqli_fetch_assoc($res)): 
            $nombre_para_sql = mysqli_real_escape_string($conexion, $p['nombres']);
            
            // Conteo rápido
            $asist = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) as t FROM carritos WHERE nombre_responsable LIKE '%$nombre_para_sql%' AND asistencia = 'SÍ VINO'"))['t'];
            
            $cred = 0;
            if($p['origen'] == 'persona'){
                $id_p = $p['id'];
                $cred = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) as t FROM creditos c JOIN emprendedores e ON c.emprendedores_idemprendedores = e.idemprendedores WHERE e.personas_idpersonas = $id_p"))['t'];
            }
    ?>
        <div class="master-card">
            <div class="card-main-info">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($p['nombres']); ?>&background=random" class="profile-pic">
                <div>
                    <h3 style="margin:0;"><?php echo $p['nombres']." ".$p['apellidos']; ?></h3>
                    <small style="opacity:0.6"><?php echo $p['rut']; ?></small>
                </div>
            </div>
            <div class="stats-grid">
                <div class="stat-item"><span class="stat-value"><?php echo $asist; ?></span><span class="stat-label">Visitas</span></div>
                <div class="stat-item"><span class="stat-value"><?php echo $cred; ?></span><span class="stat-label">Créditos</span></div>
                <div class="stat-item"><span class="stat-value"><?php echo ($asist > 3) ? 'Activo' : 'Nuevo'; ?></span><span class="stat-label">Estado</span></div>
            </div>
            <a href="ver_historial.php?nombre=<?php echo urlencode($p['nombres']); ?>&id=<?php echo $p['id']; ?>" class="btn-historial">VER TRAYECTORIA</a>
        </div>
    <?php endwhile; endif; ?>
</div>

</body>
</html>