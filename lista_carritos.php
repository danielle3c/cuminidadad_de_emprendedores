<?php 
// 1. Conexión y Configuración
include 'config.php'; 

if (!$conexion) {
    die("Error crítico: No se pudo conectar a la base de datos. Revisa config.php");
}

// Cargar configuración del tema (colores y nombre del sistema)
$res_conf = mysqli_query($conexion, "SELECT * FROM configuraciones WHERE id = 1");
$cfg = mysqli_fetch_assoc($res_conf);

// 2. Capturar filtros de búsqueda
$search = isset($_GET['buscar']) ? mysqli_real_escape_string($conexion, $_GET['buscar']) : "";
$desde  = isset($_GET['desde']) ? mysqli_real_escape_string($conexion, $_GET['desde']) : "";
$hasta  = isset($_GET['hasta']) ? mysqli_real_escape_string($conexion, $_GET['hasta']) : "";
?>

<!DOCTYPE html>
<html lang="es" data-theme="<?php echo $cfg['tema_color']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial | <?php echo $cfg['nombre_sistema']; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --bg: #f8fafc; --card: #ffffff; --text: #1e293b; --primary: #43b02a; --border: #e2e8f0; }
        [data-theme="dark"] { --bg: #0f172a; --card: #1e293b; --text: #f1f5f9; --primary: #2ecc71; --border: #334155; }
        
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: var(--bg); color: var(--text); padding: 20px; }
        .container { max-width: 1200px; margin: auto; }
        
        /* Barra de herramientas / Buscador */
        .toolbar { 
            background: var(--card); 
            padding: 20px; 
            border-radius: 12px; 
            border: 1px solid var(--border); 
            margin-bottom: 25px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: flex-end;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .tool-group { flex: 1; min-width: 200px; display: flex; flex-direction: column; gap: 8px; }
        .tool-group label { font-size: 0.75rem; font-weight: bold; color: var(--primary); text-transform: uppercase; }
        .input-tool { padding: 10px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg); color: var(--text); }
        
        .btn-search { background: var(--primary); color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: bold; }
        .btn-clear { background: #64748b; color: white; text-decoration: none; padding: 10px 15px; border-radius: 8px; font-size: 0.9rem; }

        /* Tabla */
        .card-table { background: var(--card); border-radius: 12px; border: 1px solid var(--border); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        th { background: rgba(0,0,0,0.03); padding: 15px; text-align: left; font-size: 0.85rem; color: var(--text); border-bottom: 2px solid var(--border); }
        td { padding: 15px; border-bottom: 1px solid var(--border); font-size: 0.95rem; }
        
        .badge { padding: 5px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: bold; }
        .si { background: #dcfce7; color: #166534; }
        .no { background: #fee2e2; color: #991b1b; }
        
        .btn-edit { color: #3b82f6; text-decoration: none; font-weight: bold; }
        .btn-edit:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0;"><i class="fas fa-history"></i> Historial de Carritos</h2>
        <a href="carritos.php" style="color: var(--primary); text-decoration: none; font-weight: bold;">+ Nuevo Registro</a>
    </div>

    <form method="GET" class="toolbar">
        <div class="tool-group">
            <label><i class="fas fa-search"></i> Buscar Nombre, Tel o Carrito</label>
            <input type="text" name="buscar" class="input-tool" placeholder="Ej: Juan 987..." value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div class="tool-group">
            <label><i class="fas fa-calendar-alt"></i> Desde</label>
            <input type="date" name="desde" class="input-tool" value="<?php echo $desde; ?>">
        </div>
        <div class="tool-group">
            <label><i class="fas fa-calendar-alt"></i> Hasta</label>
            <input type="date" name="hasta" class="input-tool" value="<?php echo $hasta; ?>">
        </div>
        <div style="display: flex; gap: 8px;">
            <button type="submit" class="btn-search">Filtrar</button>
            <?php if($search != "" || $desde != "" || $hasta != ""): ?>
                <a href="lista_carritos.php" class="btn-clear" title="Limpiar"><i class="fas fa-sync-alt"></i></a>
            <?php endif; ?>
        </div>
    </form>
    
    <div class="card-table">
        <table>
            <thead>
                <tr>
                    <th>Fecha / Hora</th>
                    <th>Responsable</th>
                    <th>Contacto</th>
                    <th>Carrito</th>
                    <th>Asistencia</th>
                    <th style="text-align: center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // 3. CONSTRUCCIÓN DE LA CONSULTA SQL CON FILTROS
                $condiciones = [];

                if ($search != "") {
                    $condiciones[] = "(nombre_responsable LIKE '%$search%' OR telefono_responsable LIKE '%$search%' OR nombre_carrito LIKE '%$search%')";
                }
                if ($desde != "" && $hasta != "") {
                    $condiciones[] = "DATE(created_at) BETWEEN '$desde' AND '$hasta'";
                } elseif ($desde != "") {
                    $condiciones[] = "DATE(created_at) >= '$desde'";
                }

                $sql = "SELECT * FROM carritos";
                if (count($condiciones) > 0) {
                    $sql .= " WHERE " . implode(" AND ", $condiciones);
                }
                $sql .= " ORDER BY created_at DESC";
                
                $res = mysqli_query($conexion, $sql);

                if($res && mysqli_num_rows($res) > 0) {
                    while($row = mysqli_fetch_assoc($res)) {
                        $clase_ast = ($row['asistencia'] == 'SÍ VINO') ? 'si' : 'no';
                        ?>
                        <tr>
                            <td>
                                <strong><?php echo date("d/m/Y", strtotime($row['created_at'])); ?></strong><br>
                                <small style="opacity: 0.7;"><?php echo date("H:i", strtotime($row['created_at'])); ?> hs</small>
                            </td>
                            <td><strong><?php echo htmlspecialchars($row['nombre_responsable']); ?></strong></td>
                            <td><i class="fas fa-phone-alt" style="font-size: 0.8rem; color: #94a3b8;"></i> <?php echo htmlspecialchars($row['telefono_responsable']); ?></td>
                            <td><span style="background: var(--border); padding: 3px 7px; border-radius: 4px; font-size: 0.85rem;"><?php echo htmlspecialchars($row['nombre_carrito']); ?></span></td>
                            <td><span class="badge <?php echo $clase_ast; ?>"><?php echo $row['asistencia']; ?></span></td>
                            <td style="text-align: center;">
                                <a href="editar_carrito.php?id=<?php echo $row['id']; ?>" class="btn-edit">
                                    <i class="fas fa-edit"></i> Modificar
                                </a>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    echo "<tr><td colspan='6' style='text-align:center; padding:50px; color: #94a3b8;'>No hay datos que coincidan con la búsqueda.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>