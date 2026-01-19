<?php
include 'config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$mensaje = "";

// 1. Cargar configuración visual
$res_conf = mysqli_query($conexion, "SELECT * FROM configuraciones WHERE id = 1");
$cfg = mysqli_fetch_assoc($res_conf);

// 2. Lógica para GUARDAR (Insertar)
if (isset($_POST['guardar'])) {
    $fecha       = $_POST['fecha'];
    $local       = mysqli_real_escape_string($conexion, $_POST['local']);
    $direccion   = mysqli_real_escape_string($conexion, $_POST['direccion']);
    $rep         = mysqli_real_escape_string($conexion, $_POST['representante']);
    $telefono    = mysqli_real_escape_string($conexion, $_POST['telefono']);
    $necesidades = mysqli_real_escape_string($conexion, $_POST['necesidades']);
    $participa   = $_POST['participa'];
    $beneficio   = mysqli_real_escape_string($conexion, $_POST['beneficio']);
    $obs         = mysqli_real_escape_string($conexion, $_POST['observaciones']);
    $usuario_id  = $_SESSION['usuario_id'] ?? 1;

    $sql = "INSERT INTO encuesta_2026 (
        fecha_encuesta, nombre_local, direccion, representante, telefono,
        necesidades_productivas, participa_programa_beneficios, beneficio_ofrecido,
        observaciones, created_at, created_by
    ) VALUES (
        '$fecha','$local','$direccion','$rep','$telefono',
        '$necesidades','$participa','$beneficio',
        '$obs', NOW(), '$usuario_id'
    )";

    if (mysqli_query($conexion, $sql)) {
        $mensaje = "<div style='background:#def7ec; color:#03543f; padding:15px; border-radius:10px; margin-bottom:20px;'>✅ Encuesta guardada en la tabla encuesta_2026</div>";
    } else {
        $mensaje = "<div style='background:#fde8e8; color:#9b1c1c; padding:15px; border-radius:10px; margin-bottom:20px;'>❌ Error de MySQL: ".mysqli_error($conexion)."</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es" data-theme="<?php echo $cfg['tema_color']; ?>">
<head>
    <meta charset="UTF-8">
    <title>Nueva Encuesta 2026</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --bg: #f4f7fe; --card: #ffffff; --text: #2b3674; --primary: #55b83e; --border: #e0e5f2; }
        [data-theme="dark"] { --bg: #0b1437; --card: #111c44; --text: #ffffff; --primary: #2ecc71; --border: #1b254b; }
        body { font-family: 'DM Sans', sans-serif; background: var(--bg); color: var(--text); padding: 20px; }
        .form-container { max-width: 600px; margin: 0 auto; background: var(--card); padding: 30px; border-radius: 20px; border: 1px solid var(--border); box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        input, textarea, select { width: 100%; padding: 12px; margin-bottom: 15px; border-radius: 10px; border: 1px solid var(--border); background: var(--bg); color: var(--text); box-sizing: border-box; }
        .btn-save { background: var(--primary); color: white; border: none; padding: 15px; width: 100%; border-radius: 12px; font-weight: bold; cursor: pointer; }
        .btn-back { display: inline-block; margin-bottom: 20px; text-decoration: none; color: var(--text); font-weight: bold; }
    </style>
</head>
<body>

<div class="form-container">
    <a href="index.php" class="btn-back"><i class="fas fa-arrow-left"></i> Volver al Panel</a>
    <h2><i class="fas fa-poll-h"></i> Registro Encuesta 2026</h2>
    <?php echo $mensaje; ?>
    
    <form method="POST">
        <label>Fecha</label>
        <input type="date" name="fecha" value="<?php echo date('Y-m-d'); ?>" required>
        
        <input type="text" name="local" placeholder="Nombre del Local" required>
        <input type="text" name="direccion" placeholder="Dirección" required>
        <input type="text" name="representante" placeholder="Nombre del Representante" required>
        <input type="text" name="telefono" placeholder="Teléfono">
        
        <textarea name="necesidades" placeholder="Necesidades Productivas" rows="3"></textarea>
        
        <label>¿Participa en Programa?</label>
        <select name="participa">
            <option value="1">Sí</option>
            <option value="0">No</option>
        </select>
        
        <textarea name="beneficio" placeholder="Beneficio Ofrecido" rows="2"></textarea>
        <textarea name="observaciones" placeholder="Observaciones" rows="2"></textarea>
        
        <button name="guardar" class="btn-save">GUARDAR EN BASE DE DATOS</button>
    </form>
</div>

</body>
</html>