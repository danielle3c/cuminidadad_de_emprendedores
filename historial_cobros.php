<?php 
session_start();
if (!isset($_SESSION['usuario_id'])) { header("Location: login.php"); exit(); }
include 'config.php'; 

$res_conf = mysqli_query($conexion, "SELECT * FROM configuraciones WHERE id = 1");
$cfg = mysqli_fetch_assoc($res_conf);
$moneda = $cfg['simbolo_moneda'];
?>

<!DOCTYPE html>
<html lang="es" data-theme="<?php echo $cfg['tema_color']; ?>">
<head>
    <meta charset="UTF-8">
    <title>Historial de Cobranzas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --bg: #f8fafc; --card: #ffffff; --text: #1e293b; --primary: #e11d48; --border: #e2e8f0; }
        [data-theme="dark"] { --bg: #0f172a; --card: #1e293b; --text: #f1f5f9; --primary: #fb7185; --border: #334155; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); padding: 20px; }
        .container { max-width: 800px; margin: auto; background: var(--card); padding: 25px; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        h3 { color: var(--primary); margin: 0; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 12px; border-bottom: 2px solid var(--border); }
        td { padding: 12px; border-bottom: 1px solid var(--border); }
        .btn-new { background: var(--primary); color: white; text-decoration: none; padding: 10px 15px; border-radius: 8px; font-size: 0.8em; font-weight: bold; }
        .btn-home { display: inline-block; margin-top: 20px; text-decoration: none; color: var(--text); font-size: 0.9em; opacity: 0.7; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h3><i class="fas fa-list"></i> Historial de Hoy</h3>
            <a href="cobranzas.php" class="btn-new"><i class="fas fa-plus"></i> Nuevo Cobro</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Hora</th>
                    <th>Cliente</th>
                    <th>Monto</th>
                    <th>Tipo</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT cob.monto, cob.tipo_pago, cob.fecha_hora, p.nombres 
                        FROM cobranzas cob
                        JOIN creditos cr ON cob.creditos_idcreditos = cr.idcreditos
                        JOIN emprendedores e ON cr.emprendedores_idemprendedores = e.idemprendedores
                        JOIN personas p ON e.personas_idpersonas = p.idpersonas
                        WHERE DATE(cob.fecha_hora) = CURDATE() ORDER BY cob.fecha_hora DESC";
                $res = mysqli_query($conexion, $sql);
                while($h = mysqli_fetch_assoc($res)){
                    $hora = date("H:i", strtotime($h['fecha_hora']));
                    echo "<tr>
                            <td>$hora</td>
                            <td>{$h['nombres']}</td>
                            <td>$moneda" . number_format($h['monto'], 2) . "</td>
                            <td><small>{$h['tipo_pago']}</small></td>
                        </tr>";
                }
                ?>
            </tbody>
        </table>
        <div style="text-align: center;">
            <a href="index.php" class="btn-home"><i class="fas fa-arrow-left"></i> Volver al Inicio</a>
        </div>
    </div>
</body>
</html>