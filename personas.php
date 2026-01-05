<?php include 'config.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Gestión de Personas</title>
    <style>
        body { font-family: sans-serif; background: #f8fafc; padding: 40px; }
        .box { background: white; padding: 25px; border-radius: 12px; max-width: 600px; margin: 0 auto 30px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        input { width: 100%; padding: 12px; margin: 8px 0; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        button { background: #2563eb; color: white; border: none; padding: 12px 20px; border-radius: 6px; cursor: pointer; width: 100%; font-size: 16px; }
        .back { text-decoration: none; color: #64748b; display: block; margin-bottom: 20px; }
    </style>
</head>
<body>
    <a href="index.php" class="back">← Volver al Panel</a>
    <div class="box">
        <h2>Registrar Persona</h2>
        <form method="POST">
            <input type="text" name="nom" placeholder="Nombres" required>
            <input type="text" name="ape" placeholder="Apellidos" required>
            <input type="date" name="fec" required title="Fecha de Nacimiento">
            <input type="text" name="tel" placeholder="Teléfono">
            <input type="email" name="ema" placeholder="Email">
            <button type="submit" name="save">Guardar en Sistema</button>
        </form>
        <?php
        if(isset($_POST['save'])){
            $n=$_POST['nom']; $a=$_POST['ape']; $f=$_POST['fec']; $t=$_POST['tel']; $e=$_POST['ema'];
            $q = "INSERT INTO personas (nombres, apellidos, fecha_nacimiento, telefono, email, created_at) VALUES ('$n','$a','$f','$t','$e', NOW())";
            if(mysqli_query($conexion, $q)) echo "<p style='color:green'>¡Registrado!</p>";
        }
        ?>
    </div>
</body>
</html>