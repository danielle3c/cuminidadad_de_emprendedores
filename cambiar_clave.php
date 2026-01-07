<?php 
include 'config.php'; 

$mensaje = "";

if (isset($_POST['recuperar'])) {
    $correo = mysqli_real_escape_string($conexion, $_POST['correo']);

    // 1. Verificar si el correo existe en la base de datos
    $sql = "SELECT * FROM Usuarios WHERE correo_institucional = '$correo' AND estado = 1";
    $res = mysqli_query($conexion, $sql);

    if ($f = mysqli_fetch_assoc($res)) {
        $username = $f['username'];
        
        // 2. Generar un código temporal (Token) para el cambio
        $token = bin2hex(random_bytes(10)); 
        
        // Aquí guardarías el token en la BD si quisieras un link de un solo uso
        // Por ahora, simularemos el envío:
        
        $mensaje = "<div style='color:#166534; background:#dcfce7; padding:15px; border-radius:8px;'>
                        ✅ <strong>¡Correo Enviado!</strong><br>
                        Se ha enviado un enlace de recuperación al correo: <strong>$correo</strong>.<br>
                        <small>Revisa tu bandeja de entrada o spam.</small>
                    </div>";
        
        /* LOGICA DE ENVÍO REAL (Requiere PHPMailer):
        mail($correo, "Recuperar Contraseña", "Hola $username, haz clic aquí para cambiar tu clave: http://tuweb.com/reset.php?token=$token");
        */
        
    } else {
        $mensaje = "<p style='color:#b91c1c; background:#fee2e2; padding:10px; border-radius:8px;'>
                    ❌ El correo institucional no está registrado o la cuenta está inactiva.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .form-box { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); width: 100%; max-width: 400px; text-align: center; }
        .nav-bar-mini { margin-bottom: 20px; text-align: left; }
        .nav-bar-mini a { color: #43b02a; text-decoration: none; font-weight: bold; font-size: 0.9em; }
        h2 { color: #333; margin-bottom: 10px; }
        p { color: #666; font-size: 0.9em; margin-bottom: 25px; }
        label { display: block; text-align: left; font-weight: bold; margin-bottom: 5px; color: #444; }
        input[type="email"] { width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; box-sizing: border-box; font-size: 16px; margin-bottom: 20px; }
        .btn-send { background: #43b02a; color: white; border: none; padding: 14px; width: 100%; border-radius: 8px; cursor: pointer; font-weight: bold; font-size: 16px; }
        .btn-send:hover { background: #369122; }
    </style>
</head>
<body>

<div class="form-box">
    <div class="nav-bar-mini">
        <a href="login.php">⬅️ Volver al Login</a>
    </div>

    <h2>¿Olvidaste tu clave?</h2>
    <p>Introduce tu correo institucional y te enviaremos las instrucciones para restablecerla.</p>

    <?php echo $mensaje; ?>

    <?php if(!isset($_POST['recuperar']) || strpos($mensaje, '❌') !== false): ?>
    <form method="POST">
        <label>Correo Institucional:</label>
        <input type="email" name="correo" placeholder="usuario@institucion.cl" required>
        <button type="submit" name="recuperar" class="btn-send">Enviar enlace de acceso</button>
    </form>
    <?php endif; ?>
</div>

</body>
</html>