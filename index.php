<?php 
include 'config.php'; 

// 1. Obtener la configuración general
$res_conf = mysqli_query($conexion, "SELECT * FROM configuraciones WHERE id = 1");
$cfg = mysqli_fetch_assoc($res_conf);

// 2. Estadísticas para el Dashboard
$total_usuarios = mysqli_num_rows(mysqli_query($conexion, "SELECT idUsuarios FROM Usuarios WHERE deleted_at IS NULL"));
?>

<!DOCTYPE html>
<html lang="es" data-theme="<?php echo $cfg['tema_color']; ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo $cfg['nombre_sistema']; ?> - Panel de Control</title>
    <style>
        /* Variables dinámicas según el tema */
        :root { 
            --bg: #f0f2f5; --card: #ffffff; --text: #333; --primary: #43b02a; --border: #e2e8f0; 
        }
        [data-theme="dark"] { 
            --bg: #18191a; --card: #242526; --text: #e4e6eb; --primary: #2ecc71; --border: #3a3b3c; 
        }

        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); color: var(--text); margin: 0; padding: 0; transition: 0.3s; }
        
        /* Navegación */
        .nav-bar { background: var(--primary); padding: 15px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .nav-bar a { color: white; text-decoration: none; margin: 0 12px; font-weight: 500; font-size: 0.9em; }
        .nav-bar a:hover { text-decoration: underline; }

        .header-title { text-align: center; padding: 20px; margin: 0; }

        .container { max-width: 1100px; margin: auto; padding: 20px; }

        /* Estilo Buscador */
        .search-box { background: var(--card); padding: 25px; border-radius: 12px; text-align: center; margin-bottom: 30px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        input[type="text"] { width: 60%; padding: 12px; border: 2px solid var(--border); border-radius: 8px; background: var(--bg); color: var(--text); font-size: 16px; }
        .btn-search { padding: 12px 25px; background: var(--primary); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; }

        /* Estilo Dashboard (Grilla) */
        .dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-top: 20px; }
        .card { background: var(--card); padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); text-align: center; text-decoration: none; color: inherit; transition: 0.3s; }
        .card:hover { transform: translateY(-5px); }
        .card h3 { margin: 0 0 10px 0; color: var(--primary); font-size: 1.1rem; }
        
        /* Resultados de Búsqueda */
        .result-card { border: 1px solid var(--border); padding: 20px; border-radius: 10px; margin-bottom: 20px; border-left: 6px solid var(--primary); background: var(--card); }
        .btn { padding: 8px 14px; text-decoration: none; border-radius: 6px; font-size: 0.8em; font-weight: bold; display: inline-block; margin: 5px; }
        .btn-edit { background: #fef3c7; color: #92400e; }
        .btn-user { background: #dcfce7; color: #166534; }
        .btn-pay { background: #e0f2fe; color: #0369a1; }
        .badge { background: var(--primary); color: white; padding: 3px 8px; border-radius: 4px; font-size: 0.8em; }
    </style>
</head>
<body>

<div class="nav-bar">
    <a href="index.php">🔍 Buscar</a>
    <a href="usuarios_lista.php">👥 Usuarios</a>
    <a href="configuraciones.php">⚙️ Ajustes</a>
    <a href="personas.php">👤 Personas</a>
    <a href="creditos.php">💰 Créditos</a>
</div>

<div class="container">
    <h1 class="header-title">🚀 <?php echo $cfg['nombre_sistema']; ?></h1>

    <div class="search-box">
        <form method="GET">
            <input type="text" name="buscar" placeholder="Buscar por Nombre, RUT o ID..." value="<?php echo $_GET['buscar'] ?? ''; ?>">
            <button type="submit" class="btn-search">Consultar</button>
        </form>
    </div>

    <?php if (isset($_GET['buscar']) && !empty(trim($_GET['buscar']))): ?>
        <div id="resultados">
            <?php 
            $busqueda = mysqli_real_escape_string($conexion, $_GET['buscar']);
            $sql = "SELECT p.*, e.idemprendedores, e.rubro, cr.idcreditos, cr.saldo_inicial, u.idUsuarios 
                    FROM personas p
                    LEFT JOIN emprendedores e ON p.idpersonas = e.personas_idpersonas AND e.deleted_at = 0
                    LEFT JOIN creditos cr ON e.idemprendedores = cr.emprendedores_idemprendedores AND cr.estado = 1
                    LEFT JOIN Usuarios u ON p.idpersonas = u.personas_idpersonas
                    WHERE (p.nombres LIKE '%$busqueda%' OR p.apellidos LIKE '%$busqueda%' OR p.rut LIKE '%$busqueda%' OR p.idpersonas = '$busqueda')
                    AND p.deleted_at = 0 LIMIT 5";
            $res = mysqli_query($conexion, $sql);

            if (mysqli_num_rows($res) > 0) {
                while ($f = mysqli_fetch_assoc($res)) { ?>
                    <div class="result-card">
                        <span class="badge">ID: <?php echo $f['idpersonas']; ?></span>
                        <h3><?php echo $f['nombres'] . " " . $f['apellidos']; ?></h3>
                        <p>RUT: <?php echo $f['rut'] ?: 'No registrado'; ?> | Tipo: <?php echo ($f['idemprendedores']) ? "💼 Emprendedor" : "👤 Natural"; ?></p>
                        <div style="text-align: right;">
                            <a href="editar_persona.php?id=<?php echo $f['idpersonas']; ?>" class="btn btn-edit">Ficha</a>
                            <?php if($f['idUsuarios']): ?>
                                <a href="editar_usuario.php?id=<?php echo $f['idUsuarios']; ?>" class="btn btn-user">Gestionar Usuario</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php }
            } else { echo "<p style='text-align:center;'>❌ No se encontraron resultados.</p>"; } ?>
        </div>
    <?php else: ?>
        <div class="dashboard-grid">
            <div class="card">
                <h3>⚙️ Configuración</h3>
                <p>Tema: <?php echo ucfirst($cfg['tema_color']); ?></p>
                <p>Idioma: <?php echo strtoupper($cfg['idioma']); ?></p>
                <a href="configuraciones.php" style="color: var(--primary); font-weight:bold;">Ajustar</a>
            </div>

            <a href="usuarios_lista.php" class="card">
                <h3>👥 Usuarios</h3>
                <p><?php echo $total_usuarios; ?> cuentas activas</p>
                <p>Gestionar accesos</p>
            </a>

            <div class="card">
                <h3>🔐 Seguridad</h3>
                <p>SMTP: <?php echo ($cfg['password_email']) ? '✅ Activo' : '❌ Pendiente'; ?></p>
                <p>Estado del Servidor: OK</p>
            </div>
        </div>
    <?php endif; ?>
</div>

</body>
</html>