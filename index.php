<?php 
include 'config.php'; 

// 1. Configuración general del sistema
$res_conf = mysqli_query($conexion, "SELECT * FROM configuraciones WHERE id = 1");
$cfg = mysqli_fetch_assoc($res_conf);
?>

<!DOCTYPE html>
<html lang="es" data-theme="<?php echo $cfg['tema_color']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $cfg['nombre_sistema']; ?></title>
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

        body { font-family: 'Inter', 'Segoe UI', sans-serif; background: var(--bg); color: var(--text); margin: 0; line-height: 1.5; }
        .nav-bar { background: var(--card); padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); }
        .nav-logo { font-weight: 800; color: var(--primary); font-size: 1.2rem; text-decoration: none; }
        .nav-links a { color: var(--text); text-decoration: none; margin-left: 20px; font-size: 0.9rem; font-weight: 500; transition: 0.2s; }
        .nav-links a:hover { color: var(--primary); }
        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }

        /* Estilos del Buscador */
        .hero-search { text-align: center; margin-bottom: 50px; }
        .hero-search h1 { font-size: 2.2rem; margin-bottom: 20px; letter-spacing: -1px; }
        .search-wrapper { position: relative; max-width: 600px; margin: auto; }
        .search-wrapper input { 
            width: 100%; padding: 16px 25px; border: 2px solid var(--border); border-radius: 50px; 
            background: var(--card); color: var(--text); font-size: 1rem; box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            transition: 0.3s; box-sizing: border-box;
        }
        .btn-search-icon { 
            position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
            background: var(--primary); color: white; border: none; padding: 10px 20px; border-radius: 40px; cursor: pointer;
        }

        /* Tarjetas de Resultados */
        .master-card { background: var(--card); border-radius: 20px; margin-bottom: 30px; box-shadow: 0 15px 30px rgba(0,0,0,0.05); border: 1px solid var(--border); animation: slideUp 0.4s ease-out; overflow: hidden; }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        .card-header { padding: 25px; display: flex; align-items: center; gap: 20px; border-bottom: 1px solid var(--border); }
        .profile-pic { width: 80px; height: 80px; border-radius: 20px; object-fit: cover; border: 3px solid var(--primary-soft); }
        .card-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); background: var(--border); gap: 1px; }
        .history-box { background: var(--card); padding: 20px; }
        .history-box h4 { margin: 0 0 10px 0; font-size: 0.7rem; text-transform: uppercase; color: var(--primary); letter-spacing: 0.5px; }
        .data-value { font-size: 1rem; font-weight: 700; display: block; }
        .data-label { font-size: 0.8rem; opacity: 0.6; }

        .badge { padding: 4px 10px; border-radius: 6px; font-size: 0.65rem; font-weight: 800; display: inline-flex; align-items: center; gap: 5px; }
        .badge-danger { background: #fee2e2; color: #ef4444; }
        .badge-warning { background: #fef9c3; color: #854d0e; }
        .badge-success { background: var(--primary-soft); color: var(--primary); }

        /* Dashboard de Iconos */
        .dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 20px; }
        .menu-card { background: var(--card); padding: 30px; border-radius: 20px; text-align: center; text-decoration: none; color: inherit; transition: 0.3s; border: 1px solid var(--border); }
        .menu-card:hover { border-color: var(--primary); transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .menu-card img { width: 50px; height: 50px; margin-bottom: 15px; }
        .menu-card span { display: block; font-weight: 700; font-size: 0.9rem; }
    </style>
</head>
<body>

<div class="nav-bar">
    <a href="index.php" class="nav-logo"><?php echo $cfg['nombre_sistema']; ?></a>
    <div class="nav-links">
        <a href="personas.php">Personas</a>
        <a href="emprendedores.php">Negocios</a>
        <a href="configuraciones.php">Ajustes</a>
    </div>
</div>

<div class="container">

    <div class="hero-search">
        <h1>¿A quién buscamos hoy?</h1>
        <form method="GET" class="search-wrapper">
            <input type="text" name="buscar" placeholder="Nombre, negocio o RUT..." value="<?php echo $_GET['buscar'] ?? ''; ?>" autofocus autocomplete="off">
            <button type="submit" class="btn-search-icon">Buscar</button>
        </form>
    </div>

    <?php if (isset($_GET['buscar']) && !empty(trim($_GET['buscar']))): ?>
        <div id="resultados">
            <?php 
            $busqueda = mysqli_real_escape_string($conexion, $_GET['buscar']);
            
            // BUSQUEDA UNIFICADA (Personas registradas + Carritos temporales)
            $sql = "SELECT idpersonas as id, nombres, apellidos, rut, telefono, 'persona' as origen 
                    FROM personas 
                    WHERE nombres LIKE '%$busqueda%' OR apellidos LIKE '%$busqueda%' OR rut LIKE '%$busqueda%'
                    UNION
                    SELECT id as id, nombre_responsable as nombres, '' as apellidos, '' as rut, telefono_responsable as telefono, 'carrito' as origen 
                    FROM carritos 
                    WHERE nombre_responsable LIKE '%$busqueda%'
                    LIMIT 10";
            
            $res = mysqli_query($conexion, $sql);

            if(mysqli_num_rows($res) > 0):
                while ($p = mysqli_fetch_assoc($res)) { 
                    $id_ref = $p['id'];
                    $es_persona = ($p['origen'] == 'persona');
                    $nombre_completo = $es_persona ? $p['nombres']." ".$p['apellidos'] : $p['nombres'];
                    $avatar = "https://ui-avatars.com/api/?name=".urlencode($nombre_completo)."&background=random&color=fff";
                ?>
                    <div class="master-card">
                        <div class="card-header">
                            <img src="<?php echo $es_persona ? 'img/fotos/'.$id_ref.'.jpg' : $avatar; ?>" class="profile-pic" onerror="this.src='<?php echo $avatar; ?>'">
                            <div>
                                <?php if($es_persona): ?>
                                    <span class="badge badge-success"><i class="fas fa-check-circle"></i> CLIENTE VERIFICADO</span>
                                <?php else: ?>
                                    <span class="badge badge-warning"><i class="fas fa-clock"></i> REGISTRO TEMPORAL (CARRITOS)</span>
                                <?php endif; ?>
                                <h2 style="margin:5px 0; font-size: 1.6rem;"><?php echo $nombre_completo; ?></h2>
                                <div style="display:flex; gap: 15px; font-size: 0.85rem; opacity: 0.7;">
                                    <span><i class="fas fa-id-card"></i> <?php echo !empty($p['rut']) ? $p['rut'] : 'S/RUT'; ?></span>
                                    <span><i class="fas fa-phone"></i> <?php echo !empty($p['telefono']) ? $p['telefono'] : 'S/TEL'; ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="card-grid">
                            <div class="history-box">
                                <h4>Negocio / Puesto</h4>
                                <?php 
                                if($es_persona):
                                    $q_emp = mysqli_query($conexion, "SELECT tipo_negocio FROM emprendedores WHERE personas_idpersonas = $id_ref LIMIT 1");
                                    $emp = mysqli_fetch_assoc($q_emp);
                                    echo "<span class='data-value'>".($emp['tipo_negocio'] ?? 'No asignado')."</span>";
                                else:
                                    $q_car = mysqli_query($conexion, "SELECT nombre_carrito FROM carritos WHERE id = $id_ref");
                                    $car = mysqli_fetch_assoc($q_car);
                                    echo "<span class='data-value'>".$car['nombre_carrito']."</span>";
                                endif;
                                ?>
                            </div>

                            <div class="history-box">
                                <h4>Última Actividad</h4>
                                <?php 
                                $n_sql = mysqli_real_escape_string($conexion, $p['nombres']);
                                $q_st = mysqli_query($conexion, "SELECT asistencia, created_at FROM carritos WHERE nombre_responsable LIKE '%$n_sql%' ORDER BY created_at DESC LIMIT 1");
                                if($st = mysqli_fetch_assoc($q_st)):
                                    echo "<span class='data-value'>".$st['asistencia']."</span>";
                                    echo "<span class='data-label'>".date('d M, Y', strtotime($st['created_at']))."</span>";
                                else: echo "<p class='data-label'>Sin registros.</p>"; endif;
                                ?>
                            </div>

                            <div class="history-box" style="background: var(--primary-soft); display: flex; flex-direction: column; justify-content: center; gap: 8px;">
                                <?php if($es_persona): ?>
                                    <a href="editar_persona.php?id=<?php echo $id_ref; ?>" style="text-align:center; font-weight:800; color: var(--primary); text-decoration:none;">GESTIONAR PERFIL →</a>
                                <?php else: ?>
                                    <a href="convertir_persona.php?id_carrito=<?php echo $id_ref; ?>" style="text-align:center; font-weight:800; color: #3b82f6; text-decoration:none;"><i class="fas fa-user-plus"></i> FORMALIZAR</a>
                                    <a href="editar_carrito.php?id=<?php echo $id_ref; ?>" style="text-align:center; font-size: 0.75rem; color: var(--text); opacity: 0.5; text-decoration:none;">Editar registro</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php } 
            else: 
                echo "<div style='text-align:center; padding: 50px;'><i class='fas fa-search' style='font-size:3rem; opacity:0.1; margin-bottom:15px; display:block;'></i><p style='opacity:0.5'>No se encontraron resultados para '$busqueda'.</p></div>";
            endif; ?>
        </div>
    <?php else: ?>
        <div class="dashboard-grid">
            <a href="personas.php" class="menu-card"><img src="https://cdn-icons-png.flaticon.com/512/565/565431.png"><span>Personas</span></a>
            <a href="emprendedores.php" class="menu-card"><img src="https://cdn-icons-png.flaticon.com/512/11520/11520633.png"><span>Negocios</span></a>
            <a href="creditos.php" class="menu-card"><img src="https://cdn-icons-png.flaticon.com/512/2424/2424527.png"><span>Créditos</span></a>
            <a href="carritos.php" class="menu-card"><img src="https://images.vexels.com/media/users/3/242821/isolated/preview/183eec6aea425e4345f315593eb097c3-transporte-graphicicon-30.png"><span>Carritos</span></a>
            <a href="configuraciones.php" class="menu-card"><img src="https://cdn-icons-png.flaticon.com/512/6063/6063673.png"><span>Ajustes</span></a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>