<?php 
include 'config.php'; 

// 1. Configuración y estadísticas
$res_conf = mysqli_query($conexion, "SELECT * FROM configuraciones WHERE id = 1");
$cfg = mysqli_fetch_assoc($res_conf);
$total_usuarios = mysqli_num_rows(mysqli_query($conexion, "SELECT idUsuarios FROM Usuarios WHERE deleted_at IS NULL"));
?>

<!DOCTYPE html>
<html lang="es" data-theme="<?php echo $cfg['tema_color']; ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo $cfg['nombre_sistema']; ?></title>
    <style>
        :root { 
            --bg: #f0f2f5; --card: #ffffff; --text: #333; --primary: #43b02a; --border: #e2e8f0; 
        }
        [data-theme="dark"] { 
            --bg: #18191a; --card: #242526; --text: #e4e6eb; --primary: #2ecc71; --border: #3a3b3c; 
        }

        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); color: var(--text); margin: 0; padding: 0; }
        
        /* Navbar */
        .nav-bar { background: var(--primary); padding: 15px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .nav-bar a { color: white; text-decoration: none; margin: 0 10px; font-weight: 500; font-size: 0.9em; }

        .container { max-width: 1100px; margin: auto; padding: 20px; }

        /* Alertas */
        .alert { padding: 15px; border-radius: 10px; text-align: center; margin-bottom: 20px; font-weight: bold; }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }

        /* Buscador */
        .search-box { background: var(--card); padding: 25px; border-radius: 15px; text-align: center; margin-bottom: 30px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .search-box input { width: 50%; padding: 12px; border: 2px solid var(--border); border-radius: 8px; font-size: 16px; background: var(--bg); color: var(--text); }
        .btn-search { padding: 12px 25px; background: var(--primary); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; }

        /* Dashboard Grid */
        .dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 15px; }
        .menu-card { 
            background: var(--card); padding: 20px; border-radius: 12px; text-align: center; 
            text-decoration: none; color: inherit; transition: 0.3s; box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            display: flex; flex-direction: column; align-items: center; justify-content: center;
        }
        .menu-card:hover { transform: translateY(-5px); box-shadow: 0 8px 15px rgba(0,0,0,0.1); background: var(--primary); color: white; }
        .menu-card img { width: 50px; height: 50px; margin-bottom: 12px; }
        .menu-card span { font-weight: bold; font-size: 0.9em; }

        /* Resultados */
        .result-item { 
            background: var(--card); padding: 15px; border-radius: 12px; margin-bottom: 15px; 
            display: flex; align-items: center; justify-content: space-between; gap: 20px; border-left: 5px solid var(--primary);
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .profile-pic { width: 60px; height: 60px; border-radius: 50%; object-fit: cover; background: #ddd; border: 2px solid var(--primary); }
        .info-user h3 { margin: 0; font-size: 1.1em; }
        .info-user p { margin: 5px 0 0; font-size: 0.85em; opacity: 0.8; }

        /* Botones de Acción */
        .actions { display: flex; gap: 10px; }
        .btn-action { padding: 8px 15px; border-radius: 8px; text-decoration: none; font-size: 0.85em; font-weight: bold; display: flex; align-items: center; gap: 5px; transition: 0.2s; }
        .btn-edit { background: #e0f2fe; color: #0369a1; border: 1px solid #7dd3fc; }
        .btn-edit:hover { background: #0369a1; color: white; }
        .btn-delete { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .btn-delete:hover { background: #ef4444; color: white; }
    </style>
</head>
<body>

<div class="nav-bar">
    <a href="index.php">🏠 Inicio</a>
    <a href="personas.php">👤 Personas</a>
    <a href="configuraciones.php">⚙️ Ajustes</a>
</div>

<div class="container">
    <h1 style="text-align: center;">🚀 <?php echo $cfg['nombre_sistema']; ?></h1>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'deleted'): ?>
        <div class="alert alert-success">✅ Registro eliminado correctamente.</div>
    <?php endif; ?>

    <div class="search-box">
        <form method="GET">
            <input type="text" name="buscar" placeholder="Buscar personas o negocios..." value="<?php echo $_GET['buscar'] ?? ''; ?>" autofocus>
            <button type="submit" class="btn-search">🔍 Buscar</button>
        </form>
    </div>

    <?php if (isset($_GET['buscar']) && !empty(trim($_GET['buscar']))): ?>
        <div id="resultados">
            <?php 
            $busqueda = mysqli_real_escape_string($conexion, $_GET['buscar']);
            $sql = "SELECT p.*, e.idemprendedores FROM personas p 
                    LEFT JOIN emprendedores e ON p.idpersonas = e.personas_idpersonas 
                    WHERE (p.nombres LIKE '%$busqueda%' OR p.rut LIKE '%$busqueda%') AND p.deleted_at IS NULL";
            $res = mysqli_query($conexion, $sql);

            if(mysqli_num_rows($res) > 0):
                while ($f = mysqli_fetch_assoc($res)) { 
                    $avatar = "https://ui-avatars.com/api/?name=".urlencode($f['nombres'])."&background=random&color=fff";
                ?>
                    <div class="result-item">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <img src="img/fotos/<?php echo $f['idpersonas']; ?>.jpg" class="profile-pic" onerror="this.src='<?php echo $avatar; ?>'">
                            <div class="info-user">
                                <h3><?php echo $f['nombres'] . " " . $f['apellidos']; ?></h3>
                                <p>RUT: <?php echo $f['rut']; ?> | <?php echo ($f['idemprendedores']) ? "💼 Emprendedor" : "👤 Natural"; ?></p>
                            </div>
                        </div>
                        <div class="actions">
                            <a href="editar_persona.php?id=<?php echo $f['idpersonas']; ?>" class="btn-action btn-edit">✏️ Editar</a>
                            <a href="eliminar_persona.php?id=<?php echo $f['idpersonas']; ?>" class="btn-action btn-delete" onclick="return confirm('¿Eliminar registro?')">🗑️ Borrar</a>
                        </div>
                    </div>
                <?php } 
            else: echo "<p style='text-align:center;'>No se encontraron resultados.</p>"; endif; ?>
        </div>
    <?php else: ?>
        <div class="dashboard-grid">
            <a href="personas.php" class="menu-card">
                <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="">
                <span>Personas</span>
            </a>
            <a href="emprendedores.php" class="menu-card">
                <img src="https://cdn-icons-png.flaticon.com/512/3135/3135706.png" alt="">
                <span>Negocios</span>
            </a>
            <a href="contratos.php" class="menu-card">
                <img src="https://cdn-icons-png.flaticon.com/512/3522/3522616.png" alt="">
                <span>Contratos</span>
            </a>
            <a href="creditos.php" class="menu-card">
                <img src="https://cdn-icons-png.flaticon.com/512/2489/2489756.png" alt="">
                <span>Créditos</span>
            </a>
            <a href="cobranzas.php" class="menu-card">
                <img src="https://cdn-icons-png.flaticon.com/512/1019/1019607.png" alt="">
                <span>Cobranzas</span>
            </a>
            <a href="carritos.php" class="menu-card">
                <img src="https://cdn-icons-png.flaticon.com/512/1170/1170678.png" alt="">
                <span>Carritos</span>
            </a>
            <a href="jornadas.php" class="menu-card">
                <img src="https://cdn-icons-png.flaticon.com/512/3652/3652191.png" alt="">
                <span>Jornadas</span>
            </a>
            <a href="configuraciones.php" class="menu-card">
                <img src="https://cdn-icons-png.flaticon.com/512/3953/3953226.png" alt="">
                <span>Ajustes</span>
            </a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>