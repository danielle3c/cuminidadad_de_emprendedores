<?php 
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

include 'config.php'; 

$res_conf = mysqli_query($conexion, "SELECT * FROM configuraciones WHERE id = 1");
$cfg = mysqli_fetch_assoc($res_conf);

$lang = $cfg['idioma'];
$moneda = $cfg['simbolo_moneda'];

// Diccionario extendido
$textos = [
    'es' => [
        'titulo' => 'Registrar Cobro',
        'deudor' => 'Seleccionar Deudor',
        'monto' => 'Monto',
        'metodo' => 'Método',
        'confirmar' => 'Confirmar Pago',
        'volver' => 'Volver',
        'historial' => 'Cobros de Hoy',
        'cliente' => 'Cliente',
        'fecha' => 'Hora'
    ],
    'en' => [
        'titulo' => 'Register Payment',
        'deudor' => 'Select Debtor',
        'monto' => 'Amount',
        'metodo' => 'Method',
        'confirmar' => 'Confirm Payment',
        'volver' => 'Back',
        'historial' => "Today's Collections",
        'cliente' => 'Customer',
        'fecha' => 'Time'
    ]
];
$t = $textos[$lang];

$mensaje = "";

// LÓGICA DE GUARDADO (Igual a la anterior)
if(isset($_POST['pay'])){
    $idc = mysqli_real_escape_string($conexion, $_POST['id_credito']); 
    $mon = mysqli_real_escape_string($conexion, $_POST['monto_pago']); 
    $tip = mysqli_real_escape_string($conexion, $_POST['tipo_pago']);
    
    $ins = "INSERT INTO cobranzas (creditos_idcreditos, monto, tipo_pago, fecha_hora, created_at) 
            VALUES ('$idc', '$mon', '$tip', NOW(), NOW())";
    
    if(mysqli_query($conexion, $ins)){
        mysqli_query($conexion, "UPDATE creditos SET saldo_inicial = saldo_inicial - $mon WHERE idcreditos = '$idc'");
        $check = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT saldo_inicial FROM creditos WHERE idcreditos = '$idc'"));
        
        if($check['saldo_inicial'] <= 0){
            mysqli_query($conexion, "UPDATE creditos SET estado = 2 WHERE idcreditos = '$idc'");
            $mensaje = "<div class='alert success'>✅ Pago Total Recibido</div>";
        } else {
            $mensaje = "<div class='alert success'>💵 Pago de $moneda".number_format($mon,2)." registrado.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" data-theme="<?php echo $cfg['tema_color']; ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo $t['titulo']; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --bg: #f8fafc; --card: #ffffff; --text: #1e293b; --primary: #e11d48; --btn: #10b981; --border: #e2e8f0; }
        [data-theme="dark"] { --bg: #0f172a; --card: #1e293b; --text: #f1f5f9; --primary: #fb7185; --btn: #059669; --border: #334155; }
        
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); padding: 20px; }
        .container { max-width: 900px; margin: auto; display: grid; grid-template-columns: 1fr 1.5fr; gap: 20px; }
        
        .box { background: var(--card); padding: 25px; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); height: fit-content; }
        h2, h3 { color: var(--primary); margin-top: 0; display: flex; align-items: center; gap: 10px; }
        
        input, select { width: 100%; padding: 12px; margin: 10px 0 20px; border-radius: 8px; border: 1px solid var(--border); background: var(--card); color: var(--text); }
        
        button { background: var(--btn); color: white; border: none; padding: 15px; width: 100%; border-radius: 8px; cursor: pointer; font-weight: bold; transition: 0.3s; }
        button:hover { filter: brightness(1.1); transform: translateY(-2px); }

        /* Estilos de la Tabla */
        .history-box { background: var(--card); padding: 25px; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 0.9em; }
        th { text-align: left; padding: 12px; border-bottom: 2px solid var(--border); color: var(--primary); }
        td { padding: 12px; border-bottom: 1px solid var(--border); }
        tr:hover { background: rgba(0,0,0,0.02); }

        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-weight: bold; background: #dcfce7; color: #166534; }
        
        @media (max-width: 768px) { .container { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<div class="container">
    <div class="box">
        <h2><i class="fas fa-cash-register"></i> <?php echo $t['titulo']; ?></h2>
        <?php echo $mensaje; ?>
        <form method="POST">
            <label><?php echo $t['deudor']; ?>:</label>
            <select name="id_credito" required>
                <option value="">-- Seleccionar --</option>
                <?php
                $sql_c = "SELECT c.idcreditos, p.nombres, p.apellidos, c.saldo_inicial 
                          FROM creditos c JOIN emprendedores e ON c.emprendedores_idemprendedores = e.idemprendedores 
                          JOIN personas p ON e.personas_idpersonas = p.idpersonas WHERE c.estado = 1";
                $res_c = mysqli_query($conexion, $sql_c);
                while($c = mysqli_fetch_assoc($res_c)){
                    echo "<option value='{$c['idcreditos']}'>{$c['nombres']} ({$moneda}{$c['saldo_inicial']})</option>";
                }
                ?>
            </select>

            <label><?php echo $t['monto']; ?>:</label>
            <input type="number" step="0.01" name="monto_pago" required>

            <label><?php echo $t['metodo']; ?>:</label>
            <select name="tipo_pago">
                <option value="Efectivo">Efectivo</option>
                <option value="Transferencia">Transferencia</option>
            </select>

            <button type="submit" name="pay"><i class="fas fa-check"></i> <?php echo $t['confirmar']; ?></button>
        </form>
        <div style="margin-top: 20px; text-align:center;">
            <a href="index.php" style="text-decoration:none; color:var(--text); font-size:0.8em;"><i class="fas fa-arrow-left"></i> <?php echo $t['volver']; ?></a>
        </div>
    </div>

    <div class="history-box">
        <h3><i class="fas fa-history"></i> <?php echo $t['historial']; ?></h3>
        <table>
            <thead>
                <tr>
                    <th><?php echo $t['fecha']; ?></th>
                    <th><?php echo $t['cliente']; ?></th>
                    <th><?php echo $t['monto']; ?></th>
                    <th><?php echo $t['metodo']; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Consultar cobros de HOY
                $sql_h = "SELECT cob.monto, cob.tipo_pago, cob.fecha_hora, p.nombres 
                          FROM cobranzas cob
                          JOIN creditos cr ON cob.creditos_idcreditos = cr.idcreditos
                          JOIN emprendedores e ON cr.emprendedores_idemprendedores = e.idemprendedores
                          JOIN personas p ON e.personas_idpersonas = p.idpersonas
                          WHERE DATE(cob.fecha_hora) = CURDATE()
                          ORDER BY cob.fecha_hora DESC LIMIT 10";
                $res_h = mysqli_query($conexion, $sql_h);
                
                if(mysqli_num_rows($res_h) > 0){
                    while($h = mysqli_fetch_assoc($res_h)){
                        $hora = date("H:i", strtotime($h['fecha_hora']));
                        echo "<tr>
                                <td>$hora</td>
                                <td><b>{$h['nombres']}</b></td>
                                <td>{$moneda}".number_format($h['monto'], 2)."</td>
                                <td><small>{$h['tipo_pago']}</small></td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' style='text-align:center; opacity:0.5;'>No hay cobros registrados hoy</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>