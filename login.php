<?php
include 'config.php';

if (isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ingresar'])) {
    $user = mysqli_real_escape_string($conexion, $_POST['username']);
    $pass = $_POST['password'];

    $sql = "SELECT * FROM Usuarios WHERE username = '$user' AND estado = 1";
    $res = mysqli_query($conexion, $sql);

    if ($f = mysqli_fetch_assoc($res)) {
        if (password_verify($pass, $f['password'])) {
            $_SESSION['usuario_id'] = $f['idUsuarios'];
            $_SESSION['username'] = $f['username'];
            header("Location: index.php");
            exit();
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "Usuario no encontrado o inactivo.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acceso - Comunidad</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #43b02a; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-card { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); width: 320px; text-align: center; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        .btn-in { width: 100%; padding: 12px; background: #43b02a; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; margin-bottom: 10px; }
        .footer-links { margin-top: 15px; font-size: 0.85em; border-top: 1px solid #eee; padding-top: 15px; }
        .btn-link { color: #43b02a; text-decoration: none; font-weight: bold; display: block; margin: 8px 0; }
        .btn-link:hover { text-decoration: underline; }
        .forgot-pass { color: #666; font-size: 0.8em; text-decoration: none; display: block; margin-top: 5px; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>Comunidad</h2>
        <?php if($error) echo "<p style='color:red; font-size:0.9em;'>$error</p>"; ?>
        
        <form method="POST">
            <input type="text" name="username" placeholder="Usuario" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit" name="ingresar" class="btn-in">Entrar</button>
            <a href="cambiar_clave.php" class="btn-link">¿Olvidaste o quieres cambiar tu contraseña?</a>
        </form>
        
        <div class="footer-links">
            <p style="margin-bottom: 5px;">¿No tienes cuenta?</p>
            <a href="usuarios_agregar.php" class="btn-link">Crear nueva cuenta</a>
        </div>
    </div>
</body>
</html>