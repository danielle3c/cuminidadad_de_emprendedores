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
    <title>Buscador | <?php echo $cfg['nombre_sistema']; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    
    <style>
        :root { 
            --bg: #f4f7fe; --card: #ffffff; --text: #2b3674; --primary: #43b02a; 
            --sidebar: #1b254b; --sidebar-hover: rgba(255, 255, 255, 0.08); 
            --border: #e0e5f2; --secondary-text: #a3aed0;
        }

        body { font-family: 'DM Sans', sans-serif; background: var(--bg); color: var(--text); margin: 0; display: flex; height: 100vh; overflow: hidden; }

        /* --- SIDEBAR DINÁMICO --- */
        .sidebar { 
            width: 280px; background: var(--sidebar); color: white; 
            display: flex; flex-direction: column; padding: 20px; 
            box-sizing: border-box; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }

        /* Estado Colapsado (Tres líneas activo) */
        .sidebar.collapsed { width: 90px; padding: 20px 15px; }
        .sidebar.collapsed .nav-text, 
        .sidebar.collapsed .nav-section-title, 
        .sidebar.collapsed .sidebar-brand span { display: none; }
        .sidebar.collapsed .sidebar-brand { border-bottom: none; }

        .toggle-btn {
            cursor: pointer; font-size: 1.2rem; margin-bottom: 20px;
            display: flex; justify-content: center; color: rgba(255,255,255,0.6);
        }

        .sidebar-brand { 
            font-size: 1.1rem; font-weight: 800; margin-bottom: 30px; 
            text-align: center; color: var(--primary); padding-bottom: 15px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .nav-section-title { font-size: 0.65rem; text-transform: uppercase; color: rgba(255,255,255,0.3); margin: 15px 0 10px 10px; font-weight: 700; }

        .nav-link { 
            display: flex; align-items: center; gap: 15px; padding: 12px 15px; 
            color: #a3aed0; text-decoration: none; border-radius: 12px; 
            margin-bottom: 5px; transition: 0.2s;
        }
        .nav-link i { font-size: 1.2rem; min-width: 25px; text-align: center; }
        .nav-link:hover { background: var(--sidebar-hover); color: white; }
        .nav-link.active { background: var(--primary); color: white; box-shadow: 0 4px 15px rgba(67, 176, 42, 0.3); }

        /* --- CONTENIDO --- */
        .main-content { flex: 1; overflow-y: auto; padding: 40px; transition: 0.3s; }

        .search-container { 
            background: var(--card); border-radius: 20px; padding: 8px; 
            display: flex; align-items: center; margin-top: 25px; 
            box-shadow: 14px 17px 40px 4px rgba(112, 144, 176, 0.08); border: 1px solid var(--border);
        }
        .search-container input { 
            flex: 1; border: none; padding: 12px 20px; font-size: 1rem; 
            outline: none; background: transparent; color: var(--text);
        }
        .btn-search { background: var(--primary); color: white; border: none; padding: 10px 25px; border-radius: 12px; font-weight: 700; cursor: pointer; }

        /* --- CARDS DE RESULTADOS --- */
        .results-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px; margin-top: 30px; }
        .person-card { background: var(--card); border-radius: 20px; border: 1px solid var(--border); padding: 20px; transition: 0.3s; }
        .person-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .avatar { width: 55px; height: 55px; border-radius: 15px; margin-right: 15px; }
        .stats-row { display: flex; justify-content: space-between; margin-top: 15px; padding-top: 15px; border-top: 1px solid var(--border); }
        .stat-num { display: block; font-weight: 800; font-size: 1.1rem; }
        .stat-label { font-size: 0.65rem; color: #a3aed0; }
        
        .btn-action { display: block; width: 100%; text-align: center; margin-top: 10px; padding: 10px; text-decoration: none; border-radius: 10px; font-weight: 700; font-size: 0.8rem; }
        .btn-trayectoria { background: #f4f7fe; color: #2b3674; }
        .btn-formalizar { background: var(--primary); color: white; }
    </style>
</head>
<body>

<aside class="sidebar" id="sidebar">
    <div class="toggle-btn" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </div>

    <div class="sidebar-brand">
        <i class="fas fa-leaf"></i> <span>COF La Granja</span>
    </div>

    <div class="nav-section-title">Menú</div>
    <nav>
        <a href="index.php" class="nav-link active">
            <i class="fas fa-search"></i> <span class="nav-text">Buscador</span>
        </a>
        <a href="personas.php" class="nav-link">
            <i class="fas fa-users"></i> <span class="nav-text">Personas</span>
        </a>
        <a href="lista_carritos.php" class="nav-link">
            <i class="fas fa-cart-shopping"></i> <span class="nav-text">Carritos</span>
        </a>
    </nav>

    <div class="nav-section-title">Finanzas</div>
    <nav>
        <a href="creditos.php" class="nav-link">
            <i class="fas fa-money-bill-transfer"></i> <span class="nav-text">Créditos</span>
        </a>
    </nav>

    <div style="margin-top: auto;">
        <a href="configuraciones.php" class="nav-link">
            <i class="fas fa-cog"></i> <span class="nav-text">Ajustes</span>
        </a>
    </div>
</aside>

<main class="main-content">
    <div class="header-section">
        <h1 style="color: var(--text);">Centro de Auditoría</h1>
        <p style="color: var(--secondary-text); margin-top: 5px;">Busca perfiles de clientes y registros de carritos.</p>
        
        <form method="GET" class="search-container">
            <i class="fas fa-search" style="margin-left: 15px; color: var(--secondary-text);"></i>
            <input type="text" name="buscar" placeholder="Escribe nombre o RUT..." value="<?php echo $_GET['buscar'] ?? ''; ?>" autofocus>
            <button type="submit" class="btn-search">BUSCAR</button>
        </form>
    </div>

    <div class="results-grid">
        <?php 
        if (!empty($_GET['buscar'])): 
            $b = mysqli_real_escape_string($conexion, $_GET['buscar']);
            $sql = "SELECT idpersonas as id, nombres, apellidos, rut, 'persona' as origen FROM personas 
                    WHERE nombres LIKE '%$b%' OR apellidos LIKE '%$b%' OR rut LIKE '%$b%'
                    UNION
                    SELECT id, nombre_responsable, '', '', 'carrito' FROM carritos 
                    WHERE nombre_responsable LIKE '%$b%' GROUP BY nombre_responsable";
            
            $res = mysqli_query($conexion, $sql);
            while ($p = mysqli_fetch_assoc($res)):
                $n = $p['nombres'];
                $v = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) as t FROM carritos WHERE nombre_responsable LIKE '%$n%' AND asistencia = 'SÍ VINO'"))['t'];
        ?>
            <div class="person-card">
                <div style="display: flex; align-items: center;">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($n); ?>&background=random" class="avatar">
                    <div>
                        <span class="badge <?php echo ($p['origen'] == 'persona') ? 'badge-base' : 'badge-carrito'; ?>">
                            <?php echo ($p['origen'] == 'persona') ? 'Formal' : 'Informal'; ?>
                        </span>
                        <h4 style="margin: 5px 0;"><?php echo $n . " " . $p['apellidos']; ?></h4>
                        <small style="color: var(--secondary-text);"><?php echo $p['rut'] ?: 'Sin RUT'; ?></small>
                    </div>
                </div>
                
                <div class="stats-row">
                    <div style="text-align: center;">
                        <span class="stat-num"><?php echo $v; ?></span>
                        <span class="stat-label">Visitas</span>
                    </div>
                    <div style="text-align: center;">
                        <span class="stat-num"><?php echo ($v > 4) ? '⭐' : '🆕'; ?></span>
                        <span class="stat-label">Nivel</span>
                    </div>
                </div>

                <a href="ver_historial.php?nombre=<?php echo urlencode($n); ?>&id=<?php echo $p['id']; ?>" class="btn-action btn-trayectoria">Historial</a>
                <?php if($p['origen'] == 'carrito'): ?>
                    <a href="personas.php?formalizar=<?php echo urlencode($n); ?>" class="btn-action btn-formalizar">Formalizar</a>
                <?php endif; ?>
            </div>
        <?php endwhile; endif; ?>
    </div>
</main>

<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('collapsed');
    }
</script>

</body>
</html>