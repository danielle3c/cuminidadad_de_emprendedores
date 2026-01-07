<?php 
include 'config.php'; 

$mensaje = "";

// 1. OBTENER LA CONFIGURACIÓN ACTUAL
$consulta = mysqli_query($conexion, "SELECT * FROM configuraciones WHERE id = 1");
$cfg = mysqli_fetch_assoc($consulta);

// 2. PROCESAR LA ACTUALIZACIÓN
if (isset($_POST['actualizar'])) {
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre_sistema']);
    $email  = mysqli_real_escape_string($conexion, $_POST['email_remitente']);
    $pass   = mysqli_real_escape_string($conexion, $_POST['password_email']);
    $tema   = mysqli_real_escape_string($conexion, $_POST['tema_color']);
    $idioma = mysqli_real_escape_string($conexion, $_POST['idioma']);

    $sql = "UPDATE configuraciones SET 
            nombre_sistema = '$nombre', 
            email_remitente = '$email', 
            password_email = '$pass', 
            tema_color = '$tema', 
            idioma = '$idioma' 
            WHERE id = 1";

    if (mysqli_query($conexion, $sql)) {
        $mensaje = "<div class='alert success'>✅ Configuración guardada correctamente.</div>";
        // Recargar los datos actualizados
        $consulta = mysqli_query($conexion, "SELECT * FROM configuraciones WHERE id = 1");
        $cfg = mysqli_fetch_assoc($consulta);
    } else {
        $mensaje = "<div class='alert error'>❌ Error al guardar: " . mysqli_error($conexion) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es" data-theme="<?php echo $cfg['tema_color']; ?>">
<head>
    <meta charset="UTF-8">
    <title>Panel de Configuración</title>
    <style>
        /* Variables de Color Dinámicas */
        :root { --bg: #f4f7f6; --text: #333; --card: #fff; --primary: #43b02a; }
        [data-theme="dark"] { --bg: #1a1a1a; --text: #f0f0f0; --card: #2d2d2d; --primary: #2ecc71; }
        [data-theme="blue"] { --bg: #e0e6ed; --text: #1a2a3a; --card: #fff; --primary: #0056b3; }

        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); color: var(--text); transition: 0.3s; padding: 20px; }
        .container { max-width: 600px; margin: auto; background: var(--card); padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        
        h2 { text-align: center; border-bottom: 2px solid var(--primary); padding-bottom: 10px; }
        label { display: block; margin-top: 15px; font-weight: bold; font-size: 0.9em; }
        input, select { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; background: var(--card); color: var(--text); }
        
        .btn-save { background: var(--primary); color: white; border: none; padding: 15px; width: 100%; border-radius: 8px; cursor: pointer; font-weight: bold; margin-top: 25px; font-size: 1em; }
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; }
        .success { background: #dcfce7; color: #166534; }
        .error { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>

<div class="container">
    <h2>⚙️ Configuración del Sistema</h2>
    <?php echo $mensaje; ?>

    <form method="POST">
        <label>Nombre de la Aplicación:</label>
        <input type="text" name="nombre_sistema" value="<?php echo $cfg['nombre_sistema']; ?>" required>

        <label>Correo Emisor (Gmail):</label>
        <input type="email" name="email_remitente" value="<?php echo $cfg['email_remitente']; ?>" placeholder="ejemplo@gmail.com">

        <label>Password de Aplicación (SMTP):</label>
        <input type="password" name="password_email" value="<?php echo $cfg['password_email']; ?>" placeholder="16 letras de Google">

        <label>Tema Visual:</label>
        <select name="tema_color">
            <option value="light" <?php if($cfg['tema_color'] == 'light') echo 'selected'; ?>>☀️ Claro</option>
            <option value="dark" <?php if($cfg['tema_color'] == 'dark') echo 'selected'; ?>>🌙 Oscuro</option>
            <option value="blue" <?php if($cfg['tema_color'] == 'blue') echo 'selected'; ?>>🔹 Azul</option>
        </select>

        <label>Idioma:</label>
        <select name="idioma">
            <option value="es" <?php if($cfg['idioma'] == 'es') echo 'selected'; ?>>Español</option>
            <option value="en" <?php if($cfg['idioma'] == 'en') echo 'selected'; ?>>English</option>
        </select>

        <button type="submit" name="actualizar" class="btn-save">Guardar Cambios</button>
    </form>
    <p style="text-align: center;"><a href="usuarios_lista.php" style="color: var(--primary); text-decoration: none;">⬅️ Volver al Panel</a></p>
</div>

</body>
</html>