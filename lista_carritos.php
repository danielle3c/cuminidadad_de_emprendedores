<?php 
include 'config.php'; 

// 1. Cargar configuración
$res_conf = mysqli_query($conexion, "SELECT * FROM configuraciones WHERE id = 1");
$cfg = mysqli_fetch_assoc($res_conf);

// 2. Lógica de Búsqueda
$buscar = isset($_GET['buscar']) ? mysqli_real_escape_string($conexion, $_GET['buscar']) : "";

// 3. Estadísticas de hoy
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
    <title>Lista de Carritos | <?php echo $cfg['nombre_sistema']; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --bg: #f1f5f9; --card: #ffffff; --text: #1e293b; --primary: #43b02a; --secondary: #3b82f6; --border: #e2e8f0; }
        [data-theme="dark"] { --bg: #0f172a; --card: #1e293b; --text: #f1f5f9; --primary: #2ecc71; --secondary: #60a5fa; --border: #334155; }
        
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); color: var(--text); padding: 15px; margin: 0; }
        .container { max-width: 1000px; margin: auto; }
        
        /* Estilos de la tabla y tarjetas */
        .card-table { background: var(--card); border-radius: 15px; border: 1px solid var(--border); overflow-x: auto; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; min-width: 600px; }
        th { background: #f8fafc; padding: 12px; text-align: left; font-size: 0.75rem; text-transform: uppercase; color: #64748b; border-bottom: 1px solid var(--border); }
        td { padding: 12px; border-bottom: 1px solid var(--border); font-size: 0.9rem; vertical-align: middle; }

        /* Etiquetas de tiempo (Entrada / Salida) */
        .tiempo-wrapper { display: flex; flex-direction: column; gap: 5px; }
        .tag { display: inline-flex; align-items: center; gap: 5px; padding: 3px 8px; border-radius: 6px; font-weight: bold; font-size: 0.75rem; width: fit-content; }
        .tag-entrada { background: rgba(67, 176, 42, 0.1); color: var(--primary); }
        .tag-salida { background: rgba(59, 130, 246, 0.1); color: var(--secondary); }
        .tag-vacio { background: #f1f5f9; color: #94a3b8; border: 1px dashed #cbd5e1; }

        .badge { padding: 4px 8px; border-radius: 6px; font-size: 0.7rem; font-weight: bold; }
        .si { background: #dcfce7; color: #15803d; }
        .no { background: #fee2e2; color: #b91c1c; }

        .search-container { margin-bottom: 20px; display: flex; gap: 8px; }
        .search-input { flex: 1; padding: 12px; border-radius: 10px; border: 1px solid var(--border); background: var(--card); color: var(--text); }
        .btn-search { background: var(--primary); color: white; border: none; padding: 10px 18px; border-radius: 10px; cursor: pointer; }

        @media (max-width: 600px) { .hide-mobile { display: none; } }
    </style>
</head>
<body>

<div class="container">
    <div style="text-align: center; margin-bottom: 25px;">
        <h2 style="margin: 0;"><i class="fas fa-list-check"></i> Registro de Carritos</h2>
        <a href="carritos.php" style="color: var(--secondary); text-decoration: none; font-weight: bold; font-size: 0.9rem;">+ Nuevo Registro</a>
    </div>

    <form method="GET" class="search-container">
        <input type="text" name="buscar" class="search-input" placeholder="Buscar responsable o carrito..." value="<?php echo htmlspecialchars($buscar); ?>">
        <button type="submit" class="btn-search"><i class="fas fa-search"></i></button>
    </form>

    <div class="card-table">
        <table>
            <thead>
                <tr>
                    <th>Fecha y Horarios</th>
                    <th>Responsable</th>
                    <th class="hide-mobile">Carrito</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $where = $buscar != "" ? "WHERE nombre_responsable LIKE '%$buscar%' OR nombre_carrito LIKE '%$buscar%'" : "";
                $sql = "SELECT * FROM carritos $where ORDER BY created_at DESC LIMIT 50";
                $res = mysqli_query($conexion, $sql);

                if (mysqli_num_rows($res) > 0) {
                    while($row = mysqli_fetch_assoc($res)) {
                        $fecha_dt = new DateTime($row['created_at']);
                        $clase_ast = ($row['asistencia'] == 'SÍ VINO') ? 'si' : 'no';
                    ?>
                    <tr>
                        <td>
                            <div class="tiempo-wrapper">
                                <span style="font-weight: 800; color: var(--text);">
                                    <i class="far fa-calendar-alt"></i> <?php echo $fecha_dt->format('d/m/Y'); ?>
                                </span>
                                <div class="tag tag-entrada">
                                    <i class="fas fa-sign-in-alt"></i> EN: <?php echo $fecha_dt->format('H:i'); ?>
                                </div>
                                <?php if(!empty($row['hora_salida'])): ?>
                                    <div class="tag tag-salida">
                                        <i class="fas fa-sign-out-alt"></i> SAL: <?php echo date('H:i', strtotime($row['hora_salida'])); ?>
                                    </div>
                                <?php else: ?>
                                    <div class="tag tag-vacio">
                                        <i class="fas fa-clock"></i> SAL: --:--
                                    </div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($row['nombre_responsable']); ?></strong>
                            <div style="font-size: 0.75rem; opacity: 0.6;"><i class="fas fa-phone"></i> <?php echo $row['telefono_responsable']; ?></div>
                        </td>
                        <td class="hide-mobile">
                            <span style="background: #f1f5f9; padding: 4px 8px; border-radius: 5px; font-weight: 600;">
                                <?php echo htmlspecialchars($row['nombre_carrito']); ?>
                            </span>
                        </td>
                        <td><span class="badge <?php echo $clase_ast; ?>"><?php echo $row['asistencia']; ?></span></td>
                        <td>
                            <div style="display: flex; gap: 10px;">
                                <a href="editar_carrito.php?id=<?php echo $row['id']; ?>" style="color: var(--secondary);"><i class="fas fa-edit"></i></a>
                                <a href="eliminar.php?id=<?php echo $row['id']; ?>" style="color: #ef4444;" onclick="return confirm('¿Eliminar?')"><i class="fas fa-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php } 
                } else {
                    echo "<tr><td colspan='5' style='text-align:center; padding:20px;'>No hay registros</td></tr>";
                } ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>