<?php 
include 'config.php'; 
$res_conf = mysqli_query($conexion, "SELECT * FROM configuraciones WHERE id = 1");
$cfg = mysqli_fetch_assoc($res_conf);
?>
<!DOCTYPE html>
<html lang="es" data-theme="<?php echo $cfg['tema_color']; ?>">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Carritos | <?php echo $cfg['nombre_sistema']; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --bg: #f8fafc; --card: #ffffff; --text: #1e293b; --primary: #43b02a; --border: #e2e8f0; }
        [data-theme="dark"] { --bg: #0f172a; --card: #1e293b; --text: #f1f5f9; --primary: #2ecc71; --border: #334155; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); padding: 30px; margin: 0; }
        .container { max-width: 1100px; margin: auto; }
        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .btn-add { background: var(--primary); color: white; padding: 12px 24px; border-radius: 12px; text-decoration: none; font-weight: bold; transition: 0.3s; box-shadow: 0 4px 12px rgba(67, 176, 42, 0.2); }
        .card-table { background: var(--card); border-radius: 20px; border: 1px solid var(--border); overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; }
        th { background: rgba(0,0,0,0.02); padding: 15px; text-align: left; font-size: 0.8rem; text-transform: uppercase; color: var(--primary); border-bottom: 2px solid var(--border); }
        td { padding: 15px; border-bottom: 1px solid var(--border); font-size: 0.95rem; }
        .badge { padding: 5px 12px; border-radius: 8px; font-weight: bold; font-size: 0.75rem; }
        .badge-si { background: #dcfce7; color: #166534; }
        .badge-no { background: #fee2e2; color: #991b1b; }
        .actions { display: flex; gap: 8px; }
        .btn-tool { width: 35px; height: 35px; border-radius: 8px; display: flex; align-items: center; justify-content: center; text-decoration: none; color: white; transition: 0.2s; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-flex">
            <h1><i class="fas fa-shopping-cart" style="color:var(--primary)"></i> Asistencia de Carritos</h1>
            <a href="registro_carrito.php" class="btn-add"><i class="fas fa-plus"></i> Nuevo Registro</a>
        </div>
        <div class="card-table">
            <table>
                <thead>
                    <tr>
                        <th>Responsable</th>
                        <th>Carrito</th>
                        <th>Estado</th>
                        <th>Ingreso / Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM carritos ORDER BY created_at DESC";
                    $res = mysqli_query($conexion, $sql);
                    while($row = mysqli_fetch_assoc($res)):
                        $badge = ($row['asistencia'] == 'SÍ VINO') ? 'badge-si' : 'badge-no';
                    ?>
                    <tr>
                        <td><strong><?php echo $row['nombre_responsable']; ?></strong></td>
                        <td><?php echo $row['nombre_carrito']; ?></td>
                        <td><span class="badge <?php echo $badge; ?>"><?php echo $row['asistencia']; ?></span></td>
                        <td><?php echo date('d/m, H:i', strtotime($row['created_at'])); ?></td>
                        <td class="actions">
                            <a href="ver_historial.php?nombre=<?php echo urlencode($row['nombre_responsable']); ?>" class="btn-tool" style="background:#3498db" title="Historial"><i class="fas fa-history"></i></a>
                            <a href="editar_carrito.php?id=<?php echo $row['id']; ?>" class="btn-tool" style="background:#f1c40f" title="Editar"><i class="fas fa-edit"></i></a>
                            <a href="eliminar_carrito.php?id=<?php echo $row['id']; ?>" class="btn-tool" style="background:#e74c3c" onclick="return confirm('¿Borrar registro?')" title="Eliminar"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>