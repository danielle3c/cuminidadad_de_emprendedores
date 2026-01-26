<?php 
include 'config.php'; 

// Cargar configuración de colores y nombre
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
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root { 
            --bg: #f4f7fe; --card: #ffffff; --text: #2b3674; --primary: #55b83e; 
            --sidebar: #111c44; --border: #e0e5f2; --secondary-text: #a3aed0;
            --escuela: #f1c40f; 
        }
        [data-theme="dark"] { 
            --bg: #0b1437; --card: #111c44; --text: #ffffff; --primary: #2ecc71; --border: #1b254b; --secondary-text: #707eae;
        }

        body { font-family: 'DM Sans', sans-serif; background: var(--bg); color: var(--text); margin: 0; display: flex; height: 100vh; overflow: hidden; }

        /* Estilos Sidebar */
        .sidebar { width: 280px; background: var(--sidebar); color: white; display: flex; flex-direction: column; padding: 30px 20px; box-sizing: border-box; }
        .sidebar-brand { font-size: 1.2rem; font-weight: 800; margin-bottom: 50px; text-align: center; color: var(--primary); border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 20px; }
        .nav-link { display: flex; align-items: center; gap: 15px; padding: 16px 20px; color: #707eae; text-decoration: none; border-radius: 15px; margin-bottom: 8px; transition: 0.3s; font-weight: 700; }
        .nav-link:hover, .nav-link.active { background: rgba(255,255,255,0.05); color: white; }
        .nav-link.active { border-right: 4px solid var(--primary); }

        /* Contenido Principal */
        .main-content { flex: 1; overflow-y: auto; padding: 40px; }
        .search-container { background: var(--card); border-radius: 20px; padding: 10px; display: flex; align-items: center; margin-top: 25px; box-shadow: 14px 17px 40px 4px rgba(112, 144, 176, 0.08); border: 1px solid var(--border); }
        .search-container input { flex: 1; border: none; padding: 15px 25px; font-size: 1.1rem; outline: none; background: transparent; color: var(--text); }
        .btn-search { background: var(--primary); color: white; border: none; padding: 12px 35px; border-radius: 15px; font-weight: 800; cursor: pointer; }

        /* Cards de Resultados */
        .results-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 25px; margin-top: 30px; }
        .person-card { background: var(--card); border-radius: 20px; border: 1px solid var(--border); padding: 25px; transition: 0.3s; }
        .card-escuela { border-top: 5px solid var(--escuela); }
        
        .badge { font-size: 0.65rem; padding: 4px 10px; border-radius: 8px; font-weight: 800; text-transform: uppercase; }
        .badge-escuela { background: #fef9c3; color: #92400e; border: 1px solid #fde047; }
        .badge-base { background: #e2e8f0; color: #475569; }

        .btn-group { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 20px; }
        .btn-action { text-align: center; padding: 12px; text-decoration: none; border-radius: 12px; font-weight: 700; font-size: 0.85rem; }
        .btn-escuela { background: var(--escuela); color: #000; }
        .btn-trayectoria { background: var(--sidebar); color: white; }
        .btn-editar { background: #f3f4f6; color: #1f2937; border: 1px solid #d1d5db; }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-brand">COMUNIDAD EMPRENDEDORA</div>
    <nav>
        <a href="index.php" class="nav-link active"><i class="fas fa-search"></i> Buscador</a>
        <a href="digitalizar_escuela.php" class="nav-link" style="color: var(--escuela);"><i class="fas fa-sun"></i> Escuela de Verano</a>
        <a href="personas.php" class="nav-link"><i class="fas fa-users"></i> Personas</a>
        <a href="talleres.php" class="nav-link"><i class="fas fa-chalkboard-teacher"></i> Talleres</a>
        <a href="carritos.php" class="nav-link"><i class="fas fa-shopping-basket"></i> Carritos</a>
        <a href="creditos.php" class="nav-link"><i class="fas fa-hand-holding-dollar"></i> Créditos</a>
        <a href="cobranzas.php" class="nav-link"><i class="fas fa-file-invoice-dollar"></i> Cobranzas</a>
        <a href="configuraciones.php" class="nav-link"><i class="fas fa-tools"></i> Ajustes</a>
    </nav>
</aside>

<main class="main-content">
    <h1>Centro de Auditoría Unificado</h1>
    <form method="GET" class="search-container">
        <input type="text" name="buscar" placeholder="Nombre, RUT o Emprendimiento..." value="<?php echo $_GET['buscar'] ?? ''; ?>" autofocus>
        <button type="submit" class="btn-search">BUSCAR</button>
    </form>

    <div class="results-grid">
        <?php 
        if (isset($_GET['buscar']) && !empty($_GET['buscar'])): 
            $busqueda = mysqli_real_escape_string($conexion, $_GET['buscar']);
            
            $sql = "SELECT idpersonas as id, nombres, apellidos, rut, 'persona' as origen FROM personas 
                    WHERE nombres LIKE '%$busqueda%' OR apellidos LIKE '%$busqueda%'
                    UNION
                    SELECT id as id, nombre_responsable as nombres, '' as apellidos, '' as rut, 'carrito' as origen FROM carritos 
                    WHERE nombre_responsable LIKE '%$busqueda%'
                    UNION
                    SELECT id_escuela as id, nombre_emprendedor as nombres, '' as apellidos, '' as rut, 'escuela' as origen FROM escuela_verano 
                    WHERE nombre_emprendedor LIKE '%$busqueda%' OR nombre_negocio LIKE '%$busqueda%'";
            
            $res = mysqli_query($conexion, $sql);

            while ($p = mysqli_fetch_assoc($res)): 
                $id_ref = $p['id'];
                $origen = $p['origen'];
                
                // Lógica de visualización según origen
                if ($origen == 'escuela') {
                    $q = mysqli_query($conexion, "SELECT nombre_negocio FROM escuela_verano WHERE id_escuela = $id_ref");
                    $sub = mysqli_fetch_assoc($q)['nombre_negocio'];
                    $badge = "badge-escuela";
                } else {
                    $sub = $p['rut'] ?: 'Participante';
                    $badge = "badge-base";
                }
        ?>
            <div class="person-card <?php echo ($origen == 'escuela') ? 'card-escuela' : ''; ?>">
                <span class="badge <?php echo $badge; ?>"><?php echo $origen; ?></span>
                <h3 style="margin: 10px 0;"><?php echo $p['nombres']." ".$p['apellidos']; ?></h3>
                <p style="color: var(--secondary-text); font-size: 0.9rem;"><?php echo $sub; ?></p>

                <div class="btn-group">
                    <?php if($origen == 'escuela'): ?>
                        <a href="ver_historial_escuela.php?id=<?php echo $id_ref; ?>" class="btn-action btn-escuela">REPORTE PDF</a>
                        <a href="digitalizar_escuela.php?id=<?php echo $id_ref; ?>" class="btn-action btn-editar">EDITAR</a>
                    <?php else: ?>
                        <a href="ver_historial.php?id=<?php echo $id_ref; ?>" class="btn-action btn-trayectoria">TRAYECTORIA</a>
                        <a href="editar.php?id=<?php echo $id_ref; ?>" class="btn-action btn-editar">EDITAR</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; endif; ?>
    </div>
</main>

</body>
</html>