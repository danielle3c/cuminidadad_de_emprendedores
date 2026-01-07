<?php 
include 'config.php'; 

// 1. Configuración del sistema
$res_conf = mysqli_query($conexion, "SELECT * FROM configuraciones WHERE id = 1");
$cfg = mysqli_fetch_assoc($res_conf);

// 2. Filtros
$search = isset($_GET['buscar']) ? mysqli_real_escape_string($conexion, $_GET['buscar']) : "";
$desde  = isset($_GET['desde']) ? mysqli_real_escape_string($conexion, $_GET['desde']) : "";
$hasta  = isset($_GET['hasta']) ? mysqli_real_escape_string($conexion, $_GET['hasta']) : "";
?>

<!DOCTYPE html>
<html lang="es" data-theme="<?php echo $cfg['tema_color']; ?>">
<head>
    <meta charset="UTF-8">
    <title>Historial de Carritos | <?php echo $cfg['nombre_sistema']; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --bg: #f8fafc; --card: #ffffff; --text: #1e293b; --primary: #43b02a; --border: #e2e8f0; }
        [data-theme="dark"] { --bg: #0f172a; --card: #1e293b; --text: #f1f5f9; --primary: #2ecc71; --border: #334155; }
        body { font-family: sans-serif; background: var(--bg); color: var(--text); padding: 20px; }
        .container { max-width: 1100px; margin: auto; }
        
        /* Estilo de la tabla y celdas */
        .card-table { background: var(--card); border-radius: 15px; border: 1px solid var(--border); overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; }
        th { background: rgba(0,0,0,0.03); padding: 15px; text-align: left; color: var(--primary); font-size: 0.8rem; text-transform: uppercase; }
        td { padding: 15px; border-top: 1px solid var(--border); }
        
        /* Estilo para la Fecha y Hora */
        .fecha-box { display: flex; flex-direction: column; }
        .fecha-dia { font-weight: bold; font-size: 0.95rem; }
        .fecha-hora { font-size: 0.8rem; color: #64748b; display: flex; align-items: center; gap: 4px; }
        
        .badge { padding: 5px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: bold; }
        .si { background: #dcfce7; color: #166534; }
        .no { background: #fee2e2; color: #991b1b; }
        
        .btn-edit { color: #3b82f6; text-decoration: none; font-size: 1.1rem; }
    </style>
</head>
<body>

<div class="container">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h2><i class="fas fa-history"></i> Historial de Entregas</h2>
        <a href="carritos.php" style="text-decoration:none; color:var(--primary); font-weight:bold;">+ Nuevo Registro</a>
    </div>

    <div class="card-table">
        <table>
            <thead>
                <tr>
                    <th>Fecha y Hora</th>
                    <th>Responsable</th>
                    <th>Carrito</th>
                    <th>Asistencia</th>
                    <th>Estado / Equipamiento</th>
                    <th style="text-align:center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Construcción de la consulta
                $where = " WHERE 1=1 ";
                if ($search != "") {
                    $where .= " AND (nombre_responsable LIKE '%$search%' OR nombre_carrito LIKE '%$search%') ";
                }
                if ($desde != "" && $hasta != "") {
                    $where .= " AND DATE(created_at) BETWEEN '$desde' AND '$hasta' ";
                }

                $sql = "SELECT * FROM carritos $where ORDER BY created_at DESC";
                $res = mysqli_query($conexion, $sql);

                if(mysqli_num_rows($res) > 0) {
                    while($row = mysqli_fetch_assoc($res)) {
                        $clase_ast = ($row['asistencia'] == 'SÍ VINO') ? 'si' : 'no';
                        // Separamos fecha y hora para el diseño
                        $fecha_dt = new DateTime($row['created_at']);
                        ?>
                        <tr>
                            <td>
                                <div class="fecha-box">
                                    <span class="fecha-dia"><?php echo $fecha_dt->format('d/m/Y'); ?></span>
                                    <span class="fecha-hora"><i class="far fa-clock"></i> <?php echo $fecha_dt->format('H:i'); ?> hs</span>
                                </div>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($row['nombre_responsable']); ?></strong><br>
                                <small style="opacity:0.7;"><?php echo htmlspecialchars($row['telefono_responsable']); ?></small>
                            </td>
                            <td><span style="background:rgba(0,0,0,0.05); padding:3px 8px; border-radius:4px;"><?php echo htmlspecialchars($row['nombre_carrito']); ?></span></td>
                            <td><span class="badge <?php echo $clase_ast; ?>"><?php echo $row['asistencia']; ?></span></td>
                            <td>
                                <small><strong>Estado:</strong> <?php echo htmlspecialchars($row['descripcion']); ?></small><br>
                                <small><strong>Equip:</strong> <?php echo htmlspecialchars($row['equipamiento']); ?></small>
                            </td>
                            <td style="text-align:center;">
                                <a href="editar_carrito.php?id=<?php echo $row['id']; ?>" class="btn-edit" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    echo "<tr><td colspan='6' style='text-align:center; padding:30px;'>No hay registros guardados.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>