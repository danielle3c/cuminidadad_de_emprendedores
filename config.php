<?php include 'config.php'; ?>
<form method="POST">
    <input type="text" name="nombres" placeholder="Nombres" required>
    <input type="text" name="apellidos" placeholder="Apellidos" required>
    <input type="date" name="fecha_nacimiento" required>
    <input type="text" name="telefono" placeholder="Teléfono">
    <input type="email" name="email" placeholder="Email">
    <button type="submit" name="guardar">Guardar</button>
</form>

<?php
if(isset($_POST['guardar'])){
    // Nombres de variables PHP
    $nom = $_POST['nombres'];
    $ape = $_POST['apellidos'];
    $fec = $_POST['fecha_nacimiento'];
    $tel = $_POST['telefono'];
    $ema = $_POST['email'];

    // ¡Nombres EXACTOS de tu base de datos!
    $sql = "INSERT INTO personas (nombres, apellidos, fecha_nacimiento, telefono, email, created_at, estado) 
            VALUES ('$nom', '$ape', '$fec', '$tel', '$ema', NOW(), 1)";

    if(mysqli_query($conexion, $sql)){
        echo "✅ Guardado en la tabla 'personas'";
    } else {
        echo "❌ Error: " . mysqli_error($conexion);
    }
}
?>