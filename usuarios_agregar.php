<?php 
// ... (mismo inicio de config.php) ...

if (isset($_POST['crear_usuario'])) {
    $persona_id = $_POST['persona_id'];
    $username   = $_POST['username'];
    $correo     = mysqli_real_escape_string($conexion, $_POST['correo']); // NUEVO
    $password   = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $estado     = $_POST['estado'];

    // Validar que sea un correo institucional (ejemplo: que termine en @institucion.cl)
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "<p style='color:red;'>❌ Formato de correo inválido.</p>";
    } else {
        $sql = "INSERT INTO Usuarios (username, correo_institucional, password, estado, personas_idpersonas, created_at) 
                VALUES ('$username', '$correo', '$password', '$estado', '$persona_id', NOW())";
        
        if (mysqli_query($conexion, $sql)) {
            $mensaje = "<p style='color:green;'>✅ Usuario creado con correo institucional.</p>";
        }
    }
}
?>

<label>Correo Institucional:</label>
<input type="email" name="correo" placeholder="ejemplo@comunidad.cl" required>