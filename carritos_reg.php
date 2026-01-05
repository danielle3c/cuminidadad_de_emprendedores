<?php include 'config.php'; ?>
<!DOCTYPE html>
<html>
<head><title>Registro de Carritos</title></head>
<body style="font-family: sans-serif; padding: 20px;">
    <h2>Asignación de Carritos</h2>
    <form method="POST">
        <select name="id_emp">
            <?php
            $res = mysqli_query($conexion, "SELECT e.idemprendedores, p.nombres FROM emprendedores e JOIN personas p ON e.personas_idpersonas = p.idpersonas");
            while($e = mysqli_fetch_assoc($res)) echo "<option value='{$e['idemprendedores']}'>{$e['nombres']}</option>";
            ?>
        </select><br><br>
        <input type="text" name="nom_c" placeholder="Nombre/Identificador del Carrito" required><br><br>
        <textarea name="equipo" placeholder="Equipamiento incluido"></textarea><br><br>
        <button type="submit" name="reg_c">Registrar Carrito</button>
    </form>

    <?php
    if(isset($_POST['reg_c'])){
        $ide = $_POST['id_emp']; $nom = $_POST['nom_c']; $equ = $_POST['equipo'];
        $sql = "INSERT INTO carritos (nombre_carrito, equipamiento, emprendedores_idemprendedores, created_at) VALUES ('$nom', '$equ', '$ide', NOW())";
        if(mysqli_query($conexion, $sql)) echo "✅ Carrito registrado.";
    }
    ?>
</body>
</html>