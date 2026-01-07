<?php 
include 'config.php'; 

// Obtener configuración para el tema visual
$res_conf = mysqli_query($conexion, "SELECT * FROM configuraciones WHERE id = 1");
$cfg = mysqli_fetch_assoc($res_conf);

$mensaje = "";

if(isset($_POST['pay'])){
    $idc = mysqli_real_escape_string($conexion, $_POST['id_credito']); 
    $mon = mysqli_real_escape_string($conexion, $_POST['monto_pago']); 
    $tip = mysqli_real_escape_string($conexion, $_POST['tipo_pago']);
    
    // 1. Registrar el ingreso en la tabla cobranzas
    $ins = "INSERT INTO cobranzas (creditos_idcreditos, monto, tipo_pago, fecha_hora, created_at) 
            VALUES ('$idc', '$mon', '$tip', NOW(), NOW())";
    
    if(mysqli_query($conexion, $ins)){
        // 2. Actualizar el saldo restando el pago
        mysqli_query($conexion, "UPDATE creditos SET saldo_inicial = saldo_inicial - $mon WHERE idcreditos = '$idc'");

        // 3. Lógica de Cierre: Si el saldo llegó a 0 o menos, marcar como pagado (estado 2)
        $check_saldo = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT saldo_inicial FROM creditos WHERE idcreditos = '$idc'"));
        if($check_saldo['saldo_inicial'] <= 0){
            mysqli_query($conexion, "UPDATE creditos SET estado = 2 WHERE idcreditos = '$idc'");
            $mensaje = "<div class='alert success'>✅ ¡Pago total recibido! El crédito ha sido finalizado.</div>";
        } else {
            $mensaje = "<div class='alert success'>💵 Pago registrado. Nuevo saldo: $" . number_format($check_saldo['saldo_inicial'], 2) . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es" data-theme="<?php echo $cfg['tema_color']; ?>">
<head>
    <meta charset="UTF-8">
    <title>Caja - <?php echo $cfg['nombre_sistema']; ?></title>
    <style>
        :root { --bg: #fdf2f2; --card: #ffffff; --text: #333; --primary: #e11d48; --btn: #43b02a; }
        [data-theme="dark"] { --bg: #1a1a1a; --card: #2d2d2d; --text: #f0f0f0; --primary: #fb7185; --btn: #2ecc71; }
        
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); color: var(--text); padding: 20px; }
        .box { 
            background: var(--card); padding: 30px; border-radius: 15px; 
            max-width: 500px; margin: 40px auto; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border-top: 6px solid var(--primary); 
        }
        h2 { text-align: center; margin-bottom: 25px; color: var(--primary); }
        label { display: block; font-weight: bold; font-size: 0.85em; margin-bottom: 5px; }
        
        input, select { 
            width: 100%; padding: 12px; margin-bottom: 20px; 
            border-radius: 8px; border: 1px solid #ddd; 
            background: var(--card); color: var(--text); box-sizing: border-box; 
        }

        button { 
            background: var(--btn); color: white; border: none; 
            padding: 15px; width: 100%; border-radius: 8px; 
            cursor: pointer; font-weight: bold; font-size: 1.1em;
            transition: 0.3s;
        }
        button:hover { filter: brightness(1.1); transform: scale(1.02); }

        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-weight: bold; }
        .success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
        
        .footer { text-align: center; margin-top: 20px; }
        .footer a { color: var(--text); text-decoration: none; font-size: 0.9em; opacity: 0.7; }
    </style>
</head>
<body>

<div class="box">
    <h2>💰 Registrar Cobro</h2>
    
    <?php echo $mensaje; ?>

    <form method="POST">
        <label>Seleccionar Deudor Activo:</label>
        <select name="id_credito" required>
            <option value="">-- Buscar Crédito --</option>
            <?php
            $sql = "SELECT c.idcreditos, p.nombres, p.apellidos, c.saldo_inicial 
                    FROM creditos c 
                    JOIN emprendedores e ON c.emprendedores_idemprendedores = e.idemprendedores 
                    JOIN personas p ON e.personas_idpersonas = p.idpersonas 
                    WHERE c.estado = 1"; // Solo créditos activos
            $res = mysqli_query($conexion, $sql);
            while($c = mysqli_fetch_assoc($res)){
                echo "<option value='{$c['idcreditos']}'>{$c['nombres']} {$c['apellidos']} (Debe: $" . number_format($c['saldo_inicial'], 2) . ")</option>";
            }
            ?>
        </select>

        <label>Monto del Pago ($):</label>
        <input type="number" step="0.01" name="monto_pago" placeholder="0.00" required>

        <label>Método de Pago:</label>
        <select name="tipo_pago">
            <option value="Efectivo">Efectivo</option>
            <option value="Transferencia">Transferencia</option>
            <option value="Depósito">Depósito Bancario</option>
        </select>

        <button type="submit" name="pay">Confirmar Recibo</button>
    </form>

    <div class="footer">
        <a href="index.php">Volver al Inicio</a>
    </div>
</div>

</body>
</html>