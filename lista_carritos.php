<?php 
include 'config.php'; 

// Obtener configuración de tema
$res_conf = mysqli_query($conexion, "SELECT * FROM configuraciones WHERE id = 1");
$cfg = mysqli_fetch_assoc($res_conf);
?>
<!DOCTYPE html>
<html lang="es" data-theme="<?php echo $cfg['tema_color']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Asistencia | <?php echo $cfg['nombre_sistema']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --bg: #f8fafc; --card: #ffffff; --text: #1e293b; --primary: #43b02a; --border: #e2e8f0; }
        [data-theme="dark"] { --bg: #0f172a; --card: #1e293b; --text: #f1f5f9; --primary: #2ecc71; --border: #334155; }

        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); padding: 20px; }
        .container { max-width: 1000px; margin: auto; }
        
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .btn-back { background: var(--primary); color: white; padding: 10px 20px; border-radius: 10px; text-decoration: none; font-weight: 600; }

        .card-table { background: var(--card); border-radius: 15px; border: 1px solid var(--border); overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        
        table { width: 100%; border-collapse: collapse; }
        th { background: rgba(0,0,0,0.02); padding: 15px; text-align: left; font-size: 0.8rem; text-transform: uppercase; border-bottom: 2px solid var(--border); }
        td { padding: 15px; border-bottom: 1px solid var(--border); font-size: 0.9rem; }
        
        /* Estilos de Asistencia */
        .status { padding: 5px 10px; border-radius: 20px; font-weight: 700; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 5px; }
        .status-si { background: rgba(34, 197, 94, 0.15); color: #22c55e; }
        .status-no { background: rgba(239, 68, 68, 0.15); color: #ef4444; }

        .date-text { font-weight: 600; color: var(--text); }
        .time-text { font-size: 0.8rem; opacity: 0.6; }
        
        tr:hover { background: rgba(0,0,0,0.02); }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>📋 Historial de Carritos y Asistencia</h2>
        <a href="carritos.php" class="btn-back">+ Nuevo Registro</a>
    </div>

    <div class="card-table">
        <table>
            <thead>
                <tr>
                    <th>Fecha y Hora</th>
                    <th>Emprendedor</th>
                    <th>Carrito / Puesto</th>
                    <th>Asistencia</th>
                    <th>Detalles</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Consulta que une carritos con emprendedores y personas
                $query = "SELECT c.*, p.nombres, p.apellidos 
                          FROM carritos c
                          JOIN emprendedores e ON c.emprendedores_idemprendedores = e.idemprendedores
                          JOIN personas p ON e.personas_idpersonas = p.idpersonas
                          ORDER BY c.created_at DESC"; // Los más nuevos primero

                $res = mysqli_query($conexion, $query);

                while($row = mysqli_fetch_assoc($res)) {
                    $fecha = date('d/m/Y', strtotime($row['created_at']));
                    $hora = date('H:i', strtotime($row['created_at']));
                    
                    // Clase según asistencia
                    $clase_status = ($row['asistencia'] == 'SÍ VINO') ? 'status-si' : 'status-no';
                    $icono = ($row['asistencia'] == 'SÍ VINO') ? 'fa-check-circle' : 'fa-times-circle';
                ?>
                <tr>
                    <td>
                        <span class="date-text"><?php echo $fecha; ?></span><br>
                        <span class="time-text"><i class="far fa-clock"></i> <?php echo $hora; ?></span>
                    </td>
                    <td><strong><?php echo $row['nombres'] . " " . $row['apellidos']; ?></strong></td>
                    <td><?php echo $row['nombre_carrito']; ?></td>
                    <td>
                        <span class="status <?php echo $clase_status; ?>">
                            <i class="fas <?php echo $icono; ?>"></i>
                            <?php echo $row['asistencia']; ?>
                        </span>
                    </td>
                    <td style="font-size: 0.8rem; max-width: 200px; opacity: 0.8;">
                        <strong>Estado:</strong> <?php echo $row['descripcion']; ?><br>
                        <strong>Equip:</strong> <?php echo $row['equipamiento']; ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    
    <p style="text-align: center; margin-top: 20px;">
        <a href="index.php" style="color: var(--text); opacity: 0.6; text-decoration: none;">Volver al Inicio</a>
    </p>
</div>

</body>
</html>