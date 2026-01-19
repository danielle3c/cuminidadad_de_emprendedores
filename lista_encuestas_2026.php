<?php
include 'config.php';
$res_conf = mysqli_query($conexion, "SELECT * FROM configuraciones WHERE id = 1");
$cfg = mysqli_fetch_assoc($res_conf);

// Consulta para traer todas las encuestas
$sql = "SELECT id, fecha_encuesta, nombre_local, representante, telefono FROM encuesta_2026 ORDER BY id DESC";
$resultado = mysqli_query($conexion, $sql);
?>

<!DOCTYPE html>
<html lang="es" data-theme="<?php echo $cfg['tema_color']; ?>">
<head>
    <meta charset="UTF-8">
    <title>Historial de Encuestas 2026</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --bg: #f4f7fe; --card: #ffffff; --text: #2b3674; --primary: #4318FF; --border: #e0e5f2; }
        [data-theme="dark"] { --bg: #0b1437; --card: #111c44; --text: #ffffff; --border: #1b254b; }
        
        body { font-family: 'DM Sans', sans-serif; background: var(--bg); color: var(--text); padding: 40px; }
        .container { max-width: 1000px; margin: 0 auto; }
        
        .table-card { background: var(--card); border-radius: 20px; padding: 20px; border: 1px solid var(--border); box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { text-align: left; padding: 15px; color: #a3aed0; font-size: 0.85rem; text-transform: uppercase; border-bottom: 1px solid var(--border); }
        td { padding: 15px; border-bottom: 1px solid var(--border); font-size: 0.95rem; }
        
        .btn-edit { color: var(--primary); text-decoration: none; font-weight: bold; padding: 8px 15px; border-radius: 10px; background: rgba(67, 24, 255, 0.1); }
        .btn-edit:hover { background: var(--primary); color: white; }
        
        .header-list { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    </style>
</head>
<body>

<div class="container">
    <div class="header-list">
        <div>
            <h1>Historial de Encuestas</h1>
            <p style="color: #a3aed0;">Registros históricos de las planillas 2026.</p>
        </div>
        <a href="encuesta_2026.php" style="text-decoration: none; background: var(--primary); color: white; padding: 12px 25px; border-radius: 12px; font-weight: bold;">
            <i class="fas fa-plus"></i> NUEVA ENCUESTA
        </a>
    </div>

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Local / Barrio</th>
                    <th>Representante</th>
                    <th>Teléfono</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($resultado)): ?>
                <tr>
                    <td><strong><?php echo date('d/m/Y', strtotime($row['fecha_encuesta'])); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['nombre_local']); ?></td>
                    <td><?php echo htmlspecialchars($row['representante']); ?></td>
                    <td><?php echo $row['telefono'] ?: '---'; ?></td>
                    <td>
                        <a href="editar_encuesta_2026.php?id=<?php echo $row['id']; ?>" class="btn-edit">
                            <i class="fas fa-edit"></i> EDITAR
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if(mysqli_num_rows($resultado) == 0): ?>
                    <tr><td colspan="5" style="text-align: center; padding: 40px; color: #a3aed0;">No hay encuestas registradas aún.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>