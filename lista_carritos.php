<?php 
// 1. Incluimos la configuración y conexión
include 'config.php'; 

// 2. Verificamos que la conexión exista (Si da error aquí, revisa tu config.php)
if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

// 3. Cargar configuración del tema
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
        .card-table { background: var(--card); border-radius: 15px; border: 1px solid var(--border); overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; }
        th { background: rgba(0,0,0,0.03); padding: 15px; text-align: left; color: var(--primary); font-size: 0.8rem; text-transform: uppercase; }
        td { padding: 15px; border-top: 1px solid var(--border); font-size: 0.9rem; }
        .badge { padding: 4px 8px; border-radius: 5px; font-size: 0.75rem; font-weight: bold; }
        .si { background: #dcfce7; color: #166534; }
        .no { background: #fee2e2; color: #991b1b; }
        .btn-volver { display: inline-block; margin-bottom: 20px; text-decoration: none; color: var(--primary); font-weight: bold; }
        .btn-edit { color: #3b82f6; transition: 0.3s; }
        .btn-edit:hover { color: #2563eb; transform: scale(1.1); }
    </style>
</head>
<body>

<div class="container">
    <a href="carritos.php" class="btn-volver"><i class="fas fa-arrow-left"></i> Volver al Registro</a>
    
    <div class="card-table">
        <h2 style="padding: 20px; margin: 0; border-bottom: 1px solid var(--border);">📋 Historial de Entregas</h2>
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Responsable / Teléfono</th>
                    <th>Carrito</th>
                    <th>Asistencia</th>
                    <th>Detalles</th>
                    <th style="text-align: center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Usamos la variable $conexion que viene de config.php
                $query = "SELECT * FROM carritos ORDER BY created_at DESC";
                $res = mysqli_query($conexion, $query);

                if($res && mysqli_num_rows($res) > 0) {
                    while($row = mysqli_fetch_assoc($res)) {
                        $clase_ast = ($row['asistencia'] == 'SÍ VINO') ? 'si' : 'no';
                        ?>
                        <tr>
                            <td>
                                <strong><?php echo date("d/m/Y", strtotime($row['created_at'])); ?></strong><br>
                                <small style="opacity: 0.6;"><?php echo date("H:i", strtotime($row['created_at'])); ?></small>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($row['nombre_responsable']); ?></strong><br>
                                <small><i class="fas fa-phone"></i> <?php echo htmlspecialchars($row['telefono_responsable']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($row['nombre_carrito']); ?></td>
                            <td><span class="badge <?php echo $clase_ast; ?>"><?php echo $row['asistencia']; ?></span></td>
                            <td>
                                <small><strong>Estado:</strong> <?php echo htmlspecialchars($row['descripcion']); ?></small><br>
                                <small><strong>Equip:</strong> <?php echo htmlspecialchars($row['equipamiento']); ?></small>
                            </td>
                            <td style="text-align: center;">
                                <a href="editar_carrito.php?id=<?php echo $row['id']; ?>" class="btn-edit" title="Modificar">
                                    <i class="fas fa-edit fa-lg"></i>
                                </a>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    echo "<tr><td colspan='6' style='text-align:center; padding:40px; opacity:0.5;'>No hay registros guardados aún.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>