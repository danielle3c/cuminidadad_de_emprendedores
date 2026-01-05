<?php include 'config.php'; ?>
<!DOCTYPE html>
<html>
<head><title>Activar Crédito</title></head>
<body style="font-family: sans-serif; padding: 20px;">
    <h2>Activación de Créditos</h2>
    <form method="POST">
        <label>Seleccione Contrato Firmado:</label><br>
        <select name="id_contrato" required>
            <?php
            // Solo muestra contratos que pertenecen a emprendedores activos
            $res = mysqli_query($conexion, "SELECT c.idContratos, p.nombres, c.monto_total FROM Contratos c JOIN emprendedores e ON c.emprendedores_idemprendedores = e.idemprendedores JOIN personas p ON e.personas_idpersonas = p.idpersonas");
            while($c = mysqli_fetch_assoc($res)) echo "<option value='{$c['idContratos']}'>Contrato #{$c['idContratos']} - {$c['nombres']} (${$c['monto_total']})</option>";
            ?>
        </select><br><br>
        <input type="number" step="0.01" name="cuota" placeholder="Valor Cuota Mensual" required><br><br>
        <input type="number" name="dia" placeholder="Día de pago sugerido (1-30)" required><br><br>
        <button type="submit" name="activar">Activar Crédito en Sistema</button>
    </form>

    <?php
    if(isset($_POST['activar'])){
        $con = $_POST['id_contrato']; $cuo = $_POST['cuota']; $dia = $_POST['dia'];
        // Obtenemos el monto del contrato para ponerlo como saldo inicial
        $info = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT monto_total, emprendedores_idemprendedores FROM Contratos WHERE idContratos = '$con'"));
        $monto = $info['monto_total']; $emp_id = $info['emprendedores_idemprendedores'];

        $sql = "INSERT INTO creditos (monto_inicial, saldo_inicial, fecha_inicio, estado, dia_de_pago, cuota_mensual, Contratos_idContratos, emprendedores_idemprendedores, created_at) 
                VALUES ('$monto', '$monto', NOW(), 1, '$dia', '$cuo', '$con', '$emp_id', NOW())";
        
        if(mysqli_query($conexion, $sql)) echo " Crédito activado y saldo cargado.";
    }
    ?>
</body>
</html>