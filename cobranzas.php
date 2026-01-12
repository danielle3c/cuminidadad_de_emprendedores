<?php 
session_start();
if (!isset($_SESSION['usuario_id'])) { header("Location: login.php"); exit(); }
include 'config.php'; 

$res_conf = mysqli_query($conexion, "SELECT * FROM configuraciones WHERE id = 1");
$cfg = mysqli_fetch_assoc($res_conf);
$moneda = $cfg['simbolo_moneda'];

$mensaje = "";

// Lógica para procesar el pago
if(isset($_POST['pay'])){
    $idc = mysqli_real_escape_string($conexion, $_POST['id_credito']); 
    $mon = mysqli_real_escape_string($conexion, $_POST['monto_pago']); 
    $tip = mysqli_real_escape_string($conexion, $_POST['tipo_pago']);
    
    // Inserta el registro individual de la cobranza
    $ins = "INSERT INTO cobranzas (creditos_idcreditos, monto, tipo_pago, fecha_hora, created_at) 
            VALUES ('$idc', '$mon', '$tip', NOW(), NOW())";
    
    if(mysqli_query($conexion, $ins)){
        // Resta el monto del saldo actual del crédito
        mysqli_query($conexion, "UPDATE creditos SET saldo_inicial = saldo_inicial - $mon WHERE idcreditos = '$idc'");
        
        // Verifica si el crédito se canceló por completo
        $check = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT saldo_inicial FROM creditos WHERE idcreditos = '$idc'"));
        if($check['saldo_inicial'] <= 0){
            mysqli_query($conexion, "UPDATE creditos SET estado = 2 WHERE idcreditos = '$idc'");
            $mensaje = "<div class='alert success'><i class='fas fa-check-double'></i> ¡Pago Total Recibido! Crédito Finalizado.</div>";
        } else {
            $mensaje = "<div class='alert success'><i class='fas fa-check'></i> Pago de $moneda".number_format($mon,2)." registrado con éxito.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es" data-theme="<?php echo $cfg['tema_color']; ?>">
<head>
    <meta charset="UTF-8">
    <title>Registrar Cobranza</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { 
            --bg: #f3f6f9; 
            --white: #ffffff; 
            --primary: #4e73df; 
            --success: #2ecc71; 
            --text: #333; 
            --border: #e3e6f0; 
        }
        [data-theme="dark"] { 
            --bg: #1a202c; 
            --white: #2d3748; 
            --text: #edf2f7; 
            --border: #4a5568; 
        }
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); color: var(--text); margin: 0; padding: 20px; }
        
        /* Navbar superior idéntica a tu imagen */
        .navbar { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            max-width: 900px; 
            margin: 0 auto 30px; 
        }
        .btn-nav { 
            background: var(--white); 
            border: 1px solid var(--border); 
            padding: 10px 20px; 
            border-radius: 10px; 
            text-decoration: none; 
            color: #333; 
            font-weight: bold; 
            display: flex; 
            align-items: center; 
            gap: 8px; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.05); 
        }
        .title-page { font-size: 1.2rem; font-weight: bold; display: flex; align-items: center; gap: 8px; }

        /* Estilo del Formulario */
        .card { 
            background: var(--white); 
            padding: 30px; 
            border-radius: 15px; 
            max-width: 500px; 
            margin: auto; 
            box-shadow: 0 8px 20px rgba(0,0,0,0.06); 
        }
        label { font-weight: 600; font-size: 0.9rem; color: var(--primary); display: block; margin-bottom: 5px; }
        input, select { 
            width: 100%; 
            padding: 12px; 
            margin-bottom: 20px; 
            border-radius: 8px; 
            border: 1px solid var(--border); 
            background: var(--white); 
            color: var(--text); 
            box-sizing: border-box; 
            font-size: 1rem;
        }
        .btn-submit { 
            background: var(--success); 
            color: white; 
            border: none; 
            padding: 15px; 
            width: 100%; 
            border-radius: 8px; 
            cursor: pointer; 
            font-weight: bold; 
            font-size: 1rem; 
            transition: 0.3s;
        }
        .btn-submit:hover { filter: brightness(1.1); transform: translateY(-1px); }
        .alert { 
            background: #d4edda; 
            color: #155724; 
            padding: 15px; 
            border-radius: 8px; 
            text-align: center; 
            margin-bottom: 20px; 
            font-weight: 500;
        }
    </style>
</head>
<body>

<div class="navbar">
    <a href="index.php" class="btn-nav"><i class="fas fa-home"></i> Inicio</a>
    <div class="title-page"><i class="fas fa-cash-register"></i> Registrar Cobranza</div>
    <a href="historial_cobros.php" class="btn-nav"><i class="fas fa-history"></i> Historial</a>
</div>

<div class="card">
    <?php echo $mensaje; ?>
    <form method="POST">
        <label><i class="fas fa-user"></i> Seleccionar Deudor:</label>
        <select name="id_credito" required>
            <option value="">-- Buscar Cliente --</option>
            <?php
            // Solo muestra clientes con deudas activas (estado 1)
            $sql_c = "SELECT c.idcreditos, p.nombres, c.saldo_inicial 
                      FROM creditos c 
                      JOIN emprendedores e ON c.emprendedores_idemprendedores = e.idemprendedores 
                      JOIN personas p ON e.personas_idpersonas = p.idpersonas 
                      WHERE c.estado = 1";
            $res_c = mysqli_query($conexion, $sql_c);
            while($c = mysqli_fetch_assoc($res_c)){
                echo "<option value='{$c['idcreditos']}'>{$c['nombres']} (Deuda: {$moneda}{$c['saldo_inicial']})</option>";
            }
            ?>
        </select>

        <label><i class="fas fa-money-bill-wave"></i> Monto a Cobrar:</label>
        <input type="number" step="0.01" name="monto_pago" placeholder="Ej: 50.00" required>

        <label><i class="fas fa-credit-card"></i> Método de Pago:</label>
        <select name="tipo_pago">
            <option value="Efectivo">Efectivo</option>
            <option value="Transferencia">Transferencia</option>
            <option value="Depósito">Depósito</option>
        </select>

        <button type="submit" name="pay" class="btn-submit">
            <i class="fas fa-save"></i> Confirmar y Guardar Pago
        </button>
    </form>
</div>

</body>
</html>