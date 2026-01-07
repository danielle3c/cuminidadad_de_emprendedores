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
    <title>Historial de Carritos | <?php echo $cfg['nombre_sistema']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --bg: #f8fafc; --card: #ffffff; --text: #1e293b; --primary: #43b02a; --border: #e2e8f0; }
        [data-theme="dark"] { --bg: #0f172a; --card: #1e293b; --text: #f1f5f9; --primary: #2ecc71; --border: #334155; }
        
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); padding: 20px; }
        .container { max-width: 1100px; margin: auto; }
        
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .btn-add { background: var(--primary); color: white; padding: 12px 20px; border-radius: 10px; text-decoration: none; font-weight: 700; transition: 0.3s; }
        
        .card-table { background: var(--card); border-radius: 20px; border: 1px solid var(--border); overflow: hidden; box-shadow: 0 10px 15px rgba(0,0,0,0.05); }
        
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { background: rgba(0,0,0,0.02); padding: 15px; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; color: var(--primary); border-bottom: 2px solid var(--border); }
        td { padding: 15px; border-bottom: 1px solid var(--border); font-size: 0.95rem; }
        
        .badge { padding: 5px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; }
        .badge-si { background: rgba(34, 197, 94, 0.1); color: #22c55e; }
        .badge-no { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
        
        .phone { color: #3b82f6; font-size: 0.85rem; display: block; }
        .date { opacity: 0.6; font-size: 0.85rem; }
        
        tr:hover { background: rgba(0,0,0,0.01); }
        .empty { padding: 50px; text-align: center; opacity: 0.5; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1><i class="fas fa-history"></i> Historial de Carritos</h1>
        <a href="carritos.php" class="btn-add"><i class="fas fa-plus"></i> Nuevo Registro</a>
    </div>

    <div class="card-table">
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Responsable / Teléfono</th>
                    <th>Carrito</th>
                    <th>Asistencia</th>
                    <th>Estado y Equipamiento</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Ordenamos por fecha, los más recientes primero
                $query = "SELECT * FROM carritos ORDER BY created_at DESC";
                $result = mysqli_query($conexion, $query);

                if(mysqli_num_rows($result) > 0){
                    while($row = mysqli_fetch_assoc($result)){
                        $clase_asistencia = ($row['asistencia'] == 'SÍ VINO') ? 'badge-si' : 'badge-no';
                        ?>
                        <tr>
                            <td>
                                <span class="date"><?php echo date("d/m/Y", strtotime($row['created_at'])); ?></span><br>
                                <strong><?php echo date("H:i", strtotime($row['created_at'])); ?></strong>
                            </td>
                            <td>
                                <strong><?php echo $row['nombre_responsable']; ?></strong>
                                <span class="phone"><i class="fas fa-phone"></i> <?php echo $row['telefono_responsable']; ?></span>
                            </td>
                            <td><i class="fas fa-store"></i> <?php echo $row['nombre_carrito']; ?></td>
                            <td>
                                <span class="badge <?php echo $clase_asistencia; ?>">
                                    <?php echo $row['asistencia']; ?>
                                </span>
                            </td>
                            <td>
                                <small><strong>Estético:</strong> <?php echo $row['descripcion']; ?></small><br>
                                <small><strong>Equip:</strong> <?php echo $row['equipamiento']; ?></small>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    echo "<tr><td colspan='5' class='empty'>No hay registros encontrados.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    
    <p style="text-align:center; margin-top:20px;">
        <a href="index.php" style="text-decoration:none; color:var(--text); opacity:0.6; font-weight:600;">Volver al Panel Principal</a>
    </p>
</div>

</body>
</html>