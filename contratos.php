<?php include 'config.php'; ?>
<!DOCTYPE html>
<html>
<head><title>Generar Contrato</title></head>
<body style="font-family: sans-serif; padding: 20px;">
    <h2>📝 Nuevo Contrato de Financiamiento</h2>
    <form method="POST">
        <label>Emprendedor:</label><br>
        <select name="id_emprendedor" required>
            <?php
            $res = mysqli_query($conexion, "SELECT e.idemprendedores, p.nombres, p.apellidos FROM emprendedores e JOIN personas p ON e.personas_idpersonas = p.idpersonas");
            while($e = mysqli_fetch_assoc($res)) echo "<option value='{$e['idemprendedores']}'>{$e['nombres']} {$e['apellidos']}</option>";
            ?>
        </select><br><br>
        <input type="number" step="0.01" name="monto_total" placeholder="Monto Total $" required><br><br>
        <input type="number" name="plazo" placeholder="Plazo (Meses)" required><br><br>
        <button type="submit" name="save_con">Guardar Contrato</button>
    </form>

    <?php
    if(isset($_POST['save_con'])){
        $ide = $_POST['id_emprendedor']; $monto = $_POST['monto_total']; $plazo = $_POST['plazo'];
        $sql = "INSERT INTO Contratos (fecha_firma, monto_total, plazo_meses, total_pagado, emprendedores_idemprendedores, created_at, estado) 
                VALUES (CURDATE(), '$monto', '$plazo', 0, '$ide', NOW(), 1)";
        if(mysqli_query($conexion, $sql)) echo "✅ Contrato generado con éxito.";
    }
    ?>
</body>
</html>