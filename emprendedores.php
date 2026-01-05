<?php include 'config.php'; ?>
<form method="POST">
    <select name="personas_idpersonas">
        <?php
        $res = mysqli_query($conexion, "SELECT idpersonas, nombres FROM personas");
        while($p = mysqli_fetch_assoc($res)){
            echo "<option value='{$p['idpersonas']}'>{$p['nombres']}</option>";
        }
        ?>
    </select>
    <input type="text" name="tipo_negocio" placeholder="Tipo de Negocio">
    <input type="text" name="rubro" placeholder="Rubro">
    <input type="text" name="producto_principal" placeholder="Producto">
    <input type="number" name="limite_credito" placeholder="Límite $">
    <button type="submit" name="btn_e">Vincular</button>
</form>

<?php
if(isset($_POST['btn_e'])){
    $idp = $_POST['personas_idpersonas'];
    $tipo = $_POST['tipo_negocio'];
    $rubro = $_POST['rubro'];
    $prod = $_POST['producto_principal'];
    $lim = $_POST['limite_credito'];

    // SQL con los nombres de tu tabla EMPRENDEDORES
    $sql = "INSERT INTO emprendedores (personas_idpersonas, tipo_negocio, rubro, producto_principal, limite_credito, fecha_registro, created_at) 
            VALUES ('$idp', '$tipo', '$rubro', '$prod', '$lim', NOW(), NOW())";

    if(mysqli_query($conexion, $sql)) echo "✅ Emprendedor registrado";
}
?>