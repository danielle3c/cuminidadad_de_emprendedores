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

    $sql = "INSERT INTO carritos (nombre_carrito, descripcion, equipamiento, emprendedores_idemprendedores, created_at) 
            VALUES ('$nom', '$des', '$equ', '$ide', NOW())";

    if(mysqli_query($conexion, $sql)){
        $mensaje = "<div class='alert success'>¡Activo Registrado! El carrito ha sido asignado al emprendedor correctamente.</div>";
    } else {
        $mensaje = "<div class='alert error'> Error al registrar en base de datos: " . mysqli_error($conexion) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es" data-theme="<?php echo $cfg['tema_color']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Carritos | <?php echo $cfg['nombre_sistema']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { 
            --bg: #f8fafc; --card: #ffffff; --text: #1e293b; --primary: #43b02a; --border: #e2e8f0; --input-bg: #ffffff;
        }
        [data-theme="dark"] { 
            --bg: #0f172a; --card: #1e293b; --text: #f1f5f9; --primary: #2ecc71; --border: #334155; --input-bg: #0f172a;
        }

        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); padding: 20px; transition: 0.3s; }
        
        .box { 
            background: var(--card); padding: 40px; border-radius: 24px; 
            max-width: 650px; margin: 40px auto; 
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); 
            border: 1px solid var(--border);
        }
        
        h2 { text-align: center; color: var(--primary); font-weight: 700; margin-top: 0; font-size: 1.8rem; }
        p.subtitle { text-align: center; opacity: 0.6; margin-bottom: 30px; font-size: 0.95rem; }
        
        label { display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; }
        
        input, select, textarea { 
            width: 100%; padding: 14px; margin-bottom: 20px; border: 2px solid var(--border); 
            border-radius: 12px; background: var(--input-bg); color: var(--text);
            font-size: 1rem; box-sizing: border-box; transition: 0.2s;
        }

        textarea { height: 100px; resize: none; font-family: inherit; }

        input:focus, select:focus, textarea:focus { 
            border-color: var(--primary); outline: none; 
            box-shadow: 0 0 0 4px rgba(46, 204, 113, 0.1); 
        }

        button { 
            background: var(--primary); color: white; border: none; padding: 18px; 
            width: 100%; border-radius: 14px; cursor: pointer; font-weight: 700; 
            font-size: 1.1rem; transition: 0.3s; box-shadow: 0 10px 15px -3px rgba(46, 204, 113, 0.2);
        }
        button:hover { transform: translateY(-2px); filter: brightness(1.1); }

        .alert { padding: 16px; border-radius: 12px; margin-bottom: 25px; text-align: center; font-weight: 600; animation: slideIn 0.3s ease; }
        @keyframes slideIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }

        .success { background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.2); }
        .error { background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.2); }
        
        .footer { text-align: center; margin-top: 25px; border-top: 1px solid var(--border); padding-top: 20px; }
        .footer a { color: var(--text); text-decoration: none; opacity: 0.6; font-weight: 600; font-size: 0.9rem; }
        .footer a:hover { color: var(--primary); opacity: 1; }
    </style>
</head>
<body>

<div class="box">
    <h2>🎪 Registro de Activos</h2>
    <p class="subtitle">Ingrese los detalles del carrito o puesto asignado al emprendedor.</p>
    
    <?php echo $mensaje; ?>

    <form method="POST">
        <label>Persona Responsable:</label>
        <select name="emp_id" required>
            <option value="">-- Seleccionar Emprendedor --</option>
            <?php
            $query = "SELECT e.idemprendedores, p.nombres, p.apellidos 
                FROM emprendedores e 
                JOIN personas p ON e.personas_idpersonas = p.idpersonas 
                WHERE (p.deleted_at IS NULL OR p.deleted_at = '0000-00-00 00:00:00')";
            $res = mysqli_query($conexion, $query);
            while($e = mysqli_fetch_assoc($res)) {
                echo "<option value='{$e['idemprendedores']}'>{$e['nombres']} {$e['apellidos']}</option>";
            }
            ?>
        </select>
        
        <label>Nombre / ID del Carrito:</label>
        <input type="text" name="nombre_c" placeholder="Puesto #01 - Sector A" required>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div>
                <label>Estado Estético:</label>
                <textarea name="desc" placeholder="Pintura, estructura, limpieza..."></textarea>
            </div>
            <div>
                <label>Equipamiento / Inventario:</label>
                <textarea name="equip" placeholder="Gas, utensilios, herramientas..."></textarea>
            </div>
        </div>
        
        <button type="submit" name="save_car">Guardar y Asignar Activo</button>
    </form>

    <div class="footer">
        <a href="index.php">Volver al Panel Principal</a>
    </div>
</div>

</body>
</html>