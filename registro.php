    <?php
include 'config.php'; // Aquí ya está el session_start y la conexión

if (isset($_POST['registrar'])) {
    $persona_id = mysqli_real_escape_string($conexion, $_POST['id_persona']);
    $user = mysqli_real_escape_string($conexion, $_POST['username']);
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // 1. Verificar si la persona existe
    $check_p = mysqli_query($conexion, "SELECT idpersonas FROM personas WHERE idpersonas = '$persona_id'");
    
    // 2. Verificar si el nombre de usuario ya está ocupado
    $check_u = mysqli_query($conexion, "SELECT idUsuarios FROM Usuarios WHERE username = '$user'");

    if (mysqli_num_rows($check_p) == 0) {
        $error = "El ID de persona no existe.";
    } elseif (mysqli_num_rows($check_u) > 0) {
        $error = "El nombre de usuario ya está en uso.";
    } else {
        $sql = "INSERT INTO Usuarios (username, password, personas_idpersonas, estado, created_at) 
                VALUES ('$user', '$pass', '$persona_id', 1, NOW())";
        
        if (mysqli_query($conexion, $sql)) {
            echo "<script>alert('✅ Cuenta creada. Ya puedes iniciar sesión.'); window.location='login.php';</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Comunidad</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .reg-card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 350px; }
        .btn-save { width: 100%; padding: 12px; background: #43b02a; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; margin-top: 10px; }
        input { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
    </style>
</head>
<body>
    <div class="reg-card">
        <h2 style="text-align:center; color: #43b02a;">Crear Cuenta</h2>
        <?php if(isset($error)) echo "<p style='color:red; font-size:0.8em;'>$error</p>"; ?>
        
        <form method="POST">
            <label>ID de Persona (Debe estar registrada):</label>
            <input type="number" name="id_persona" placeholder="Ej: 15" required>
            
            <label>Elige Nombre de Usuario:</label>
            <input type="text" name="username" placeholder="juan.perez" required>
            
            <label>Contraseña Nueva:</label>
            <input type="password" name="password" placeholder="********" required>
            
            <button type="submit" name="registrar" class="btn-save">Registrar Trabajador</button>
        </form>
        <p style="text-align:center;"><a href="login.php" style="color:#666; font-size:0.8em;">Volver al login</a></p>
    </div>
</body>
</html>