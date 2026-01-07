<?php 
include 'config.php'; 

// 1. Obtener la configuración general (Tema, Nombre del Sistema)
$res_conf = mysqli_query($conexion, "SELECT * FROM configuraciones WHERE id = 1");
$cfg = mysqli_fetch_assoc($res_conf);

// 2. Estadísticas para el Dashboard
$total_usuarios = mysqli_num_rows(mysqli_query($conexion, "SELECT idUsuarios FROM Usuarios WHERE deleted_at IS NULL"));
?>

<!DOCTYPE html>
<html lang="es" data-theme="<?php echo $cfg['tema_color']; ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo $cfg['nombre_sistema']; ?> - Gestión</title>
    <style>
        :root { 
            --bg: #f0f2f5; --card: #ffffff; --text: #333; --primary: #43b02a; --border: #e2e8f0; --danger: #dc2626;
        }
        [data-theme="dark"] { 
            --bg: #18191a; --card: #242526; --text: #e4e6eb; --primary: #2ecc71; --border: #3a3b3c; --danger: #ff4d4d;
        }

        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); color: var(--text); margin: 0; padding: 0; transition: 0.3s; }
        
        .nav-bar { background: var(--primary); padding: 15px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .nav-bar a { color: white; text-decoration: none; margin: 0 12px; font-weight: 500; font-size: 0.9em; }

        .container { max-width: 1100px; margin: auto; padding: 20px; }
        .header-title { text-align: center; padding: 10px; }

        /* Buscador */
        .search-box { background: var(--card); padding: 25px; border-radius: 12px; text-align: center; margin-bottom: 30px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        input[type="text"] { width: 60%; padding: 12px; border: 2px solid var(--border); border-radius: 8px; background: var(--bg); color: var(--text); }
        .btn-search { padding: 12px 25px; background: var(--primary); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; }

        /* Dashboard */
        .dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; }
        .card { background: var(--card); padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); text-align: center; text-decoration: none; color: inherit; }

        /* Resultados */
        .result-card { border: 1px solid var(--border); padding: 20px; border-radius: 10px; margin-bottom: 20px; border-left: 6px solid var(--primary); background: var(--card); }
        .actions-group { margin-top: 15px; padding-top: 12px; border-top: 1px dashed var(--border); display: flex; gap: 8px; flex-wrap: wrap; }
        
        .btn { padding: 8px 14px; text-decoration: none; border-radius: 6px; font-size: 0.8em; font-weight: bold; display: inline-block; }
        .btn-edit { background: #fef3c7; color: #92400e; }
        .btn-user { background: #dcfce7; color: #166534; }
        .btn-del { background: var(--danger); color: white; }
        .badge { background: var(--primary); color: white; padding: 3px 8px; border-radius: 4px; font-size: 0.8em; }
    </style>
</head>
<body>

<div class="nav-bar">
    <a href="index.php">🔍 Buscar</a>
    <a href="usuarios_lista.php">👥 Usuarios</a>
    <a href="configuraciones.php">⚙️ Ajustes</a>
    <a href="personas.php">👤 Personas</a>
</div>

<div class="container">
    <h1 class="header-title">🚀 <?php echo $cfg['nombre_sistema']; ?></h1>

    <div class="search-box">
        <form method="GET">
            <input type="text" name="buscar" placeholder="Nombre, RUT o ID..." value="<?php echo $_GET['buscar'] ?? ''; ?>">
            <button type="submit" class="btn-search">Consultar</button>
        </form>
    </div>

    <?php if (isset($_GET['buscar']) && !empty(trim($_GET['buscar']))): ?>
        <div id="resultados">
            <?php 
            $busqueda = mysqli_real_escape_string($conexion, $_GET['buscar']);
            $sql = "SELECT p.*, e.idemprendedores, e.rubro, u.idUsuarios 
                    FROM personas p
                    LEFT JOIN emprendedores e ON p.idpersonas = e.personas_idpersonas AND e.deleted_at = 0
                    LEFT JOIN Usuarios u ON p.idpersonas = u.personas_idpersonas
                    WHERE (p.nombres LIKE '%$busqueda%' OR p.rut LIKE '%$busqueda%' OR p.idpersonas = '$busqueda')
                    AND p.deleted_at = 0 LIMIT 5";
            $res = mysqli_query($conexion, $sql);

            if (mysqli_num_rows($res) > 0) {
                while ($f = mysqli_fetch_assoc($res)) { ?>
                    <div class="result-card">
                        <div style="display: flex; justify-content: space-between;">
                            <div>
                                <span class="badge">ID: <?php echo $f['idpersonas']; ?></span>
                                <h3><?php echo $f['nombres'] . " " . $f['apellidos']; ?></h3>
                                <p>RUT: <?php echo $f['rut'] ?: 'No registrado'; ?> | Tipo: <?php echo ($f['idemprendedores']) ? "💼 Emprendedor" : "👤 Natural"; ?></p>
                            </div>
                            <div>
                                <a href="editar_persona.php?id=<?php echo $f['idpersonas']; ?>" class="btn btn-edit">📝 Ficha</a>
                                <?php if($f['idUsuarios']): ?>
                                    <a href="editar_usuario.php?id=<?php echo $f['idUsuarios']; ?>" class="btn btn-user">⚙️ Cuenta</a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="actions-group">
                            <a href="eliminar_persona.php?id=<?php echo $f['idpersonas']; ?>" class="btn btn-del" onclick="return confirm('¿Borrar a esta PERSONA por completo?')">🗑️ Borrar Persona</a>
                            
                            <?php if($f['idUsuarios']): ?>
                                <a href="eliminar_usuario.php?id=<?php echo $f['idUsuarios']; ?>" class="btn btn-del" style="opacity: 0.8;" onclick="return confirm('¿Quitar acceso al sistema?')">🚫 Quitar Usuario</a>
                            <?php endif; ?>

                            <?php if($f['idemprendedores']): ?>
                                <a href="eliminar_emprendedor.php?id=<?php echo $f['idemprendedores']; ?>" class="btn btn-del" style="background:#6b21a8;" onclick="return confirm('¿Dar de baja el negocio?')">💼 Borrar Negocio</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php }
            } else { echo "<p style='text-align:center;'>❌ Sin resultados.</p>"; } ?>
        </div>
    <?php else: ?>
        <div class="dashboard-grid">
            <div class="card">
                <h3>👥 Usuarios</h3>
                <p><?php echo $total_usuarios; ?> Activos</p>
            </div>
            <div class="card">
                <h3>⚙️ Sistema</h3>
                <p>Tema: <?php echo ucfirst($cfg['tema_color']); ?></p>
            </div>
        </div>
    <?php endif; ?>
</div>

</body>
</html>