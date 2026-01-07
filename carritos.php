<?php 
include 'config.php'; 

// Obtener tema para consistencia visual
$res_conf = mysqli_query($conexion, "SELECT * FROM configuraciones WHERE id = 1");
$cfg = mysqli_fetch_assoc($res_conf);

$mensaje = "";

if(isset($_POST['save_car'])){
    $ide = mysqli_real_escape_string($conexion, $_POST['emp_id']); 
    $nom = mysqli_real_escape_string($conexion, $_POST['nombre_c']); 
    $des = mysqli_real_escape_string($conexion, $_POST['desc']); 
    $equ = mysqli_real_escape_string($conexion, $_POST['equip']);
    $ast = mysqli_real_escape_string($conexion, $_POST['asistencia']); 

    $sql = "INSERT INTO carritos (nombre_carrito, descripcion, equipamiento, asistencia, emprendedores_idemprendedores, created_at) 
            VALUES ('$nom', '$des', '$equ', '$ast', '$ide', NOW())";

    if(mysqli_query($conexion, $sql)){
        $mensaje = "<div class='alert success'>✅ ¡Registro Guardado! El historial de asistencia se actualizó.</div>";
    } else {
        $mensaje = "<div class='alert error'> ❌ Error: " . mysqli_error($conexion) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es" data-theme="<?php echo $cfg['tema_color']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Carritos | <?php echo $cfg['nombre_sistema']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --bg: #f8fafc; --card: #ffffff; --text: #1e293b; --primary: #43b02a; --border: #e2e8f0; --input-bg: #ffffff; }
        [data-theme="dark"] { --bg: #0f172a; --card: #1e293b; --text: #f1f5f9; --primary: #2ecc71; --border: #334155; --input-bg: #0f172a; }

        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); padding: 20px; }
        .box { background: var(--card); padding: 35px; border-radius: 24px; max-width: 700px; margin: 20px auto; border: 1px solid var(--border); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        
        h2 { text-align: center; color: var(--primary); margin-bottom: 5px; font-weight: 700; }
        .subtitle { text-align: center; opacity: 0.6; margin-bottom: 30px; font-size: 0.95rem; }
        
        label { display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; }
        input, select, textarea { width: 100%; padding: 14px; margin-bottom: 20px; border: 2px solid var(--border); border-radius: 12px; background: var(--input-bg); color: var(--text); font-size: 1rem; box-sizing: border-box; transition: 0.2s; }
        
        textarea { height: 100px; resize: none; }
        input:focus, select:focus, textarea:focus { border-color: var(--primary); outline: none; box-shadow: 0 0 0 4px rgba(67, 176, 42, 0.1); }

        /* Estilo para Radio Buttons de Asistencia */
        .attendance-card { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px; }
        .attendance-option { border: 2px solid var(--border); padding: 15px; border-radius: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; font-weight: 700; transition: 0.3s; }
        .attendance-option input { display: none; }
        .attendance-option:has(input:checked) { border-color: var(--primary); background: rgba(67, 176, 42, 0.05); color: var(--primary); }

        .btn-save { background: var(--primary); color: white; border: none; padding: 18px; width: 100%; border-radius: 14px; cursor: pointer; font-weight: 700; font-size: 1.1rem; transition: 0.3s; box-shadow: 0 4px 12px rgba(67, 176, 42, 0.2); }
        .btn-save:hover { transform: translateY(-2px); filter: brightness(1.1); }

        .btn-list { display: block; text-align: center; background: #3b82f6; color: white; padding: 14px; border-radius: 14px; text-decoration: none; margin-top: 15px; font-weight: 600; transition: 0.3s; }
        
        .alert { padding: 16px; border-radius: 12px; margin-bottom: 25px; text-align: center; font-weight: 600; }
        .success { background: rgba(34, 197, 94, 0.1); color: #22c55e; }
        .error { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

        .footer { margin-top: 25px; text-align: center; border-top: 1px solid var(--border); padding-top: 20px; }
        .footer a { color: var(--text); text-decoration: none; font-size: 0.9rem; opacity: 0.6; font-weight: 600; }
    </style>
</head>
<body>

<div class="box">
    <h2>🎪 Registro de Carritos</h2>
    <p class="subtitle">Asigne activos y controle la asistencia diaria.</p>
    
    <?php echo $mensaje; ?>

    <form method="POST">
        <label><i class="fas fa-user"></i> Seleccionar Emprendedor (Datos Completos):</label>
        <select name="emp_id" required>
            <option value="">-- Buscar por Nombre, Rubro o Teléfono --</option>
            <?php
            // Traemos Nombre, Apellido, Rubro y Teléfono para que el usuario identifique bien
            $query = "SELECT e.idemprendedores, p.nombres, p.apellidos, p.telefono, e.rubro 
                      FROM emprendedores e 
                      JOIN personas p ON e.personas_idpersonas = p.idpersonas 
                      WHERE (p.deleted_at IS NULL OR p.deleted_at = '0000-00-00 00:00:00')";
            $res = mysqli_query($conexion, $query);
            while($e = mysqli_fetch_assoc($res)) {
                echo "<option value='{$e['idemprendedores']}'>
                        👤 {$e['nombres']} {$e['apellidos']} | 🛠️ Rubro: {$e['rubro']} | 📞 Tel: {$e['telefono']}
                      </option>";
            }
            ?>
        </select>
        
        <label><i class="fas fa-calendar-check"></i> ¿Se presentó hoy?</label>
        <div class="attendance-card">
            <label class="attendance-option">
                <input type="radio" name="asistencia" value="SÍ VINO" checked> 
                <i class="fas fa-check-circle"></i> SÍ VINO
            </label>
            <label class="attendance-option">
                <input type="radio" name="asistencia" value="NO VINO"> 
                <i class="fas fa-times-circle"></i> NO VINO
            </label>
        </div>

        <label><i class="fas fa-shopping-cart"></i> Nombre / ID del Carrito:</label>
        <input type="text" name="nombre_c" placeholder="Ej: Carrito Hot-Dogs #04" required>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div>
                <label>Estado Físico:</label>
                <textarea name="desc" placeholder="Limpieza, abolladuras..."></textarea>
            </div>
            <div>
                <label>Equipamiento:</label>
                <textarea name="equip" placeholder="Gas, bandejas, toldo..."></textarea>
            </div>
        </div>
        
        <button type="submit" name="save_car" class="btn-save">
            <i class="fas fa-save"></i> Guardar Registro de Hoy
        </button>

        <a href="lista_carritos.php" class="btn-list">
            <i class="fas fa-list-alt"></i> Ver Lista e Historial Completo
        </a>
    </form>

    <div class="footer">
        <a href="index.php"><i class="fas fa-arrow-left"></i> Volver al Panel de Control</a>
    </div>
</div>

</body>
</html>