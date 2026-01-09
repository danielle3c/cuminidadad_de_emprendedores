<?php 
include 'config.php'; 

// 1. Cargar configuración del sistema (Colores y Nombre)
$res_conf = mysqli_query($conexion, "SELECT * FROM configuraciones WHERE id = 1");
$cfg = mysqli_fetch_assoc($res_conf);
?>

<!DOCTYPE html>
<html lang="es" data-theme="<?php echo $cfg['tema_color']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control | <?php echo $cfg['nombre_sistema']; ?></title>
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

        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); margin: 0; padding-bottom: 100px; }
        
        /* BANNER SUPERIOR ESTILIZADO */
        .hero-banner { 
            background: linear-gradient(135deg, var(--accent) 0%, #1e293b 100%);
            color: white;
            padding: 80px 20px 100px;
            text-align: center;
            border-radius: 0 0 50px 50px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            position: relative;
        }
        .hero-banner h1 { margin: 0; font-size: 2.5rem; letter-spacing: -1.5px; font-weight: 800; }
        .hero-banner p { opacity: 0.8; margin-top: 8px; font-size: 1.1rem; }

        .container { max-width: 850px; margin: auto; padding: 0 20px; }

        /* BUSCADOR FLOTANTE TIPO "CAPSULA" */
        .search-container {
            margin-top: -45px;
            position: relative;
            z-index: 10;
        }
        .search-box { 
            background: var(--card); 
            padding: 8px; 
            border-radius: 60px; 
            display: flex; 
            align-items: center; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.12);
            border: 1px solid var(--border);
        }
        .search-box input { 
            flex: 1; border: none; padding: 15px 25px; border-radius: 60px; 
            background: transparent; color: var(--text); font-size: 1.1rem; outline: none;
        }
        .search-box button { 
            background: var(--primary); color: white; border: none; 
            width: 55px; height: 55px; border-radius: 50%; cursor: pointer;
            transition: 0.3s; display: flex; align-items: center; justify-content: center;
        }
        .search-box button:hover { transform: scale(1.05); filter: brightness(1.1); }

        /* RESULTADOS DE BÚSQUEDA */
        .master-card { 
            background: var(--card); border-radius: 28px; margin-top: 25px; 
            border: 1px solid var(--border); overflow: hidden; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            animation: slideUp 0.5s ease;
        }
        @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }

        .card-main-info { padding: 25px; display: flex; align-items: center; gap: 20px; }
        .profile-pic { width: 75px; height: 75px; border-radius: 20px; border: 3px solid var(--primary-soft); }
        
        .user-info h3 { margin: 0; font-size: 1.3rem; letter-spacing: -0.5px; }
        .user-info span { font-size: 0.85rem; opacity: 0.6; font-weight: 600; display: block; margin-top: 4px; }

        .btn-trayectoria { 
            background: var(--accent); color: white; text-align: center; 
            display: block; padding: 18px; text-decoration: none; 
            font-weight: 800; font-size: 0.85rem; letter-spacing: 1.5px; 
            transition: 0.3s;
        }
        .btn-trayectoria:hover { background: var(--primary); }

        /* BOTONERA INFERIOR (TAB BAR) */
        .bottom-nav { 
            position: fixed; bottom: 0; left: 0; right: 0; 
            background: var(--card); 
            display: flex; justify-content: space-around; 
            padding: 12px 5px 25px; 
            border-top: 1px solid var(--border);
            box-shadow: 0 -10px 30px rgba(0,0,0,0.08);
            z-index: 1000;
        }
        .nav-item { 
            text-align: center; text-decoration: none; color: var(--text); 
            opacity: 0.5; transition: 0.3s; flex: 1;
        }
        .nav-item i { font-size: 1.5rem; display: block; margin-bottom: 5px; }
        .nav-item span { font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }
        .nav-item:hover, .nav-item.active { opacity: 1; color: var(--primary); }

        /* Badge de origen */
        .origin-badge { font-size: 0.6rem; padding: 3px 8px; border-radius: 4px; background: var(--primary-soft); color: var(--primary); font-weight: 900; }
    </style>
</head>
<body>

<div class="hero-banner">
    <div class="container">
        <h1>Centro de Gestión</h1>
        <p>Busca un perfil para auditar su trayectoria</p>
    </div>
</div>

<div class="container">
    <div class="search-container">
        <form method="GET" class="search-box">
            <input type="text" name="buscar" placeholder="Nombre o identificación del responsable..." value="<?php echo htmlspecialchars($_GET['buscar'] ?? ''); ?>" autocomplete="off" autofocus>
            <button type="submit"><i class="fas fa-search fa-lg"></i></button>
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

        if(mysqli_num_rows($res) > 0):
            while ($p = mysqli_fetch_assoc($res)): 
                $nombre_p = mysqli_real_escape_string($conexion, $p['nombres']);
                // Conteo rápido de visitas al carrito
                $asist = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) as t FROM carritos WHERE nombre_responsable LIKE '%$nombre_p%' AND asistencia = 'SÍ VINO'"))['t'];
        ?>
            <div class="master-card">
                <div class="card-main-info">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($p['nombres']); ?>&background=random&bold=true" class="profile-pic">
                    <div class="user-info">
                        <span class="origin-badge"><?php echo ($p['origen'] == 'persona') ? 'SISTEMA BASE' : 'REGISTRO CARRITO'; ?></span>
                        <h3><?php echo htmlspecialchars($p['nombres']." ".$p['apellidos']); ?></h3>
                        <span><i class="far fa-address-card"></i> <?php echo !empty($p['rut']) ? $p['rut'] : 'S/I'; ?> • <i class="fas fa-history"></i> <?php echo $asist; ?> Visitas</span>
                    </div>
                </div>
                <a href="ver_historial.php?nombre=<?php echo urlencode($p['nombres']); ?>&id=<?php echo $p['id']; ?>" class="btn-trayectoria">
                    <i class="fas fa-chart-line"></i> ANALIZAR TRAYECTORIA COMPLETA
                </a>
            </div>
        <?php endwhile; 
        else: ?>
            <div style="text-align:center; margin-top:60px; opacity:0.3;">
                <i class="fas fa-search-minus fa-4x"></i>
                <p style="margin-top:15px; font-weight:700;">No se encontró a nadie con ese nombre.</p>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<nav class="bottom-nav">
    <a href="index.php" class="nav-item <?php echo (!isset($_GET['buscar'])) ? 'active' : ''; ?>">
        <i class="fas fa-home"></i>
        <span>Inicio</span>
    </a>
    <a href="personas.php" class="nav-item">
        <i class="fas fa-users"></i>
        <span>Personas</span>
    </a>
    <a href="lista_carritos.php" class="nav-item">
        <i class="fas fa-shopping-basket"></i>
        <span>Carritos</span>
    </a>
    <a href="creditos.php" class="nav-item">
        <i class="fas fa-hand-holding-usd"></i>
        <span>Créditos</span>
    </a>
    <a href="configuraciones.php" class="nav-item">
        <i class="fas fa-cog"></i>
        <span>Ajustes</span>
    </a>
</nav>

</body>
</html>