<?php 
include 'config.php'; 

// Obtener tema visual
$res_conf = mysqli_query($conexion, "SELECT * FROM configuraciones WHERE id = 1");
$cfg = mysqli_fetch_assoc($res_conf);

$mensaje = "";

if(isset($_POST['activar'])){
    $con = mysqli_real_escape_string($conexion, $_POST['id_contrato']);
    $cuo = mysqli_real_escape_string($conexion, $_POST['cuota']);
    $dia = mysqli_real_escape_string($conexion, $_POST['dia']);

    // 1. Obtenemos datos del contrato
    $info = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT monto_total, emprendedores_idemprendedores FROM Contratos WHERE idContratos = '$con'"));
    $monto = $info['monto_total'];
    $emp_id = $info['emprendedores_idemprendedores'];

    // 2. VALIDACIÓN: ¿Tiene algún crédito con saldo pendiente?
    $chequeo = mysqli_query($conexion, "SELECT idcreditos FROM creditos WHERE emprendedores_idemprendedores = '$emp_id' AND saldo_inicial > 0 AND estado = 1");

    if(mysqli_num_rows($chequeo) > 0) {
        $mensaje = "<div class='alert error'>⚠️ Error: El emprendedor ya tiene un crédito activo. Debe saldarlo antes de activar uno nuevo.</div>";
    } else {
        // 3. Crear el nuevo crédito
        $sql = "INSERT INTO creditos (monto_inicial, saldo_inicial, fecha_inicio, estado, dia_de_pago, cuota_mensual, Contratos_idContratos, emprendedores_idemprendedores, created_at) 
                VALUES ('$monto', '$monto', NOW(), 1, '$dia', '$cuo', '$con', '$emp_id', NOW())";
        
        if(mysqli_query($conexion, $sql)) {
            $mensaje = "<div class='alert success'>💰 ¡Crédito activado! El préstamo ha sido cargado al historial del emprendedor.</div>";
        } else {
            $mensaje = "<div class='alert error'>❌ Error al activar: " . mysqli_error($conexion) . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es" data-theme="<?php echo $cfg['tema_color']; ?>">
<head>
    <meta charset="UTF-8">
    <title>Activar Crédito - <?php echo $cfg['nombre_sistema']; ?></title>
    <style>
        :root { --bg: #f4f7f6; --text: #333; --card: #fff; --primary: #43b02a; --accent: #2c3e50; }
        [data-theme="dark"] { --bg: #1a1a1a; --text: #f0f0f0; --card: #2d2d2d; --primary: #2ecc71; --accent: #ecf0f1; }
        
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); color: var(--text); padding: 20px; margin: 0; }
        .container { max-width: 550px; margin: 40px auto; background: var(--card); padding: 35px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        
        h2 { text-align: center; color: var(--primary); margin-bottom: 10px; }
        p.desc { text-align: center; font-size: 0.9em; opacity: 0.8; margin-bottom: 25px; }
        
        label { display: block; font-weight: bold; margin-bottom: 8px; font-size: 0.85em; text-transform: uppercase; }
        input, select { 
            width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 10px; 
            box-sizing: border-box; background: var(--card); color: var(--text); font-size: 1em; margin-bottom: 20px;
        }

        .btn-activate { 
            background: var(--primary); color: white; border: none; padding: 16px; 
            width: 100%; border-radius: 10px; cursor: pointer; font-weight: bold; 
            font-size: 1.1em; transition: 0.3s;
        }
        .btn-activate:hover { filter: brightness(1.1); transform: translateY(-2px); }

        .alert { padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center; font-weight: bold; }
        .success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        
        .nav-links { margin-top: 25px; text-align: center; border-top: 1px solid #eee; padding-top: 15px; }
        .nav-links a { color: var(--accent); text-decoration: none; font-size: 0.9em; font-weight: 500; }
    </style>
</head>
<body>

<div class="container">
    <h2>💸 Activación de Créditos</h2>
    <p class="desc">Vincula un contrato firmado para iniciar la cobranza.</p>
    
    <?php echo $mensaje; ?>

    <form method="POST">
        <label>Contrato Firmado Pendiente:</label>
        <select name="id_contrato" required>
            <option value="">Seleccione contrato...</option>
            <?php
            // Solo mostramos contratos que NO tengan un crédito creado todavía
            $sql_pendientes = "SELECT c.idContratos, p.nombres, p.apellidos, c.monto_total 
                               FROM Contratos c 
                               JOIN emprendedores e ON c.emprendedores_idemprendedores = e.idemprendedores 
                               JOIN personas p ON e.personas_idpersonas = p.idpersonas 
                               LEFT JOIN creditos cr ON c.idContratos = cr.Contratos_idContratos 
                               WHERE cr.idcreditos IS NULL";
            
            $res = mysqli_query($conexion, $sql_pendientes);
            while($c = mysqli_fetch_assoc($res)) {
                echo "<option value='{$c['idContratos']}'>#{$c['idContratos']} - {$c['nombres']} {$c['apellidos']} (\${$c['monto_total']})</option>";
            }
            ?>
        </select>

        <label>Valor de Cuota Mensual ($):</label>
        <input type="number" step="0.01" name="cuota" placeholder="Ej: 50.50" required>

        <label>Día de Pago sugerido:</label>
        <input type="number" name="dia" min="1" max="30" placeholder="Ej: 5" required>

        <button type="submit" name="activar" class="btn-activate">
            🚀 Activar Crédito y Generar Plan
        </button>
    </form>

    <div class="nav-links">
        <a href="index.php">🏠 Volver al Inicio</a>
    </div>
</div>

</body>
</html>