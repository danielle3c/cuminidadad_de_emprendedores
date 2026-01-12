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
    <title>Auditoría y Cobranzas | <?php echo $cfg['nombre_sistema']; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root { 
            --bg: #f4f7fe; --card: #ffffff; --text: #2b3674; --primary: #43b02a; 
            --sidebar: #111c44; --border: #e0e5f2; --secondary-text: #a3aed0;
            --danger: #ee5d5d; --warning: #ff9f43;
        }

        body { font-family: 'DM Sans', sans-serif; background: var(--bg); color: var(--text); margin: 0; display: flex; height: 100vh; overflow: hidden; }

        /* --- SIDEBAR DINÁMICO --- */
        .sidebar { 
            width: 280px; background: var(--sidebar); color: white; 
            display: flex; flex-direction: column; padding: 20px; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .sidebar.collapsed { width: 85px; padding: 20px 15px; }
        .sidebar.collapsed .nav-text, .sidebar.collapsed .sidebar-brand span { display: none; }

        .toggle-btn {
            cursor: pointer; font-size: 1.2rem; margin-bottom: 20px;
            display: flex; justify-content: center; color: rgba(255,255,255,0.6);
        }

        .sidebar-brand { font-size: 1.2rem; font-weight: 800; margin-bottom: 30px; text-align: center; color: var(--primary); }

        .nav-link { 
            display: flex; align-items: center; gap: 15px; padding: 12px 15px; 
            color: #707eae; text-decoration: none; border-radius: 12px; margin-bottom: 5px; transition: 0.2s;
        }
        .nav-link:hover, .nav-link.active { background: rgba(255,255,255,0.05); color: white; }
        .nav-link.active { border-right: 4px solid var(--primary); }

        /* --- CONTENIDO --- */
        .main-content { flex: 1; overflow-y: auto; padding: 40px; transition: 0.3s; }

        .search-container { 
            background: var(--card); border-radius: 20px; padding: 10px; 
            display: flex; align-items: center; margin-top: 25px; 
            box-shadow: 14px 17px 40px 4px rgba(112, 144, 176, 0.08); border: 1px solid var(--border);
        }
        .search-container input { flex: 1; border: none; padding: 12px 20px; font-size: 1.1rem; outline: none; background: transparent; color: var(--text); }
        .btn-search { background: var(--primary); color: white; border: none; padding: 12px 30px; border-radius: 15px; font-weight: 700; cursor: pointer; }

        /* --- CARDS CON COBRANZA --- */
        .results-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px; margin-top: 30px; }
        .person-card { background: var(--card); border-radius: 20px; border: 1px solid var(--border); padding: 25px; position: relative; }
        
        .debt-badge { 
            position: absolute; top: 0; right: 0; background: var(--danger); 
            color: white; padding: 6px 12px; font-size: 0.7rem; font-weight: 800; 
            border-bottom-left-radius: 15px;
        }

        .avatar { width: 60px; height: 60px; border-radius: 15px; margin-right: 15px; }
        .stats-row { display: flex; justify-content: space-around; margin: 20px 0; padding: 15px; background: var(--bg); border-radius: 15px; }
        
        .btn-action { display: block; width: 100%; text-align: center; margin-top: 10px; padding: 12px; text-decoration: none; border-radius: 12px; font-weight: 700; font-size: 0.85rem; }
        .btn-cobranza { background: var(--danger); color: white; }
        .btn-trayectoria { background: var(--sidebar); color: white; }
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
    <nav>
        <a href="index.php" class="nav-link active"><i class="fas fa-search"></i> <span class="nav-text">Buscador</span></a>
        <a href="personas.php" class="nav-link"><i class="fas fa-users"></i> <span class="nav-text">Personas</span></a>
        <a href="cobranzas.php" class="nav-link"><i class="fas fa-file-invoice-dollar"></i> <span class="nav-text">Cobranzas</span></a>
        <a href="configuraciones.php" class="nav-link"><i class="fas fa-tools"></i> <span class="nav-text">Ajustes</span></a>
    </nav>
</aside>

<main class="main-content">
    <h1>Centro de Auditoría</h1>
    <p style="color: var(--secondary-text);">Buscador unificado de deudas y registros.</p>

    <form method="GET" class="search-container">
        <input type="text" name="buscar" placeholder="Nombre o RUT..." value="<?php echo $_GET['buscar'] ?? ''; ?>" autofocus>
        <button type="submit" class="btn-search">BUSCAR</button>
    </form>

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
                $rut = $p['rut'];
                $nom = $p['nombres'];

                // 2. VERIFICAR COBRANZA
                $q_deuda = mysqli_query($conexion, "SELECT SUM(monto_pendiente) as total FROM cobranzas WHERE rut_cliente = '$rut' OR nombre_cliente LIKE '%$nom%'");
                $deuda = mysqli_fetch_assoc($q_deuda)['total'] ?? 0;
        ?>
            <div class="person-card">
                <?php if($deuda > 0): ?>
                    <div class="debt-badge"><i class="fas fa-exclamation-circle"></i> DEUDA: $<?php echo number_format($deuda,0,',','.'); ?></div>
                <?php endif; ?>

                <div style="display: flex; align-items: center;">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($nom); ?>&background=random" class="avatar">
                    <div>
                        <h4 style="margin:0;"><?php echo $nom." ".$p['apellidos']; ?></h4>
                        <small><?php echo $rut ?: 'Sin RUT'; ?></small>
                    </div>
                </div>

                <div class="stats-row">
                    <div style="text-align:center;">
                        <span style="font-weight:800; display:block; color: <?php echo ($deuda > 0) ? 'var(--danger)' : 'var(--primary)'; ?>">
                            <?php echo ($deuda > 0) ? 'MOROSO' : 'AL DÍA'; ?>
                        </span>
                        <small class="stat-label">FINANZAS</small>
                    </div>
                </div>

                <a href="ver_historial.php?id=<?php echo $p['id']; ?>" class="btn-action btn-trayectoria">Ver Trayectoria</a>
                
                <?php if($deuda > 0): ?>
                    <a href="cobranzas.php?buscar=<?php echo urlencode($rut ?: $nom); ?>" class="btn-action btn-cobranza">Gestionar Cobro</a>
                <?php elseif($p['origen'] == 'carrito'): ?>
                    <a href="personas.php?formalizar=<?php echo urlencode($nom); ?>" class="btn-action btn-formalizar">Formalizar (Contrato)</a>
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