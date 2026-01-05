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
            // Solo muestra contratos de emprendedores activos
            $res = mysqli_query($conexion, "SELECT c.idContratos, p.nombres, c.monto_total FROM Contratos c JOIN emprendedores e ON c.emprendedores_idemprendedores = e.idemprendedores JOIN personas p ON e.personas_idpersonas = p.idpersonas");
            while($c = mysqli_fetch_assoc($res)) echo "<option value='{$c['idContratos']}'>Contrato #{$c['idContratos']} - {$c['nombres']} ({$c['monto_total']})</option>";
            ?>
        </select><br><br>
        <input type="number" step="0.01" name="cuota" placeholder="Valor Cuota Mensual" required><br><br>
        <input type="number" name="dia" placeholder="Día de pago sugerido (1-30)" required><br><br>
        <button type="submit" name="activar">Activar Crédito en Sistema</button>
    </form>

    <?php
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
            echo "<p style='color:red; font-weight:bold;'>❌ Error: El emprendedor aún tiene un crédito activo con saldo pendiente. Debe saldarlo antes de activar uno nuevo.</p>";
        } else {
            // 3. Si no tiene deudas activas, creamos el nuevo crédito
            $sql = "INSERT INTO creditos (monto_inicial, saldo_inicial, fecha_inicio, estado, dia_de_pago, cuota_mensual, Contratos_idContratos, emprendedores_idemprendedores, created_at) 
                    VALUES ('$monto', '$monto', NOW(), 1, '$dia', '$cuo', '$con', '$emp_id', NOW())";
            
            if(mysqli_query($conexion, $sql)) {
                echo "<p style='color:green; font-weight:bold;'>✅ ¡Nuevo crédito activado! Este es un nuevo préstamo para el historial del emprendedor.</p>";
            }
        }
    }
    ?>
    <hr>
    <a href="index.php">Volver al Buscador</a>
</body>
</html>