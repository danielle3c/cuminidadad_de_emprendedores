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
        $mensaje = "<div class='alert success'>🎪 Carrito registrado y asignado con éxito.</div>";
    } else {
        $mensaje = "<div class='alert error'>❌ Error al registrar: " . mysqli_error($conexion) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es" data-theme="<?php echo $cfg['tema_color']; ?>">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Carritos - <?php echo $cfg['nombre_sistema']; ?></title>
    <style>
        :root { --bg: #f4f7f6; --text: #333; --card: #fff; --primary: #43b02a; --accent: #2c3e50; }
        [data-theme="dark"] { --bg: #1a1a1a; --text: #f0f0f0; --card: #2d2d2d; --primary: #2ecc71; --accent: #ecf0f1; }
        
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); color: var(--text); padding: 20px; }
        .box { 
            background: var(--card); padding: 30px; border-radius: 15px; 
            max-width: 600px; margin: 40px auto; box-shadow: 0 10px 25px rgba(0,0,0,0.1); 
        }
        
        h2 { text-align: center; color: var(--primary); margin-bottom: 25px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; font-size: 0.9em; }
        
        input, select, textarea { 
            width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; 
            border-radius: 8px; box-sizing: border-box; background: var(--card); color: var(--text);
        }

        textarea { height: 80px; resize: vertical; }

        button { 
            background: var(--primary); color: white; border: none; padding: 15px; 
            width: 100%; border-radius: 10px; cursor: pointer; font-weight: bold; 
            font-size: 1.1em; transition: 0.3s;
        }
        button:hover { opacity: 0.9; transform: translateY(-2px); }

        .alert { padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center; font-weight: bold; }
        .success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        
        .footer { text-align: center; margin-top: 20px; }
        .footer a { color: var(--text); text-decoration: none; opacity: 0.7; font-size: 0.9em; }
    </style>
</head>
<body>

<div class="box">
    <h2>🎪 Registro de Carritos / Puestos</h2>
    
    <?php echo $mensaje; ?>

    <form method="POST">
        <label>Responsable del Activo (Emprendedor):</label>
        <select name="emp_id" required>
            <option value="">-- Seleccionar Emprendedor --</option>
            <?php
            // Solo mostramos emprendedores activos
            $res = mysqli_query($conexion, "SELECT e.idemprendedores, p.nombres, p.apellidos 
                                        FROM emprendedores e 
                                        JOIN personas p ON e.personas_idpersonas = p.idpersonas 
                                        WHERE p.deleted_at IS NULL");
            while($e = mysqli_fetch_assoc($res)) {
                echo "<option value='{$e['idemprendedores']}'>{$e['nombres']} {$e['apellidos']}</option>";
            }
            ?>
        </select>
        
        <label>Nombre Identificador:</label>
        <input type="text" name="nombre_c" placeholder="Ej: Carrito Hot-Dogs #04" required>
        
        <label>Estado Inicial:</label>
        <textarea name="desc" placeholder="Describa abolladuras, pintura o estado mecánico..."></textarea>
        
        <label>Inventario de Equipamiento:</label>
        <textarea name="equip" placeholder="Ej: 1 Freidora, 2 pinzas, 1 extintor..."></textarea>
        
        <button type="submit" name="save_car">💾 Registrar Carrito</button>
    </form>

    <div class="footer">
        <a href="index.php">Volver al Inicio</a>
    </div>
</div>

</body>
</html>