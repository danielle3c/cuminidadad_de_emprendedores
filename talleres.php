<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Control de Talleres</title>
    <style>
        body { font-family: sans-serif; background: #f0fdf4; padding: 20px; }
        .box { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 800px; margin: auto; }
        .form-group { margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background: #166534; color: white; }
        .btn { background: #166534; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; }
    </style>
</head>
<body>
    <a href="index.php">← Volver al Panel</a>
    <div class="box">
        <h2>🎓 Registro de Talleres y Asistencia</h2>
        
        <form method="POST" style="border-bottom: 2px solid #eee; padding-bottom: 20px;">
            <input type="text" name="nombre_taller" placeholder="Nombre del Taller" required style="width:70%; padding:8px;">
            <input type="date" name="fecha" required style="padding:8px;">
            <button type="submit" name="crear_taller" class="btn">Crear Taller</button>
        </form>

        <?php
        if(isset($_POST['crear_taller'])){
            $nom = $_POST['nombre_taller']; $fec = $_POST['fecha'];
            mysqli_query($conexion, "INSERT INTO talleres (nombre, fecha, created_at) VALUES ('$nom', '$fec', NOW())");
            echo "<p style='color:green'>Taller creado. Ahora puedes marcar asistencia abajo.</p>";
        }
        ?>

        <h3>Lista de Asistencia Rápida</h3>
        <form method="POST">
            <table>
                <thead>
                    <tr>
                        <th>Emprendedor</th>
                        <th>Taller</th>
                        <th>¿Asistió?</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Buscamos emprendedores y talleres para cruzar datos
                    $emp = mysqli_query($conexion, "SELECT e.idemprendedores, p.nombres, p.apellidos FROM emprendedores e JOIN personas p ON e.personas_idpersonas = p.idpersonas");
                    $tal = mysqli_query($conexion, "SELECT idtalleres, nombre FROM talleres ORDER BY idtalleres DESC LIMIT 1");
                    $taller_actual = mysqli_fetch_assoc($tal);

                    if($taller_actual){
                        while($e = mysqli_fetch_assoc($emp)){
                            echo "<tr>
                                <td>{$e['nombres']} {$e['apellidos']}</td>
                                <td>{$taller_actual['nombre']}</td>
                                <td><input type='checkbox' name='asistio[]' value='{$e['idemprendedores']}'></td>
                            </tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
            <input type="hidden" name="id_taller" value="<?php echo $taller_actual['idtalleres']; ?>">
            <br>
            <button type="submit" name="guardar_asistencia" class="btn" style="background:#15803d;">Guardar Asistencia de Hoy</button>
        </form>
    </div>
</body>
</html>
