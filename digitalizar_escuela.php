<?php
include 'config.php';

$buscar = isset($_GET['buscar']) ? mysqli_real_escape_string($conexion, $_GET['buscar']) : '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consulta de Historial - Escuela de Verano</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'DM Sans', sans-serif; background: #f4f7fe; padding: 40px; color: #2b3674; }
        .container { max-width: 900px; margin: auto; }
        .search-card { background: white; padding: 30px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-bottom: 30px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        
        .search-box { display: flex; gap: 10px; }
        input[type="text"] { flex: 1; padding: 15px; border: 1px solid #e0e5f2; border-radius: 12px; outline: none; }
        .btn-search { background: #55b83e; color: white; border: none; padding: 0 25px; border-radius: 12px; cursor: pointer; font-weight: bold; }
        
        .results-table { width: 100%; border-collapse: collapse; background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .results-table th { background: #111c44; color: white; padding: 15px; text-align: left; }
        .results-table td { padding: 15px; border-bottom: 1px solid #f4f7fe; }
        .btn-view { background: #f1c40f; color: black; padding: 8px 15px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 0.8rem; }
        .btn-view:hover { background: #d4ac0d; }
        .no-results { padding: 40px; text-align: center; color: #a3aed0; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1><i class="fas fa-file-invoice"></i> Historial Escuela de Verano</h1>
        <a href="index.php" style="text-decoration: none; color: #707eae; font-weight: bold;"><i class="fas fa-home"></i> Inicio</a>
    </div>

    <div class="search-card">
        <form method="GET" class="search-box">
            <input type="text" name="buscar" placeholder="Buscar por nombre o ID..." value="<?php echo htmlspecialchars($buscar); ?>" autofocus>
            <button type="submit" class="btn-search">BUSCAR</button>
        </form>
    </div>

    <?php if ($buscar != ''): ?>
        <table class="results-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Emprendedor</th>
                    <th>Negocio</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Consulta con JOIN para asegurar que el emprendedor tiene evaluación
                $sql = "SELECT e.id_emprendedor, e.nombre, e.emprendimiento 
                        FROM emprendedores e
                        INNER JOIN evaluaciones_notas n ON e.id_emprendedor = n.id_emprendedor
                        WHERE e.nombre LIKE '%$buscar%' OR e.id_emprendedor = '$buscar'
                        GROUP BY e.id_emprendedor";
                
                $res = mysqli_query($conexion, $sql);

                if (mysqli_num_rows($res) > 0):
                    while ($row = mysqli_fetch_assoc($res)):
                ?>
                    <tr>
                        <td><strong>#<?php echo $row['id_emprendedor']; ?></strong></td>
                        <td><?php echo $row['nombre']; ?></td>
                        <td><?php echo $row['emprendimiento']; ?></td>
                        <td>
                            <a href="ver_historial_escuela.php?id=<?php echo $row['id_emprendedor']; ?>" class="btn-view">
                                <i class="fas fa-eye"></i> VER REPORTE
                            </a>
                        </td>
                    </tr>
                <?php 
                    endwhile; 
                else: 
                ?>
                    <tr>
                        <td colspan="4" class="no-results">No se encontraron emprendedores con evaluaciones registradas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="no-results">
            <i class="fas fa-search fa-3x" style="margin-bottom: 15px; opacity: 0.2;"></i>
            <p>Ingresa un nombre para consultar su historial de evaluación.</p>
        </div>
    <?php endif; ?>
</div>

</body>
</html>