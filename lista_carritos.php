<?php 
include 'config.php'; 

// 1. Cargar configuración del sistema
$res_conf = mysqli_query($conexion, "SELECT * FROM configuraciones WHERE id = 1");
$cfg = mysqli_fetch_assoc($res_conf);

// 2. Lógica de Búsqueda
$buscar = isset($_GET['buscar']) ? mysqli_real_escape_string($conexion, $_GET['buscar']) : "";

// 3. Lógica de Contadores (Hoy)
$hoy = date('Y-m-d');
$res_asistencia = mysqli_query($conexion, "SELECT 
    COUNT(*) as total, 
    SUM(CASE WHEN asistencia = 'SÍ VINO' THEN 1 ELSE 0 END) as vinieron 
    FROM carritos WHERE DATE(created_at) = '$hoy'");
$stats = mysqli_fetch_assoc($res_asistencia);
?>

<!DOCTYPE html>
<html lang="es" data-theme="<?php echo $cfg['tema_color']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control | <?php echo $cfg['nombre_sistema']; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --bg: #f1f5f9; --card: #ffffff; --text: #1e293b; --primary: #43b02a; --border: #e2e8f0; }
        [data-theme="dark"] { --bg: #0f172a; --card: #1e293b; --text: #f1f5f9; --primary: #2ecc71; --border: #334155; }
        
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); color: var(--text); padding: 15px; margin: 0; }
        .container { max-width: 1000px; margin: auto; }
        
        /* Mensajes de alerta */
        .alert { padding: 12px; border-radius: 10px; text-align: center; margin-bottom: 15px; font-weight: bold; }
        .alert-delete { background: #fee2e2; color: #b91c1c; border: 1px solid #f87171; }

        /* Tarjetas superiores */
        .stats-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; }
        .stat-card { background: var(--card); padding: 15px; border-radius: 12px; border: 1px solid var(--border); text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
        .stat-card h3 { margin: 0; font-size: 1.5rem; color: var(--primary); }
        .stat-card p { margin: 5px 0 0; font-size: 0.75rem; opacity: 0.7; font-weight: bold; text-transform: uppercase; }

        /* Buscador */
        .search-container { margin-bottom: 20px; display: flex; gap: 8px; }
        .search-input { flex: 1; padding: 12px; border-radius: 10px; border: 1px solid var(--border); background: var(--card); color: var(--text); font-size: 0.95rem; }
        .btn-search { background: var(--primary); color: white; border: none; padding: 10px 18px; border-radius: 10px; cursor: pointer; }
        .btn-clear { background: #64748b; color: white; padding: 10px 15px; border-radius: 10px; text-decoration: none; }

        /* Tabla */
        .card-table { background: var(--card); border-radius: 15px; border: 1px solid var(--border); overflow-x: auto; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; min-width: 600px; }
        th { background: #f8fafc; padding: 12px; text-align: left; font-size: 0.7rem; text-transform: uppercase; color: #64748b; border-bottom: 1px solid var(--border); }
        td { padding: 12px; border-bottom: 1px solid var(--border); font-size: 0.9rem; }
        
        .tiempo-tag { background: rgba(67, 176, 42, 0.1); color: var(--primary); padding: 3px 7px; border-radius: 6px; font-weight: bold; font-size: 0.8rem; }
        .badge { padding: 4px 8px; border-radius: 6px; font-size: 0.7rem; font-weight: bold; }
        .si { background: #dcfce7; color: #15803d; }
        .no { background: #fee2e2; color: #b91c1c; }
        
        .actions { display: flex; gap: 12px; }
        .btn-edit { color: #3b82f6; }
        .btn-delete { color: #ef4444; border: none; background: none; cursor: pointer; padding: 0; }

        @media (max-width: 600px) { .hide-mobile { display: none; } }
    </style>
</head>
<body>

<div class="container">
    <div style="margin-bottom: 20px; text-align: center;">
        <h2 style="margin: 0;"><i class="fas fa-list-check"></i> Registro de Carritos</h2>
        <small style="color: var(--primary); font-weight: bold;">Gestión en Tiempo Real</small>
    </div>

    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'eliminado'): ?>
        <div class="alert alert-delete">
            <i class="fas fa-trash-alt"></i> Registro eliminado correctamente.
        </div>
    <?php endif; ?>

    <form method="GET" class="search-container">
        <input type="text" name="buscar" class="search-input" placeholder="Buscar por nombre o carrito..." value="<?php echo htmlspecialchars($buscar); ?>">
        <button type="submit" class="btn-search"><i class="fas fa-search"></i></button>
        <?php if($buscar != ""): ?>
            <a href="lista_carritos.php" class="btn-clear"><i class="fas fa-times"></i></a>
        <?php endif; ?>
    </form>

    <div class="stats-grid">
        <div class="stat-card">
            <h3><?php echo (int)$stats['vinieron']; ?></h3>
            <p>Vinieron Hoy</p>
        </div>
        <div class="stat-card">
            <a href="carritos.php" style="text-decoration:none; color:inherit;">
                <h3 style="color:#3b82f6;"><i class="fas fa-plus-circle"></i></h3>
                <p>Nuevo Registro</p>
            </a>
        </div>
    </div>

    <div class="card-table">
        <table>
            <thead>
                <tr>
                    <th>Fecha / Hora</th>
                    <th>Responsable</th>
                    <th class="hide-mobile">Carrito</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $where = "";
                if ($buscar != "") {
                    $where = "WHERE nombre_responsable LIKE '%$buscar%' OR nombre_carrito LIKE '%$buscar%'";
                }

                $sql = "SELECT * FROM carritos $where ORDER BY created_at DESC LIMIT 50";
                $res = mysqli_query($conexion, $sql);

                if (mysqli_num_rows($res) > 0) {
                    while($row = mysqli_fetch_assoc($res)) {
                        $clase_ast = ($row['asistencia'] == 'SÍ VINO') ? 'si' : 'no';
                        $fecha_dt = new DateTime($row['created_at']);
                    ?>
                    <tr>
                        <td>
                            <strong style="display:block;"><?php echo $fecha_dt->format('d/m/Y'); ?></strong>
                            <span class="tiempo-tag"><?php echo $fecha_dt->format('H:i'); ?></span>
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($row['nombre_responsable']); ?></strong>
                            <div style="font-size:0.75rem; opacity:0.6;" class="hide-mobile">
                                <i class="fas fa-phone-alt"></i> <?php echo htmlspecialchars($row['telefono_responsable']); ?>
                            </div>
                        </td>
                        <td class="hide-mobile">
                            <span style="background:#f1f5f9; padding:4px 8px; border-radius:5px; border:1px solid #e2e8f0; font-weight:600;">
                                <?php echo htmlspecialchars($row['nombre_carrito']); ?>
                            </span>
                        </td>
                        <td><span class="badge <?php echo $clase_ast; ?>"><?php echo $row['asistencia']; ?></span></td>
                        <td>
                            <div class="actions">
                                <a href="editar_carrito.php?id=<?php echo $row['id']; ?>" class="btn-edit" title="Editar">
                                    <i class="fas fa-edit fa-lg"></i>
                                </a>
                                <a href="eliminar_carrito.php?id=<?php echo $row['id']; ?>" 
                                   class="btn-delete" 
                                   onclick="return confirm('¿Eliminar este registro de forma permanente?');" 
                                   title="Eliminar">
                                    <i class="fas fa-trash-alt fa-lg"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php 
                    } 
                } else {
                    echo "<tr><td colspan='5' style='text-align:center; padding:30px; opacity:0.5;'>No hay registros que coincidan.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>