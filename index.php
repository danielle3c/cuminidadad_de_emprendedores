<?php 
session_start();
include 'config.php'; 

// Cargar configuración
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
            --sidebar: #1b254b; --border: #e0e5f2; --secondary-text: #a3aed0;
            --danger: #ee5d5d; --warning: #ff9f43;
        }

        body { font-family: 'DM Sans', sans-serif; background: var(--bg); color: var(--text); margin: 0; display: flex; height: 100vh; overflow: hidden; }

        /* SIDEBAR */
        .sidebar { width: 280px; background: var(--sidebar); color: white; display: flex; flex-direction: column; padding: 20px; transition: 0.3s; }
        .sidebar.collapsed { width: 85px; }
        .sidebar.collapsed .nav-text, .sidebar.collapsed .sidebar-brand span { display: none; }
        
        .nav-link { display: flex; align-items: center; gap: 15px; padding: 12px 15px; color: #a3aed0; text-decoration: none; border-radius: 12px; margin-bottom: 5px; }
        .nav-link.active { background: var(--primary); color: white; }

        /* MAIN CONTENT */
        .main-content { flex: 1; overflow-y: auto; padding: 40px; }

        /* SEARCH */
        .search-container { 
            background: var(--card); border-radius: 20px; padding: 10px; 
            display: flex; align-items: center; margin-top: 25px; 
            box-shadow: 14px 17px 40px 4px rgba(112, 144, 176, 0.08); border: 1px solid var(--border);
        }
        .search-container input { flex: 1; border: none; padding: 12px 20px; font-size: 1.1rem; outline: none; background: transparent; color: var(--text); }
        .btn-search { background: var(--primary); color: white; border: none; padding: 12px 30px; border-radius: 15px; font-weight: 700; cursor: pointer; }

        /* CARDS */
        .results-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px; margin-top: 30px; }
        .person-card { background: var(--card); border-radius: 20px; border: 1px solid var(--border); padding: 20px; position: relative; overflow: hidden; }
        
        /* Etiqueta de Cobranza (Si debe dinero) */
        .debt-alert { 
            position: absolute; top: 0; right: 0; background: var(--danger); 
            color: white; padding: 5px 15px; font-size: 0.7rem; font-weight: 800; 
            border-bottom-left-radius: 15px; text-transform: uppercase;
        }

        .avatar { width: 60px; height: 60px; border-radius: 15px; margin-right: 15px; }
        .stats-row { display: flex; justify-content: space-around; margin-top: 15px; padding: 10px; background: #f8fafc; border-radius: 12px; }
        .stat-num { display: block; font-weight: 800; }
        .stat-label { font-size: 0.7rem; color: var(--secondary-text); }

        .btn-action { display: block; width: 100%; text-align: center; margin-top: 10px; padding: 10px; text-decoration: none; border-radius: 10px; font-weight: 700; font-size: 0.85rem; }
        .btn-cobranza { background: var(--danger); color: white; }
        .btn-history { background: #f4f7fe; color: var(--text); border: 1px solid var(--border); }
    </style>
</head>
<body>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand" style="text-align:center; padding-bottom:20px; color:var(--primary); font-weight:800;">
        <i class="fas fa-leaf"></i> <span>COF LA GRANJA</span>
    </div>
    <nav>
        <a href="index.php" class="nav-link active"><i class="fas fa-search"></i> <span class="nav-text">Buscador</span></a>
        <a href="personas.php" class="nav-link"><i class="fas fa-users"></i> <span class="nav-text">Personas</span></a>
        <a href="cobranzas.php" class="nav-link"><i class="fas fa-file-invoice-dollar"></i> <span class="nav-text">Cobranzas</span></a>
        <a href="configuraciones.php" class="nav-link"><i class="fas fa-cog"></i> <span class="nav-text">Ajustes</span></a>
    </nav>
</aside>

<main class="main-content">
    <h1>Centro de Auditoría</h1>
    <p style="color: var(--secondary-text);">Busca por Nombre o RUT para verificar estatus y deudas.</p>

    <form method="GET" class="search-container">
        <input type="text" name="buscar" placeholder="Nombre o RUT del cliente..." value="<?php echo $_GET['buscar'] ?? ''; ?>" autofocus>
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
                $n = $p['nombres'];
                $rut = $p['rut'];

                // CONSULTA DE COBRANZA: Ver si tiene cuotas pendientes
                $q_deuda = mysqli_query($conexion, "SELECT SUM(monto_pendiente) as deuda FROM cobranzas WHERE rut_cliente = '$rut' OR nombre_cliente LIKE '%$n%'");
                $dato_deuda = mysqli_fetch_assoc($q_deuda);
                $deuda_total = $dato_deuda['deuda'] ?? 0;
        ?>
            <div class="person-card">
                <?php if($deuda_total > 0): ?>
                    <div class="debt-alert"><i class="fas fa-exclamation-triangle"></i> Deuda: $<?php echo number_format($deuda_total, 0, ',', '.'); ?></div>
                <?php endif; ?>

                <div style="display: flex; align-items: center;">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($n); ?>&background=random" class="avatar">
                    <div>
                        <h4 style="margin:0;"><?php echo $n . " " . $p['apellidos']; ?></h4>
                        <small style="color:var(--secondary-text);"><?php echo $rut ?: 'Sin RUT'; ?></small>
                    </div>
                </div>

                <div class="stats-row">
                    <div style="text-align:center;">
                        <span class="stat-num" style="color: <?php echo ($deuda_total > 0) ? 'var(--danger)' : 'var(--primary)'; ?>">
                            <?php echo ($deuda_total > 0) ? 'Moroso' : 'Al día'; ?>
                        </span>
                        <span class="stat-label">Estado Financiero</span>
                    </div>
                </div>

                <a href="ver_historial.php?id=<?php echo $p['id']; ?>" class="btn-action btn-history">Ver Trayectoria</a>
                
                <?php if($deuda_total > 0): ?>
                    <a href="cobranzas.php?buscar=<?php echo urlencode($rut ?: $n); ?>" class="btn-action btn-cobranza">Gestionar Cobro</a>
                <?php endif; ?>
            </div>
        <?php endwhile; endif; ?>
    </div>
</main>

</body>
</html>