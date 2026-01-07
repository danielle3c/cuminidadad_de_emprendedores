<?php 
include 'config.php'; 

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conexion, $_GET['id']);
    $res = mysqli_query($conexion, "SELECT * FROM Usuarios WHERE idUsuarios = '$id'");
    $u = mysqli_fetch_assoc($res);
}

if (isset($_POST['update_user'])) {
    $user = mysqli_real_escape_string($conexion, $_POST['username']);
    $estado = $_POST['estado'];
    $pass = $_POST['password'];

    // Si el campo password no está vacío, la actualizamos (encriptada)
    if (!empty($pass)) {
        $pass_enc = password_hash($pass, PASSWORD_DEFAULT);
        $sql = "UPDATE Usuarios SET username='$user', password='$pass_enc', estado='$estado', updated_at=NOW() WHERE idUsuarios='$id'";
    } else {
        $sql = "UPDATE Usuarios SET username='$user', estado='$estado', updated_at=NOW() WHERE idUsuarios='$id'";
    }

    if (mysqli_query($conexion, $sql)) {
        echo "<p style='color:green;'>✅ Usuario actualizado correctamente.</p>";
        header("Refresh:2; url=usuarios_lista.php");
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Editar Usuario</title></head>
<body style="font-family: sans-serif; padding: 30px;">
    <div style="max-width: 400px; margin: auto; background: white; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
        <h3>Modificar Usuario ID: <?php echo $id; ?></h3>
        <form method="POST">
            <label>Nombre de Usuario:</label><br>
            <input type="text" name="username" value="<?php echo $u['username']; ?>" style="width:100%; padding:8px; margin: 10px 0;"><br>
            
            <label>Nueva Contraseña (dejar en blanco para no cambiar):</label><br>
            <input type="password" name="password" style="width:100%; padding:8px; margin: 10px 0;"><br>
            
            <label>Estado de Cuenta:</label><br>
            <select name="estado" style="width:100%; padding:8px; margin: 10px 0;">
                <option value="1" <?php if($u['estado'] == 1) echo 'selected'; ?>>Activo</option>
                <option value="0" <?php if($u['estado'] == 0) echo 'selected'; ?>>Bloqueado / Inactivo</option>
            </select><br><br>
            
            <button type="submit" name="update_user" style="width:100%; background:#43b02a; color:white; border:none; padding:10px; cursor:pointer;">Guardar Cambios</button>
        </form>
    </div>
</body>
</html>