<?php 
session_start();
if (!isset($_SESSION['usuario_id'])) { header("Location: login.php"); exit(); }
include 'config.php'; 

$res_conf = mysqli_query($conexion, "SELECT * FROM configuraciones WHERE id = 1");
$cfg = mysqli_fetch_assoc($res_conf);
$lang = $cfg['idioma'];
$moneda = $cfg['simbolo_moneda'];

$textos = [
    'es' => ['titulo' => 'Registrar Cobro', 'deudor' => 'Seleccionar Deudor', 'monto' => 'Monto', 'metodo' => 'Método', 'confirmar' => 'Confirmar Pago', 'volver' => 'Inicio', 'ver_historial' => 'Ver Historial'],
    'en' => ['titulo' => 'Register Payment', 'deudor' => 'Select Debtor', 'monto' => 'Amount', 'metodo' => 'Method', 'confirmar' => 'Confirm Payment', 'volver' => 'Home', 'ver_historial' => 'View History']
];
$t = $textos[$lang];
$mensaje = "";

if(isset($_POST['pay'])){
    $idc = mysqli_real_escape_string($conexion, $_POST['id_credito']); 
    $mon = mysqli_real_escape_string($conexion, $_POST['monto_pago']); 
    $tip = mysqli_real_escape_string($conexion, $_POST['tipo_pago']);
    
    $ins = "INSERT INTO cobranzas (creditos_idcreditos, monto, tipo_pago, fecha_hora, created_at) VALUES ('$idc', '$mon', '$tip', NOW(), NOW())";
    if(mysqli_query($conexion, $ins)){
        mysqli_query($conexion, "UPDATE creditos SET saldo_inicial = saldo_inicial - $mon WHERE idcreditos = '$idc'");
        $mensaje = "<div class='alert success'>Pago de $moneda".number_format($mon,2)." registrado.</div>";
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
        :root { --bg: #f8fafc; --card: #ffffff; --text: #1e293b; --primary: #e11d48; --btn: #10b981; --border: #e2e8f0; --secondary: #64748b; }
        [data-theme="dark"] { --bg: #0f172a; --card: #1e293b; --text: #f1f5f9; --primary: #fb7185; --btn: #059669; --border: #334155; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); display: flex; justify-content: center; padding: 40px 20px; }
        .box { background: var(--card); padding: 30px; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); width: 100%; max-width: 450px; }
        h2 { color: var(--primary); margin-top: 0; text-align: center; }
        input, select { width: 100%; padding: 12px; margin: 10px 0 20px; border-radius: 8px; border: 1px solid var(--border); background: var(--card); color: var(--text); box-sizing: border-box; }
        button { background: var(--btn); color: white; border: none; padding: 15px; width: 100%; border-radius: 8px; cursor: pointer; font-weight: bold; }
        .nav-links { display: flex; justify-content: space-between; margin-top: 25px; border-top: 1px solid var(--border); padding-top: 15px; }
        .nav-links a { text-decoration: none; color: var(--secondary); font-size: 0.9em; }
        .alert { background: #dcfce7; color: #166534; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="box">
        <h2><i class="fas fa-cash-register"></i> <?php echo $t['titulo']; ?></h2>
        <?php echo $mensaje; ?>
        <form method="POST">
            <label><?php echo $t['deudor']; ?>:</label>
            <select name="id_credito" required>
                <option value="">-- Seleccionar --</option>
                <?php
                $sql_c = "SELECT c.idcreditos, p.nombres, c.saldo_inicial FROM creditos c JOIN emprendedores e ON c.emprendedores_idemprendedores = e.idemprendedores JOIN personas p ON e.personas_idpersonas = p.idpersonas WHERE c.estado = 1";
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
        <div class="nav-links">
            <a href="index.php"><i class="fas fa-home"></i> <?php echo $t['volver']; ?></a>
            <a href="historial_cobros.php"><i class="fas fa-history"></i> <?php echo $t['ver_historial']; ?></a>
        </div>
    </div>
</body>
</html>