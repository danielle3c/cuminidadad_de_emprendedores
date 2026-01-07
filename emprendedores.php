<?php 
include 'config.php'; 

// 1. OBTENER CONFIGURACIÓN PARA EL TEMA VISUAL
$res_conf = mysqli_query($conexion, "SELECT * FROM configuraciones WHERE id = 1");
$cfg = mysqli_fetch_assoc($res_conf);

$mensaje = "";

// 2. PROCESAR EL REGISTRO
if(isset($_POST['btn_e'])){
    $idp   = mysqli_real_escape_string($conexion, $_POST['personas_idpersonas']);
    $tipo  = mysqli_real_escape_string($conexion, $_POST['tipo_negocio']);
    $rubro = mysqli_real_escape_string($conexion, $_POST['rubro']);
    $prod  = mysqli_real_escape_string($conexion, $_POST['producto_principal']);
    $lim   = mysqli_real_escape_string($conexion, $_POST['limite_credito']);

    // Verificar si ya es emprendedor para evitar duplicados
    $check = mysqli_query($conexion, "SELECT idemprendedores FROM emprendedores WHERE personas_idpersonas = '$idp'");
    
    if(mysqli_num_rows($check) > 0){
        $mensaje = "<div class='alert error'>⚠️ Esta persona ya está registrada como emprendedor.</div>";
    } else {
        $sql = "INSERT INTO emprendedores (personas_idpersonas, tipo_negocio, rubro, producto_principal, limite_credito, fecha_registro, created_at) 
                VALUES ('$idp', '$tipo', '$rubro', '$prod', '$lim', NOW(), NOW())";

        if(mysqli_query($conexion, $sql)){
            $mensaje = "<div class='alert success'>🚀 Emprendedor vinculado exitosamente.</div>";
        } else {
            $mensaje = "<div class='alert error'>❌ Error: " . mysqli_error($conexion) . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es" data-theme="<?php echo $cfg['tema_color']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vincular Emprendedor - <?php echo $cfg['nombre_sistema']; ?></title>
    <style>
        /* Variables de Color Dinámicas */
        :root { --bg: #f4f7f6; --text: #333; --card: #fff; --primary: #43b02a; --secondary: #6c757d; }
        [data-theme="dark"] { --bg: #1a1a1a; --text: #f0f0f0; --card: #2d2d2d; --primary: #2ecc71; --secondary: #495057; }
        [data-theme="blue"] { --bg: #e0e6ed; --text: #1a2a3a; --card: #fff; --primary: #0056b3; --secondary: #5a6268; }

        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); color: var(--text); transition: 0.3s; padding: 20px; }
        .container { max-width: 550px; margin: auto; background: var(--card); padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        
        h2 { text-align: center; color: var(--primary); border-bottom: 2px solid var(--primary); padding-bottom: 10px; margin-top: 0; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-top: 15px; font-weight: bold; font-size: 0.9em; }
        
        input, select { 
            width: 100%; padding: 12px; margin-top: 5px; border: 1px solid #ddd; border-radius: 8px; 
            box-sizing: border-box; background: var(--card); color: var(--text); font-size: 1em;
        }
        
        .btn-submit { 
            background: var(--primary); color: white; border: none; padding: 15px; 
            width: 100%; border-radius: 8px; cursor: pointer; font-weight: bold; 
            margin-top: 25px; font-size: 1.1em; transition: 0.3s;
        }
        .btn-submit:hover { filter: brightness(1.1); transform: translateY(-2px); }

        /* Estilos de Alertas */
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-weight: bold; }
        .success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }

        /* Navegación Inferior */
        .footer-nav { 
            display: flex; gap: 10px; margin-top: 30px; border-top: 1px solid #ddd; padding-top: 20px; 
        }
        .btn-nav { 
            flex: 1; text-align: center; padding: 10px; text-decoration: none; border-radius: 8px; 
            font-size: 0.9em; font-weight: bold; background: var(--secondary); color: white !important; 
        }
        .btn-home { background: #333; }
        .btn-nav:hover { opacity: 0.8; }
    </style>
</head>
<body>

<div class="container">
    <h2>💼 Nuevo Emprendedor</h2>
    
    <?php echo $mensaje; ?>

    <form method="POST">
        <div class="form-group">
            <label>Seleccionar Persona:</label>
            <select name="personas_idpersonas" required>
                <option value="">-- Seleccione una persona --</option>
                <?php
                // CONSULTA CORREGIDA: Se ajusta para detectar registros con deleted_at vacío o cero
                $query = "SELECT idpersonas, nombres, apellidos, rut 
                          FROM personas 
                          WHERE (deleted_at IS NULL OR deleted_at = '' OR deleted_at = '0000-00-00 00:00:00') 
                          ORDER BY nombres ASC";
                
                $res = mysqli_query($conexion, $query);

                if (mysqli_num_rows($res) > 0) {
                    while($p = mysqli_fetch_assoc($res)){
                        echo "<option value='{$p['idpersonas']}'>{$p['nombres']} {$p['apellidos']} ({$p['rut']})</option>";
                    }
                } else {
                    echo "<option value=''>⚠️ No hay personas disponibles</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label>Tipo de Negocio:</label>
            <input type="text" name="tipo_negocio" placeholder="Ej: Almacén, Taller, Servicios" required>
        </div>

        <div class="form-group">
            <label>Rubro:</label>
            <input type="text" name="rubro" placeholder="Ej: Alimentación, Textil" required>
        </div>

        <div class="form-group">
            <label>Producto / Servicio Principal:</label>
            <input type="text" name="producto_principal" placeholder="¿Qué vende principalmente?" required>
        </div>

        <div class="form-group">
            <label>Límite de Crédito Sugerido ($):</label>
            <input type="number" name="limite_credito" placeholder="0.00" step="0.01" required>
        </div>

        <button type="submit" name="btn_e" class="btn-submit">✅ Registrar y Vincular</button>
    </form>

    <div class="footer-nav">
        <a href="index.php" class="btn-nav btn-home">🏠 Inicio</a>
        <a href="emprendedores.php" class="btn-nav">📋 Lista</a>
    </div>
</div>

</body>
</html>