<?php 
include 'config.php'; 

// 1. Cargar configuración visual
$res_conf = mysqli_query($conexion, "SELECT * FROM configuraciones WHERE id = 1");
$cfg = mysqli_fetch_assoc($res_conf);

$mensaje = "";

// 2. Procesar el guardado
if(isset($_POST['save_car'])){
    $ide = mysqli_real_escape_string($conexion, $_POST['emp_id']); 
    $nom = mysqli_real_escape_string($conexion, $_POST['nombre_c']); 
    $des = mysqli_real_escape_string($conexion, $_POST['desc']); 
    $equ = mysqli_real_escape_string($conexion, $_POST['equip']);
    $ast = mysqli_real_escape_string($conexion, $_POST['asistencia']); 

    $sql = "INSERT INTO carritos (nombre_carrito, descripcion, equipamiento, asistencia, emprendedores_idemprendedores, created_at) 
            VALUES ('$nom', '$des', '$equ', '$ast', '$ide', NOW())";

    if(mysqli_query($conexion, $sql)){
        $mensaje = "<div class='alert success'>✅ ¡Registro Guardado con éxito!</div>";
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
    <title>Asignación de Carritos | <?php echo $cfg['nombre_sistema']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --bg: #f8fafc; --card: #ffffff; --text: #1e293b; --primary: #43b02a; --border: #e2e8f0; }
        [data-theme="dark"] { --bg: #0f172a; --card: #1e293b; --text: #f1f5f9; --primary: #2ecc71; --border: #334155; }

        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); padding: 20px; }
        .box { background: var(--card); padding: 35px; border-radius: 20px; max-width: 700px; margin: auto; border: 1px solid var(--border); box-shadow: 0 10px 15px rgba(0,0,0,0.05); }
        
        label { display: block; margin-bottom: 8px; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; color: var(--primary); }
        
        select, input, textarea { 
            width: 100%; padding: 12px; margin-bottom: 20px; border: 2px solid var(--border); 
            border-radius: 10px; background: transparent; color: var(--text); font-size: 1rem; box-sizing: border-box;
        }

        /* Estilo para los botones de asistencia */
        .asistencia-container { display: flex; gap: 15px; margin-bottom: 20px; }
        .asistencia-btn { 
            flex: 1; border: 2px solid var(--border); padding: 15px; border-radius: 12px; 
            text-align: center; cursor: pointer; font-weight: 700; transition: 0.3s;
        }
        .asistencia-btn input { display: none; }
        .asistencia-btn:has(input:checked) { background: var(--primary); color: white; border-color: var(--primary); }

        .btn-save { 
            background: var(--primary); color: white; border: none; padding: 15px; 
            width: 100%; border-radius: 12px; cursor: pointer; font-weight: 700; font-size: 1.1rem;
        }
        
        .alert { padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center; font-weight: 600; }
        .success { background: rgba(34, 197, 94, 0.1); color: #22c55e; }
        .error { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

        .footer-links { display: flex; justify-content: space-between; margin-top: 20px; }
        .footer-links a { color: var(--text); text-decoration: none; font-weight: 600; opacity: 0.7; }
    </style>
</head>
<body>

<div class="box">
    <h2 style="text-align: center; margin-top: 0;">🎪 Registrar Entrega</h2>
    
    <?php echo $mensaje; ?>

    <form method="POST">
        <label><i class="fas fa-id-card"></i> Seleccionar Emprendedor:</label>
        <select name="emp_id" required>
            <option value="">-- Buscar por Nombre, Apellidos o Rubro --</option>
            <?php
            // Consulta para unir personas con emprendedores
            $query = "SELECT e.idemprendedores, p.nombres, p.apellidos, p.telefono, e.rubro 
                      FROM emprendedores e 
                      JOIN personas p ON e.personas_idpersonas = p.idpersonas 
                      WHERE p.deleted_at IS NULL";
            $res = mysqli_query($conexion, $query);
            while($e = mysqli_fetch_assoc($res)) {
                // Se muestra Nombre, Apellido, Rubro y Teléfono en la misma opción
                echo "<option value='{$e['idemprendedores']}'>
                        {$e['nombres']} {$e['apellidos']} | {$e['rubro']} | Tel: {$e['telefono']}
                      </option>";
            }
            ?>
        </select>

        <label><i class="fas fa-clock"></i> ¿Asistió hoy?</label>
        <div class="asistencia-container">
            <label class="asistencia-btn">
                <input type="radio" name="asistencia" value="SÍ VINO" checked> ✅ SÍ VINO
            </label>
            <label class="asistencia-btn">
                <input type="radio" name="asistencia" value="NO VINO"> ❌ NO VINO
            </label>
        </div>

        <label><i class="fas fa-store"></i> Nombre del Carrito / Puesto:</label>
        <input type="text" name="nombre_c" placeholder="Ej: Carrito Hamburguesas A-1" required>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div>
                <label>Estado Estético:</label>
                <textarea name="desc" placeholder="Pintura, llantas..."></textarea>
            </div>
            <div>
                <label>Inventario/Equipamiento:</label>
                <textarea name="equip" placeholder="Gas, utensilios..."></textarea>
            </div>
        </div>

        <button type="submit" name="save_car" class="btn-save">
            <i class="fas fa-save"></i> Guardar y Registrar
        </button>
    </form>

    <div class="footer-links">
        <a href="index.php"><i class="fas fa-chevron-left"></i> Inicio</a>
        <a href="lista_carritos.php"><i class="fas fa-list"></i> Ver Historial</a>
    </div>
</div>

</body>
</html>