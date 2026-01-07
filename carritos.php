<?php 
include 'config.php'; 

$res_conf = mysqli_query($conexion, "SELECT * FROM configuraciones WHERE id = 1");
$cfg = mysqli_fetch_assoc($res_conf);

$mensaje = "";

if(isset($_POST['save_car'])){
    $ide = mysqli_real_escape_string($conexion, $_POST['emp_id']); 
    $nom = mysqli_real_escape_string($conexion, $_POST['nombre_c']); 
    $des = mysqli_real_escape_string($conexion, $_POST['desc']); 
    $equ = mysqli_real_escape_string($conexion, $_POST['equip']);
    $ast = mysqli_real_escape_string($conexion, $_POST['asistencia']); // Nuevo campo

    // Asegúrate de que tu tabla 'carritos' tenga la columna 'asistencia'
    $sql = "INSERT INTO carritos (nombre_carrito, descripcion, equipamiento, asistencia, emprendedores_idemprendedores, created_at) 
            VALUES ('$nom', '$des', '$equ', '$ast', '$ide', NOW())";

    if(mysqli_query($conexion, $sql)){
        $mensaje = "<div class='alert success'>¡Registro Guardado! El historial se ha actualizado correctamente.</div>";
    } else {
        $mensaje = "<div class='alert error'> Error: " . mysqli_error($conexion) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es" data-theme="<?php echo $cfg['tema_color']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro | <?php echo $cfg['nombre_sistema']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --bg: #f8fafc; --card: #ffffff; --text: #1e293b; --primary: #43b02a; --border: #e2e8f0; --input-bg: #ffffff; }
        [data-theme="dark"] { --bg: #0f172a; --card: #1e293b; --text: #f1f5f9; --primary: #2ecc71; --border: #334155; --input-bg: #0f172a; }

        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); padding: 20px; }
        .box { background: var(--card); padding: 30px; border-radius: 24px; max-width: 650px; margin: 20px auto; border: 1px solid var(--border); box-shadow: 0 10px 15px rgba(0,0,0,0.1); }
        
        h2 { text-align: center; color: var(--primary); margin-bottom: 5px; }
        .subtitle { text-align: center; opacity: 0.6; margin-bottom: 25px; font-size: 0.9rem; }
        
        label { display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.8rem; text-transform: uppercase; }
        input, select, textarea { width: 100%; padding: 12px; margin-bottom: 15px; border: 2px solid var(--border); border-radius: 10px; background: var(--input-bg); color: var(--text); box-sizing: border-box; }
        
        .radio-group { display: flex; gap: 20px; margin-bottom: 20px; background: var(--bg); padding: 15px; border-radius: 10px; border: 1px solid var(--border); }
        .radio-option { display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: 600; }
        .radio-option input { width: auto; margin: 0; }

        .btn-save { background: var(--primary); color: white; border: none; padding: 15px; width: 100%; border-radius: 12px; cursor: pointer; font-weight: 700; font-size: 1rem; }
        .btn-list { display: block; text-align: center; background: #3b82f6; color: white; padding: 12px; border-radius: 12px; text-decoration: none; margin-top: 10px; font-weight: 600; }
        
        .alert { padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center; }
        .success { background: rgba(34, 197, 94, 0.1); color: #22c55e; }
        .error { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
        
        .footer { margin-top: 20px; text-align: center; display: flex; justify-content: space-between; }
        .footer a { color: var(--text); text-decoration: none; font-size: 0.8rem; opacity: 0.7; }
    </style>
</head>
<body>

<div class="box">
    <h2>🎪 Control de Activos</h2>
    <p class="subtitle">Registre la asignación y marque la asistencia del emprendedor.</p>
    
    <?php echo $mensaje; ?>

    <form method="POST">
        <label>Persona Responsable:</label>
        <select name="emp_id" required>
            <option value="">-- Seleccionar Emprendedor --</option>
            <?php
            $res = mysqli_query($conexion, "SELECT e.idemprendedores, p.nombres, p.apellidos FROM emprendedores e JOIN personas p ON e.personas_idpersonas = p.idpersonas WHERE (p.deleted_at IS NULL OR p.deleted_at = '0000-00-00 00:00:00')");
            while($e = mysqli_fetch_assoc($res)) {
                echo "<option value='{$e['idemprendedores']}'>{$e['nombres']} {$e['apellidos']}</option>";
            }
            ?>
        </select>
        
        <label>¿Se presentó hoy?</label>
        <div class="radio-group">
            <label class="radio-option">
                <input type="radio" name="asistencia" value="SÍ VINO" checked> ✅ SÍ VINO
            </label>
            <label class="radio-option">
                <input type="radio" name="asistencia" value="NO VINO"> ❌ NO VINO
            </label>
        </div>

        <label>Nombre / ID del Carrito:</label>
        <input type="text" name="nombre_c" placeholder="Puesto #01" required>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div>
                <label>Estado Estético:</label>
                <textarea name="desc" placeholder="Condiciones..."></textarea>
            </div>
            <div>
                <label>Equipamiento:</label>
                <textarea name="equip" placeholder="Inventario..."></textarea>
            </div>
        </div>
        
        <button type="submit" name="save_car" class="btn-save">Guardar Registro Diario</button>
        <a href="lista_carritos.php" class="btn-list">Ver Historial y Fechas</a>
    </form>

    <div class="footer">
        <a href="index.php">← Volver al Panel</a>
    </div>
</div>

</body>
</html>