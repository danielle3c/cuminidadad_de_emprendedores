<?php 
include 'config.php'; 

// Si no hay sesión, config.php ya lo redirigirá al login automáticamente

$mensaje = "";

if (isset($_POST['actualizar_clave'])) {
    $id_usuario = $_SESSION['usuario_id'];
    $clave_nueva = $_POST['nueva_password'];
    $clave_confirma = $_POST['confirma_password'];

    if ($clave_nueva !== $clave_confirma) {
        $mensaje = "<p style='color:red;'>❌ Las contraseñas nuevas no coinciden.</p>";
    } elseif (strlen($clave_nueva) < 4) {
        $mensaje = "<p style='color:red;'>❌ La contraseña debe tener al menos 4 caracteres.</p>";
    } else {
        // Encriptar la nueva contraseña
        $pass_enc = password_hash($clave_nueva, PASSWORD_DEFAULT);
        
        $sql = "UPDATE Usuarios SET password = '$pass_enc' WHERE idUsuarios = '$id_usuario'";

        if (mysqli_query($conexion, $sql)) {
            $mensaje = "<p style='color:green;'>✅ Contraseña actualizada correctamente.</p>";
        } else {
            $mensaje = "<p style='color:red;'>❌ Error al actualizar: " . mysqli_error($conexion) . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cambiar Contraseña</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; padding: 20px; }
        .form-box { max-width: 400px; margin: 50px auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .nav-bar { background: #43b02a; padding: 15px; text-align: center; border-radius: 8px; margin-bottom: 25px; }
        .nav-bar a { color: white; text-decoration: none; font-weight: bold; }
        h2 { color: #333; text-align: center; margin-bottom: 20px; }
        label { font-weight: bold; display: block; margin-top: 15px; }
        input { width: 100%; padding: 12px; margin: 5px 0; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        .btn-save { background: #43b02a; color: white; border: none; padding: 15px; width: 100%; border-radius: 8px; cursor: pointer; font-weight: bold; margin-top: 20px; }
    </style>
</head>
<body>

<div class="nav-bar">
    <a href="index.php">⬅️ Volver al Panel</a>
</div>

<div class="form-box">
    <h2>🔑 Cambiar Contraseña</h2>
    <?php echo $mensaje; ?>

    <form method="POST">
        <p style="font-size: 0.85em; color: #666;">Usuario: <strong><?php echo $_SESSION['username']; ?></strong></p>
        
        <label>Nueva Contraseña:</label>
        <input type="password" name="nueva_password" required placeholder="Escriba la nueva clave">

        <label>Confirmar Nueva Contraseña:</label>
        <input type="password" name="confirma_password" required placeholder="Repita la nueva clave">

        <button type="submit" name="actualizar_clave" class="btn-save">Actualizar Contraseña</button>
    </form>
</div>

</body>
</html>