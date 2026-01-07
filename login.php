<?php
// 1. Iniciamos la sesión para que el sistema te reconozca
session_start();
include 'config.php';

// Si ya iniciaste sesión, te manda directo al panel principal
if (isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

$error = "";

// 2. Cuando presionas el botón "Entrar"
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ingresar'])) {
    
    // Limpiamos el correo y la contraseña de espacios vacíos
    $user = mysqli_real_escape_string($conexion, trim($_POST['username']));
    $pass = $_POST['password'];

    // 3. BUSCAMOS POR EMAIL (porque usas tu correo para entrar)
    // Verificamos que el estado sea 1 (Activo)
    $sql = "SELECT * FROM Usuarios WHERE email = '$user' AND estado = 1";
    $res = mysqli_query($conexion, $sql);

    // 4. Si encontramos al usuario...
    if ($f = mysqli_fetch_assoc($res)) {
        
        // 5. Verificamos si la contraseña coincide con la encriptada
        if (password_verify($pass, $f['password'])) {
            
            // ¡TODO BIEN! Guardamos tus datos en la sesión
            $_SESSION['usuario_id'] = $f['idUsuarios'];
            $_SESSION['username']   = $f['nombre']; 
            
            header("Location: index.php");
            exit();
        } else {
            // Si la contraseña no coincide
            $error = "La contraseña es incorrecta.";
        }
    } else {
        // Si el correo no existe o el estado sigue en 0
        $error = "Usuario no encontrado o cuenta inactiva.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso - Comunidad</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #43b02a; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-card { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); width: 320px; text-align: center; }
        h2 { color: #333; margin: 0 0 20px 0; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; outline: none; }
        input:focus { border-color: #43b02a; }
        .btn-in { width: 100%; padding: 12px; background: #43b02a; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; margin-top: 10px; font-size: 1rem; }
        .btn-in:hover { background: #369622; }
        .error-msg { color: #d32f2f; background: #ffebee; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 0.85rem; border: 1px solid #ffcdd2; }
        .footer-links { margin-top: 20px; font-size: 0.85em; border-top: 1px solid #eee; padding-top: 15px; }
        .btn-link { color: #43b02a; text-decoration: none; font-weight: bold; display: block; margin: 8px 0; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>Comunidad</h2>
        
        <?php if($error): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Correo electrónico" required autofocus>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit" name="ingresar" class="btn-in">Entrar</button>
            <a href="cambiar_clave.php" style="color: #777; font-size: 0.8em; text-decoration: none;">¿Olvidaste tu contraseña?</a>
        </form>
        
        <div class="footer-links">
            <p style="margin: 0 0 5px 0; color: #666;">¿No tienes cuenta?</p>
            <a href="usuarios_agregar.php" class="btn-link">Crear nueva cuenta</a>
        </div>
    </div>
</body>
</html>