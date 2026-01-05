<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Personas</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; padding: 20px; }
        .form-container { background: white; padding: 25px; border-radius: 12px; max-width: 500px; margin: auto; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        input, select, textarea { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { background: #2563eb; color: white; border: none; padding: 12px; width: 100%; border-radius: 8px; cursor: pointer; width: 100%; font-weight: bold; }
        label { font-weight: bold; color: #444; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>👤 Registro de Persona</h2>
    <form method="POST">
        <label>Nombres:</label>
        <input type="text" name="nombres" placeholder="Ej: Juan Pedro" required>
        
        <label>Apellidos:</label>
        <input type="text" name="apellidos" placeholder="Ej: Pérez García" required>
        
        <label>Género:</label>
        <select name="genero">
            <option value="Masculino">Masculino</option>
            <option value="Femenino">Femenino</option>
            <option value="Otro">Otro</option>
        </select>

        <label>Fecha de Nacimiento:</label>
        <input type="date" name="fecha_nacimiento" required>
        
        <label>Teléfono:</label>
        <input type="text" name="telefono" placeholder="Ej: 0987654321">
        
        <label>Email:</label>
        <input type="email" name="email" placeholder="correo@ejemplo.com">

        <label>Dirección:</label>
        <textarea name="direccion" rows="2" placeholder="Calle, número y barrio..."></textarea>

        <button type="submit" name="guardar">Guardar Persona</button>
    </form>

    <?php
    if(isset($_POST['guardar'])){
        // Recogemos los datos del formulario
        $nom = mysqli_real_escape_string($conexion, $_POST['nombres']);
        $ape = mysqli_real_escape_string($conexion, $_POST['apellidos']);
        $gen = $_POST['genero'];
        $fec = $_POST['fecha_nacimiento'];
        $tel = mysqli_real_escape_string($conexion, $_POST['telefono']);
        $ema = mysqli_real_escape_string($conexion, $_POST['email']);
        $dir = mysqli_real_escape_string($conexion, $_POST['direccion']);

        // SQL corregido con nombres exactos de tu tabla 'personas'
        $sql = "INSERT INTO personas (nombres, apellidos, fecha_nacimiento, genero, telefono, direccion, email, created_at, estado) 
                VALUES ('$nom', '$ape', '$fec', '$gen', '$tel', '$dir', '$ema', NOW(), 1)";

        if(mysqli_query($conexion, $sql)){
            echo "<p style='color:green; text-align:center; margin-top:15px;'>✅ Persona registrada correctamente.</p>";
        } else {
            echo "<p style='color:red;'>❌ Error al guardar: " . mysqli_error($conexion) . "</p>";
        }
    }
    ?>
    <p style="text-align:center;"><a href="index.php" style="text-decoration:none; color:#2563eb;">⬅ Volver al Buscador</a></p>
</div>

</body>
</html>