<?php include 'config.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Caja - Cobranzas</title>
    <style>
        body { font-family: sans-serif; background: #fff1f2; padding: 20px; }
        .box { background: white; padding: 20px; border-radius: 10px; max-width: 500px; margin: auto; border-top: 5px solid #e11d48; }
        input, select { width: 100%; padding: 10px; margin: 10px 0; border-radius: 5px; border: 1px solid #ddd; }
        button { background: #43b02a; color: white; border: none; padding: 12px; width: 100%; border-radius: 5px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Registrar Pago</h2>
        <form method="POST">
            <label>Crédito del Emprendedor:</label>
            <select name="id_credito" required>
                <?php
                $sql = "SELECT c.idcreditos, p.nombres, p.apellidos, c.saldo_inicial 
                        FROM creditos c 
                        JOIN emprendedores e ON c.emprendedores_idemprendedores = e.idemprendedores 
                        JOIN personas p ON e.personas_idpersonas = p.idpersonas WHERE c.estado = 1";
                $res = mysqli_query($conexion, $sql);
                while($c = mysqli_fetch_assoc($res)) echo "<option value='{$c['idcreditos']}'>{$c['nombres']} (Saldo: {$c['saldo_inicial']})</option>";
                ?>
            </select>
            <input type="number" step="0.01" name="monto_pago" placeholder="Monto que entrega" required>
            <select name="tipo_pago">
                <option value="Efectivo">Efectivo</option>
                <option value="Transferencia">Transferencia</option>
            </select>
            <button type="submit" name="pay">Registrar Cobro</button>
        </form>

        <?php
        if(isset($_POST['pay'])){
            $idc = $_POST['id_credito']; $mon = $_POST['monto_pago']; $tip = $_POST['tipo_pago'];
            
            // 1. Guardar en cobranzas
            mysqli_query($conexion, "INSERT INTO cobranzas (creditos_idcreditos, monto, tipo_pago, fecha_hora, created_at) VALUES ('$idc', '$mon', '$tip', NOW(), NOW())");
            
            // 2. Restar saldo en la tabla creditos automáticamente
            mysqli_query($conexion, "UPDATE creditos SET saldo_inicial = saldo_inicial - $mon WHERE idcreditos = '$idc'");
            
            echo "Pago registrado y saldo actualizado.";
        }
        ?>
    </div>
</body>
</html>