<?php
session_start();
include 'config.php';

if (isset($_POST['ingresar'])) {
    $user = mysqli_real_escape_string($conexion, $_POST['username']);
    $pass = $_POST['password'];

    $sql = "SELECT * FROM Usuarios WHERE username = '$user' AND estado = 1";
    $res = mysqli_query($conexion, $sql);

    if ($f = mysqli_fetch_assoc($res)) {
        // Verificamos la contraseña (asumiendo que usaste password_hash al crearlos)
        if (password_verify($pass, $f['password'])) {
            $_SESSION['usuario_id'] = $f['idUsuarios'];
            $_SESSION['nombre'] = $f['username'];
            header("Location: index.php");
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
    <title>Acceso al Sistema</title>
    <style>
        body { font-family: sans-serif; background: #43b02a; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); width: 300px; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #43b02a; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2 style="text-align:center; color: #333;">Comunidad Login</h2>
        <?php if(isset($error)) echo "<p style='color:red; font-size:0.8em;'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Usuario" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit" name="ingresar">Entrar al Sistema</button>
        </form>
    </div>
</body>
</html>