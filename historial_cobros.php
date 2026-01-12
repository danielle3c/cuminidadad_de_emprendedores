<?php 
session_start();
if (!isset($_SESSION['usuario_id'])) { header("Location: login.php"); exit(); }
include 'config.php'; 

$res_conf = mysqli_query($conexion, "SELECT * FROM configuraciones WHERE id = 1");
$cfg = mysqli_fetch_assoc($res_conf);
$moneda = $cfg['simbolo_moneda'];

$buscar = isset($_GET['buscar']) ? mysqli_real_escape_string($conexion, $_GET['buscar']) : '';
?>

<!DOCTYPE html>
<html lang="es" data-theme="<?php echo $cfg['tema_color']; ?>">
<head>
    <meta charset="UTF-8">
    <title>Historial de Cobros</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --bg: #f3f6f9; --white: #ffffff; --border: #e3e6f0; --text: #333; --blue-btn: #448aff; --green-btn: #4caf50; }
        [data-theme="dark"] { --bg: #1a202c; --white: #2d3748; --text: #edf2f7; --border: #4a5568; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); color: var(--text); margin: 0; padding: 20px; }
        .container { max-width: 1100px; margin: auto; }
        .navbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .btn-nav-white { background: var(--white); border: 1px solid var(--border); padding: 10px 20px; border-radius: 10px; text-decoration: none; color: #333; font-weight: bold; display: flex; align-items: center; gap: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .btn-new-blue { background: var(--blue-btn); color: white; border: none; border-radius: 10px; padding: 10px 25px; text-decoration: none; display: flex; align-items: center; gap: 8px; font-weight: bold; }
        .title-page { font-size: 1.3rem; font-weight: bold; display: flex; align-items: center; gap: 10px; }
        .search-bar { display: flex; gap: 10px; margin-bottom: 20px; }
        .search-input { flex-grow: 1; padding: 12px 20px; border-radius: 10px; border: 1px solid var(--border); background: var(--white); color: var(--text); font-size: 1rem; }
        .btn-search-green { background: var(--green-btn); color: white; border: none; padding: 0 20px; border-radius: 10px; cursor: pointer; font-size: 1.2rem; }
        .table-card { background: var(--white); border-radius: 15px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8f9fc; padding: 15px; text-align: left; border-bottom: 2px solid var(--border); color: #4e73df; }
        td { padding: 15px; border-bottom: 1px solid var(--border); }
        .badge-saldo { color: #e74c3c; font-weight: bold; } /* Rojo para resaltar deuda pendiente */
    </style>
</head>
<body>

<div class="container">
    <div class="navbar">
        <a href="index.php" class="btn-nav-white"><i class="fas fa-home"></i> Inicio</a>
        <div class="title-page"><i class="fas fa-list-ul"></i> Historial de Cobranzas</div>
        <a href="cobranzas.php" class="btn-new-blue"><i class="fas fa-plus"></i> Nuevo</a>
    </div>

    <form method="GET" class="search-bar">
        <input type="text" name="buscar" class="search-input" placeholder="Buscar por cliente..." value="<?php echo htmlspecialchars($buscar); ?>">
        <button type="submit" class="btn-search-green"><i class="fas fa-search"></i></button>
    </form>

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>Fecha / Hora</th>
                    <th>Cliente</th>
                    <th>Monto Pagado</th>
                    <th>Saldo Restante</th> <th>Método</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $where = $buscar ? "AND (p.nombres LIKE '%$buscar%')" : "";
                // La consulta ahora también trae el saldo_inicial (que es el saldo actual en tu lógica)
                $sql = "SELECT cob.monto, cob.tipo_pago, cob.fecha_hora, p.nombres, cr.saldo_inicial as saldo_actual
                        FROM cobranzas cob
                        JOIN creditos cr ON cob.creditos_idcreditos = cr.idcreditos
                        JOIN emprendedores e ON cr.emprendedores_idemprendedores = e.idemprendedores
                        JOIN personas p ON e.personas_idpersonas = p.idpersonas
                        WHERE 1=1 $where
                        ORDER BY cob.fecha_hora DESC";
                
                $res = mysqli_query($conexion, $sql);
                if(mysqli_num_rows($res) > 0){
                    while($h = mysqli_fetch_assoc($res)){
                        $fecha_fmt = date("d/m/Y H:i", strtotime($h['fecha_hora']));
                        echo "<tr>
                                <td>$fecha_fmt</td>
                                <td><b>{$h['nombres']}</b></td>
                                <td style='color: #27ae60; font-weight: bold;'>+ {$moneda}".number_format($h['monto'], 2)."</td>
                                <td class='badge-saldo'>{$moneda}".number_format($h['saldo_actual'], 2)."</td>
                                <td><small>{$h['tipo_pago']}</small></td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' style='text-align:center; padding:40px;'>No hay registros.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>