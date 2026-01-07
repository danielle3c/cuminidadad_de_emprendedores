<?php 
include 'config.php'; 

// 1. Obtener configuración del tema
$res_conf = mysqli_query($conexion, "SELECT * FROM configuraciones WHERE id = 1");
$cfg = mysqli_fetch_assoc($res_conf);

$mensaje = "";

if(isset($_POST['activar'])){
    $id_contrato = mysqli_real_escape_string($conexion, $_POST['id_contrato']);
    $cuota_mensual = mysqli_real_escape_string($conexion, $_POST['cuota']);
    $dia_pago = mysqli_real_escape_string($conexion, $_POST['dia']);

    // 1. Obtenemos datos del contrato y el ID del emprendedor
    $res_info = mysqli_query($conexion, "SELECT monto_total, emprendedores_idemprendedores FROM Contratos WHERE idContratos = '$id_contrato'");
    
    if(mysqli_num_rows($res_info) > 0) {
        $info = mysqli_fetch_assoc($res_info);
        $monto = $info['monto_total'];
        $emp_id = $info['emprendedores_idemprendedores'];

        // 2. VALIDACIÓN: ¿Ya tiene un crédito activo con deuda?
        $chequeo = mysqli_query($conexion, "SELECT idcreditos FROM creditos WHERE emprendedores_idemprendedores = '$emp_id' AND saldo_inicial > 0 AND estado = 1");

        if(mysqli_num_rows($chequeo) > 0) {
            $mensaje = "<div class='alert error'>El emprendedor ya tiene una deuda pendiente. Debe saldarla antes de activar un nuevo crédito.</div>";
        } else {
            // 3. Crear el nuevo crédito
            $sql = "INSERT INTO creditos (monto_inicial, saldo_inicial, fecha_inicio, estado, dia_de_pago, cuota_mensual, Contratos_idContratos, emprendedores_idemprendedores, created_at) 
                    VALUES ('$monto', '$monto', NOW(), 1, '$dia_pago', '$cuota_mensual', '$id_contrato', '$emp_id', NOW())";
            
            if(mysqli_query($conexion, $sql)) {
                $mensaje = "<div class='alert success'> ¡Crédito activado exitosamente! El plan de cobranza ha iniciado.</div>";
            } else {
                $mensaje = "<div class='alert error'> Error técnico: " . mysqli_error($conexion) . "</div>";
            }
        }
    } else {
        $mensaje = "<div class='alert error'> El contrato seleccionado no existe o ya no está disponible.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es" data-theme="<?php echo $cfg['tema_color']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activar Crédito | <?php echo $cfg['nombre_sistema']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { 
            --bg: #f8fafc; --card: #ffffff; --text: #1e293b; --primary: #43b02a; --border: #e2e8f0; --input-bg: #ffffff;
        }
        [data-theme="dark"] { 
            --bg: #0f172a; --card: #1e293b; --text: #f1f5f9; --primary: #2ecc71; --border: #334155; --input-bg: #0f172a;
        }

        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); padding: 20px; transition: 0.3s; }
        
        .container { 
            max-width: 550px; margin: 40px auto; background: var(--card); padding: 40px; 
            border-radius: 24px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); border: 1px solid var(--border);
        }
        
        h2 { text-align: center; color: var(--primary); font-weight: 700; font-size: 1.8rem; margin-top: 0; }
        p.desc { text-align: center; color: var(--text); opacity: 0.6; margin-bottom: 30px; }
        
        label { display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; }
        
        input, select { 
            width: 100%; padding: 14px; border: 2px solid var(--border); border-radius: 12px; 
            background: var(--input-bg); color: var(--text); font-size: 1rem; margin-bottom: 20px;
            box-sizing: border-box; transition: 0.2s;
        }

        input:focus, select:focus { border-color: var(--primary); outline: none; box-shadow: 0 0 0 4px rgba(46, 204, 113, 0.1); }

        .btn-activate { 
            background: var(--primary); color: white; border: none; padding: 18px; 
            width: 100%; border-radius: 14px; cursor: pointer; font-weight: 700; 
            font-size: 1.1rem; transition: 0.3s; margin-top: 10px;
        }
        .btn-activate:hover { transform: translateY(-2px); filter: brightness(1.1); }

        /* Estilo de Alertas */
        .alert { padding: 16px; border-radius: 12px; margin-bottom: 25px; text-align: center; font-weight: 600; }
        .success { background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.2); }
        .error { background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.2); }
        
        .nav-links { margin-top: 30px; text-align: center; border-top: 1px solid var(--border); padding-top: 20px; }
        .nav-links a { color: var(--text); opacity: 0.6; text-decoration: none; font-size: 0.9rem; font-weight: 600; }
        .nav-links a:hover { opacity: 1; color: var(--primary); }
    </style>
</head>
<body>

<div class="container">
    <h2> Activar Crédito</h2>
    <p class="desc">Autoriza el desembolso y define las condiciones de pago.</p>
    
    <?php echo $mensaje; ?>

    <form method="POST">
        <label>Contrato Firmado:</label>
        <select name="id_contrato" required>
            <option value="">Seleccione un contrato...</option>
            <?php
            // Solo mostramos contratos que NO tengan un crédito ya creado
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

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div>
                <label>Cuota Mensual ($):</label>
                <input type="number" step="0.01" name="cuota" placeholder="0.00" required>
            </div>
            <div>
                <label>Día de Pago (1-30):</label>
                <input type="number" name="dia" min="1" max="30" placeholder="5" required>
            </div>
        </div>

        <button type="submit" name="activar" class="btn-activate">
            Confirmar y Activar Préstamo
        </button>
    </form>

    <div class="nav-links">
        <a href="index.php">Volver al Panel Principal</a>
    </div>
</div>

</body>
</html>