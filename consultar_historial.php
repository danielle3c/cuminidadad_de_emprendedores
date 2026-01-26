<?php
include 'config.php';
$buscar = isset($_GET['buscar']) ? mysqli_real_escape_string($conexion, $_GET['buscar']) : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial General - Escuela de Verano</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'DM Sans', sans-serif; background: #f4f7fe; padding: 20px; color: #2b3674; }
        .container { max-width: 900px; margin: auto; background: white; padding: 30px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th { background: #111c44; color: white; padding: 12px; text-align: left; }
        .table td { padding: 12px; border-bottom: 1px solid #f4f7fe; }
        .btn-view { background: #f1c40f; color: black; padding: 8px 12px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 0.8rem; }
        .search-input { padding: 10px; width: 300px; border-radius: 10px; border: 1px solid #ddd; }
    </style>
</head>
<body>
<div class="container">
    <div style="display:flex; justify-content:space-between; align-items:center;">
        <h1><i class="fas fa-history"></i> Historial de Escuela</h1>
        <a href="digitalizar_escuela.php" style="text-decoration:none; color:#422afb; font-weight:bold;"><i class="fas fa-plus"></i> Nueva Digitalización</a>
    </div>

    <form method="GET" style="margin: 20px 0;">
        <input type="text" name="buscar" class="search-input" placeholder="Buscar por nombre..." value="<?php echo $buscar; ?>">
        <button type="submit" style="padding:10px 20px; background:#422afb; color:white; border:none; border-radius:10px; cursor:pointer;">Buscar</button>
    </form>

    <table class="table">
        <thead>
            <tr>
                <th>ID Excel</th>
                <th>Nombre Emprendedor</th>
                <th>Emprendimiento</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM emprendedores WHERE nombre LIKE '%$buscar%' ORDER BY id_emprendedor ASC";
            $res = mysqli_query($conexion, $sql);
            
            if(mysqli_num_rows($res) > 0) {
                while($row = mysqli_fetch_assoc($res)) {
                    echo "<tr>
                            <td><strong>#{$row['id_emprendedor']}</strong></td>
                            <td>{$row['nombre']}</td>
                            <td>{$row['emprendimiento']}</td>
                            <td><a href='ver_historial_escuela.php?id={$row['id_emprendedor']}' class='btn-view'><i class='fas fa-eye'></i> VER REPORTE</a></td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='4' style='text-align:center;'>No hay registros encontrados.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
</body>
</html>