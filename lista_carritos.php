<?php 
include 'config.php'; 

// 1. Configuración del sistema
$res_conf = mysqli_query($conexion, "SELECT * FROM configuraciones WHERE id = 1");
$cfg = mysqli_fetch_assoc($res_conf);
?>

<!DOCTYPE html>
<html lang="es" data-theme="<?php echo $cfg['tema_color']; ?>">
<head>
    <meta charset="UTF-8">
    <title>Historial de Turnos | <?php echo $cfg['nombre_sistema']; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --bg: #f8fafc; --card: #ffffff; --text: #1e293b; --primary: #43b02a; --border: #e2e8f0; }
        [data-theme="dark"] { --bg: #0f172a; --card: #1e293b; --text: #f1f5f9; --primary: #2ecc71; --border: #334155; }
        
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); padding: 20px; }
        .container { max-width: 1100px; margin: auto; }
        
        .card-table { background: var(--card); border-radius: 15px; border: 1px solid var(--border); overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; }
        th { background: rgba(0,0,0,0.03); padding: 15px; text-align: left; color: var(--primary); font-size: 0.75rem; text-transform: uppercase; border-bottom: 2px solid var(--border); }
        td { padding: 15px; border-bottom: 1px solid var(--border); font-size: 0.9rem; }
        
        /* Diseño del bloque de tiempo */
        .tiempo-tag { 
            background: rgba(67, 176, 42, 0.1); 
            color: var(--primary); 
            padding: 4px 8px; 
            border-radius: 6px; 
            font-weight: bold; 
            font-size: 0.85rem;
            display: inline-block;
        }
        
        .badge { padding: 5px 10px; border-radius: 6px; font-size: 0.7rem; font-weight: bold; }
        .si { background: #dcfce7; color: #166534; }
        .no { background: #fee2e2; color: #991b1b; }
        
        .btn-edit { color: #3b82f6; text-decoration: none; transition: 0.2s; }
        .btn-edit:hover { transform: scale(1.2); }
    </style>
</head>
<body>

<div class="container">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h2><i class="fas fa-list-ul"></i> Historial de Carritos</h2>
        <a href="carritos.php" style="background:var(--primary); color:white; padding:10px 20px; border-radius:10px; text-decoration:none; font-weight:bold; font-size:0.9rem;">+ Nuevo Turno</a>
    </div>

    <div class="card-table">
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Horario de Turno</th>
                    <th>Responsable</th>
                    <th>Carrito</th>
                    <th>Asistencia</th>
                    <th style="text-align:center;">Editar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM carritos ORDER BY created_at DESC";
                $res = mysqli_query($conexion, $sql);

                if(mysqli_num_rows($res) > 0) {
                    while($row = mysqli_fetch_assoc($res)) {
                        $clase_ast = ($row['asistencia'] == 'SÍ VINO') ? 'si' : 'no';
                        $fecha_dt = new DateTime($row['created_at']);
                        
                        // Extraemos el horario que guardamos en la descripción
                        // Nota: Si usas el código de carritos anterior, el horario ya viene en el texto
                        ?>
                        <tr>
                            <td><strong><?php echo $fecha_dt->format('d/m/Y'); ?></strong></td>
                            <td>
                                <span class="tiempo-tag">
                                    <i class="far fa-clock"></i> 
                                    <?php 
                                        // Aquí mostramos la hora que guardamos
                                        echo $fecha_dt->format('H:i'); 
                                    ?>
                                </span>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($row['nombre_responsable']); ?></strong>
                                <div style="font-size: 0.8rem; opacity: 0.7;"><?php echo htmlspecialchars($row['telefono_responsable']); ?></div>
                            </td>
                            <td><span style="background:#f1f5f9; padding:4px 8px; border-radius:5px; border:1px solid #e2e8f0;"><?php echo htmlspecialchars($row['nombre_carrito']); ?></span></td>
                            <td><span class="badge <?php echo $clase_ast; ?>"><?php echo $row['asistencia']; ?></span></td>
                            <td style="text-align:center;">
                                <a href="editar_carrito.php?id=<?php echo $row['id']; ?>" class="btn-edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    echo "<tr><td colspan='6' style='text-align:center; padding:40px;'>No hay turnos registrados.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>