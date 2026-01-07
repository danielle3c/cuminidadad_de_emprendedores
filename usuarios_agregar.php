<?php 
include 'config.php'; 

$mensaje = "";

if (isset($_POST['crear_usuario'])) {
    $persona_id = mysqli_real_escape_string($conexion, $_POST['persona_id']);
    $username   = mysqli_real_escape_string($conexion, $_POST['username']);
    $password   = $_POST['password'];
    $estado     = $_POST['estado'];

    // 1. Verificar si la persona existe
    $check_p = mysqli_query($conexion, "SELECT idpersonas FROM personas WHERE idpersonas = '$persona_id'");
    
    // 2. Verificar si el nombre de usuario ya existe
    $check_u = mysqli_query($conexion, "SELECT idUsuarios FROM Usuarios WHERE username = '$username'");

    if (mysqli_num_rows($check_p) == 0) {
        $mensaje = "<p style='color:red;'>❌ Error: El ID de persona no existe.</p>";
    } elseif (mysqli_num_rows($check_u) > 0) {
        $mensaje = "<p style='color:red;'>❌ Error: El nombre de usuario '$username' ya está en uso.</p>";
    } else {
        // Encriptar contraseña
        $pass_enc = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO Usuarios (username, password, estado, personas_idpersonas, created_at) 
                VALUES ('$username', '$pass_enc', '$estado', '$persona_id', NOW())";

        if (mysqli_query($conexion, $sql)) {
            $mensaje = "<p style='color:green;'>✅ Usuario creado exitosamente.</p>";
            header("Refresh:2; url=usuarios_lista.php");
        } else {
            $mensaje = "<p style='color:red;'>❌ Error al insertar: " . mysqli_error($conexion) . "</p>";
        }
    }
}

// Obtener lista de personas que aún NO tienen usuario para el desplegable
$personas_libres = mysqli_query($conexion, "SELECT p.idpersonas, p.nombres, p.apellidos 
                                            FROM personas p 
                                            LEFT JOIN Usuarios u ON p.idpersonas = u.personas_idpersonas 
                                            WHERE u.idUsuarios IS NULL AND p.deleted_at = 0 
                                            ORDER BY p.nombres ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Nuevo Usuario</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; padding: 20px; }
        .form-box { max-width: 450px; margin: auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .nav-bar { background: #43b02a; padding: 15px; text-align: center; border-radius: 8px; margin-bottom: 25px; }
        .nav-bar a { color: white; text-decoration: none; font-weight: bold; }
        h2 { color: #333; text-align: center; }
        label { font-weight: bold; display: block; margin-top: 15px; font-size: 0.9em; }
        input, select { width: 100%; padding: 12px; margin: 5px 0; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        .btn-save { background: #43b02a; color: white; border: none; padding: 15px; width: 100%; border-radius: 8px; cursor: pointer; font-weight: bold; margin-top: 25px; font-size: 1em; }
        .btn-save:hover { background: #369122; }
    </style>
</head>
<body>

<div class="nav-bar">
    <a href="usuarios_lista.php">⬅️ Volver a Lista de Usuarios</a>
</div>

<div class="form-box">
    <h2>👥 Registrar Nuevo Usuario</h2>
    <?php echo $mensaje; ?>

    <form method="POST">
        <label>Seleccionar Persona:</label>
        <select name="persona_id" required>
            <option value="">-- Seleccione una persona --</option>
            <?php while($p = mysqli_fetch_assoc($personas_libres)): ?>
                <option value="<?php echo $p['idpersonas']; ?>">
                    <?php echo $p['nombres'] . " " . $p['apellidos'] . " (ID: " . $p['idpersonas'] . ")"; ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Nombre de Usuario (Login):</label>
        <input type="text" name="username" placeholder="Ej: jperez" required>

        <label>Contraseña:</label>
        <input type="password" name="password" placeholder="********" required>

        <label>Estado de Acceso:</label>
        <select name="estado">
            <option value="1">Activo (Puede entrar al sistema)</option>
            <option value="0">Inactivo (Acceso bloqueado)</option>
        </select>

        <button type="submit" name="crear_usuario" class="btn-save">Crear Usuario de Sistema</button>
    </form>
</div>

</body>
</html>