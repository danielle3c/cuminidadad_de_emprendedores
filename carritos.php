<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Carritos</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; padding: 20px; }
        .box { background: white; padding: 25px; border-radius: 12px; max-width: 600px; margin: auto; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        input, select, textarea { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { background: #6366f1; color: white; border: none; padding: 12px; width: 100%; border-radius: 8px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>
<div class="box">
    <h2>🛒 Registrar Carrito / Puesto</h2>
    <form method="POST">
        <label>Asignar a Emprendedor:</label>
        <select name="emp_id" required>
            <?php
            $res = mysqli_query($conexion, "SELECT e.idemprendedores, p.nombres, p.apellidos FROM emprendedores e JOIN personas p ON e.personas_idpersonas = p.idpersonas WHERE e.deleted_at = 0");
            while($e = mysqli_fetch_assoc($res)) echo "<option value='{$e['idemprendedores']}'>{$e['nombres']} {$e['apellidos']}</option>";
            ?>
        </select>
        
        <input type="text" name="nombre_c" placeholder="Nombre del Carrito (Ej: Puesto 01 - Plaza)" required>
        <textarea name="desc" placeholder="Descripción del estado del carrito..."></textarea>
        <textarea name="equip" placeholder="Equipamiento (Ej: Freidora, Cilindro gas, etc.)"></textarea>
        
        <button type="submit" name="save_car">Guardar Carrito</button>
    </form>

    <?php
    if(isset($_POST['save_car'])){
        $ide = $_POST['emp_id']; $nom = $_POST['nombre_c']; $des = $_POST['desc']; $equ = $_POST['equip'];
        $sql = "INSERT INTO carritos (nombre_carrito, descripcion, equipamiento, emprendedores_idemprendedores, created_at) VALUES ('$nom', '$des', '$equ', '$ide', NOW())";
        if(mysqli_query($conexion, $sql)) echo "<p style='color:green;'>✅ Carrito registrado correctamente.</p>";
    }
    ?>
</div>
</body>
</html>