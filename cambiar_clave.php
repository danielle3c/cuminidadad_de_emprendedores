<?php 
include 'config.php'; 

$mensaje = "";

if (isset($_POST['verificar_correo'])) {
    $correo = mysqli_real_escape_string($conexion, $_POST['correo']);
    
    // Verificamos si existe ese correo institucional
    $sql = "SELECT idUsuarios FROM Usuarios WHERE correo_institucional = '$correo' AND estado = 1";
    $res = mysqli_query($conexion, $sql);

    if (mysqli_num_rows($res) > 0) {
        // AQUÍ iría la lógica de enviar el correo real. 
        // Por ahora, simulamos que se envió:
        $mensaje = "<p style='color:green; background:#dcfce7; padding:10px; border-radius:8px;'>
                    ✅ Se ha enviado un código a tu correo institucional ($correo) para cambiar la clave.</p>";
    } else {
        $mensaje = "<p style='color:red;'>❌ El correo no está registrado en nuestro sistema.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Acceso</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .box { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 350px; text-align: center; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        .btn { background: #43b02a; color: white; border: none; padding: 12px; width: 100%; border-radius: 8px; cursor: pointer; font-weight: bold; }
        a { color: #43b02a; text-decoration: none; font-size: 0.9em; display: block; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="box">
        <h3>Recuperar Contraseña</h3>
        <?php echo $mensaje; ?>
        <form method="POST">
            <p style="font-size: 0.9em; color: #666;">Ingresa tu correo institucional para recibir instrucciones.</p>
            <input type="email" name="correo" placeholder="tu-correo@institucion.cl" required>
            <button type="submit" name="verificar_correo" class="btn">Enviar instrucciones</button>
        </form>
        <a href="login.php">⬅️ Volver al Login</a>
    </div>
</body>
</html>