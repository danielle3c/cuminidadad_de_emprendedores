<?php 
include 'config.php'; 

// 1. Cargar configuración (Nombre del sistema y Tema)
$res_conf = mysqli_query($conexion, "SELECT * FROM configuraciones WHERE id = 1");
$cfg = mysqli_fetch_assoc($res_conf);
?>

<!DOCTYPE html>
<html lang="es" data-theme="<?php echo $cfg['tema_color']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Auditoría | <?php echo $cfg['nombre_sistema']; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root { 
            --bg: #f4f7fe; --card: #ffffff; --text: #2b3674; --primary: #43b02a; 
            --sidebar: #1b254b; --sidebar-hover: rgba(255, 255, 255, 0.08); --border: #e0e5f2; --secondary-text: #a3aed0;
        }
        [data-theme="dark"] { 
            --bg: #0b1437; --card: #111c44; --text: #ffffff; --primary: #2ecc71; --border: #1b254b; --secondary-text: #707eae;
        }

        body { font-family: 'DM Sans', sans-serif; background: var(--bg); color: var(--text); margin: 0; display: flex; height: 100vh; overflow: hidden; }

        /* --- SIDEBAR REDISEÑADO --- */
        .sidebar { 
            width: 290px; 
            background: var(--sidebar); 
            color: white; 
            display: flex; 
            flex-direction: column; 
            padding: 25px; 
            box-sizing: border-box; 
            transition: all 0.3s;
        }
        
        .sidebar-brand { 
            font-size: 1.2rem; 
            font-weight: 800; 
            margin-bottom: 40px; 
            padding: 15px;
            text-align: center; 
            color: white;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .nav-section-title {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,0.4);
            margin: 20px 0 10px 15px;
            font-weight: 700;
        }

        .nav-link { 
            display: flex; 
            align-items: center; 
            gap: 15px; 
            padding: 14px 18px; 
            color: #a3aed0; 
            text-decoration: none; 
            border-radius: 12px; 
            margin-bottom: 4px; 
            transition: 0.2s ease-in-out; 
            font-weight: 500;
        }

        .nav-link i { font-size: 1.1rem; width: 25px; text-align: center; }

        .nav-link:hover { 
            background: var(--sidebar-hover); 
            color: white; 
            transform: translateX(5px);
        }

        .nav-link.active { 
            background: var(--primary); 
            color: white; 
            font-weight: 700;
            box-shadow: 0px 10px 20px rgba(67, 176, 42, 0.3);
        }
        
        .nav-link.active i { color: white; }

        /* --- CONTENIDO PRINCIPAL --- */
        .main-content { flex: 1; overflow-y: auto; padding: 40px; position: relative; }

        .header-section h1 { font-size: 2.2rem; font-weight: 800; margin: 0; letter-spacing: -1px; }
        
        .search-container { 
            background: var(--card); border-radius: 20px; padding: 10px; 
            display: flex; align-items: center; margin-top: 25px; 
            box-shadow: 14px 17px 40px 4px rgba(112, 144, 176, 0.08);
            border: 1px solid var(--border);
        }
        .search-container input { 
            flex: 1; border: none; padding: 15px 25px; font-size: 1.1rem; 
            outline: none; background: transparent; color: var(--text);
        }
        .btn-search { 
            background: var(--primary); color: white; border: none; padding: 12px 35px; 
            border-radius: 15px; font-weight: 800; cursor: pointer; transition: 0.3s;
        }

        /* --- GRID Y CARDS --- */
        .results-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 25px; margin-top: 20px; }
        .person-card { background: var(--card); border-radius: 20px; border: 1px solid var(--border); padding: 25px; transition: 0.3s; }
        .person-card:hover { transform: translateY(-5px); box-shadow: 0px 20px 40px rgba(0,0,0,0.05); }
        
        .avatar { width: 65px; height: 65px; border-radius: 18px; object-fit: cover; }
        .badge { font-size: 0.65rem; padding: 4px 10px; border-radius: 8px; font-weight: 800; text-transform: uppercase; }
        .badge-base { background: #e2e8f0; color: #475569; }
        .badge-carrito { background: #fef9c3; color: #854d0e; }

        .stats-row { display: flex; justify-content: space-between; padding-top: 15px; border-top: 1px solid var(--border); margin-bottom: 15px; }
        .stat-num { display: block; font-weight: 800; font-size: 1.2rem; }
        .stat-label { font-size: 0.7rem; color: #a3aed0; font-weight: 700; }

        .btn-action { display: block; width: 100%; text-align: center; margin-top: 10px; padding: 12px; text-decoration: none; border-radius: 12px; font-weight: 700; font-size: 0.85rem; }
        .btn-trayectoria { background: #f4f7fe; color: #2b3674; border: 1px solid #e0e5f2; }
        .btn-formalizar { background: var(--primary); color: white; }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-leaf"></i> COF La Granja
    </div>

    <div class="nav-section-title">Principal</div>
    <nav>
        <a href="index.php" class="nav-link active">
            <i class="fas fa-house"></i> <span>Buscador</span>
        </a>
        <a href="personas.php" class="nav-link">
            <i class="fas fa-user-group"></i> <span>Personas</span>
        </a>
        <a href="lista_carritos.php" class="nav-link">
            <i class="fas fa-cart-shopping"></i> <span>Carritos</span>
        </a>
    </nav>

    <div class="nav-section-title">Finanzas y Gestión</div>
    <nav>
        <a href="creditos.php" class="nav-link">
            <i class="fas fa-hand-holding-dollar"></i> <span>Créditos</span>
        </a>
        <a href="cobranzas.php" class="nav-link">
            <i class="fas fa-file-invoice-dollar"></i> <span>Cobranzas</span>
        </a>
    </nav>

    <div style="margin-top: auto;">
        <div class="nav-section-title">Sistema</div>
        <a href="configuraciones.php" class="nav-link">
            <i class="fas fa-gears"></i> <span>Ajustes</span>
        </a>
    </div>
</aside>

<main class="main-content">
    <div class="header-section">
        <h1>Centro de Auditoría</h1>
        <p style="color: #a3aed0;">Gestión unificada de clientes formales y registros de carritos.</p>
        
        <form method="GET" class="search-container">
            <i class="fas fa-search" style="margin-left: 20px; color: #a3aed0;"></i>
            <input type="text" name="buscar" placeholder="Nombre completo, RUT o Apellidos..." value="<?php echo htmlspecialchars($_GET['buscar'] ?? ''); ?>" autofocus autocomplete="off">
            <button type="submit" class="btn-search">BUSCAR PERFIL</button>
        </form>
    </div>

    <div class="results-grid">
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
                    $q_visitas = mysqli_query($conexion, "SELECT COUNT(*) as t FROM carritos WHERE nombre_responsable LIKE '%$nombre_p%' AND asistencia = 'SÍ VINO'");
                    $visitas = mysqli_fetch_assoc($q_visitas)['t'];
            ?>
                <div class="person-card">
                    <div class="card-top">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($p['nombres']); ?>&background=random&bold=true" class="avatar">
                        <div>
                            <span class="badge <?php echo ($p['origen'] == 'persona') ? 'badge-base' : 'badge-carrito'; ?>">
                                <?php echo ($p['origen'] == 'persona') ? 'Cliente Formal' : 'Registro Informal'; ?>
                            </span>
                            <h3 style="margin: 5px 0; font-size: 1.1rem;"><?php echo htmlspecialchars($p['nombres']." ".$p['apellidos']); ?></h3>
                            <small style="color: #a3aed0; font-weight: 600;"><i class="far fa-id-card"></i> <?php echo $p['rut'] ?: 'Sin ID Registrada'; ?></small>
                        </div>
                    </div>
                    
                    <div class="stats-row">
                        <div class="stat-box">
                            <span class="stat-num"><?php echo $visitas; ?></span>
                            <span class="stat-label">Visitas</span>
                        </div>
                        <div class="stat-box">
                            <span class="stat-num" style="color: var(--primary);"><i class="fas fa-user-check"></i></span>
                            <span class="stat-label">Estado</span>
                        </div>
                        <div class="stat-box">
                            <span class="stat-num"><?php echo ($visitas > 4) ? 'Frecuente' : 'Nuevo'; ?></span>
                            <span class="stat-label">Rango</span>
                        </div>
                    </div>

                    <a href="ver_historial.php?nombre=<?php echo urlencode($p['nombres']); ?>&id=<?php echo $p['id']; ?>" class="btn-action btn-trayectoria">
                        <i class="fas fa-history"></i> VER TRAYECTORIA
                    </a>

                    <?php if($p['origen'] == 'carrito'): ?>
                        <a href="personas.php?formalizar_nombre=<?php echo urlencode($p['nombres']); ?>" class="btn-action btn-formalizar">
                            <i class="fas fa-user-plus"></i> REGISTRAR PARA PRÉSTAMO
                        </a>
                    <?php endif; ?>
                </div>
            <?php endwhile; 
            else: ?>
                <div style="grid-column: 1 / -1; text-align:center; padding: 80px; opacity:0.3;">
                    <i class="fas fa-search fa-4x"></i>
                    <p style="font-size: 1.2rem; font-weight: 700; margin-top: 20px;">No se encontraron registros.</p>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</main>

</body>
</html>