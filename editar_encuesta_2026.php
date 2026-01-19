<?php
include 'config.php';

$id = $_GET['id'];
$mensaje = "";

$res = mysqli_query($conexion,"SELECT * FROM encuesta_2026 WHERE id_encuesta='$id'");
$e = mysqli_fetch_assoc($res);

if (isset($_POST['actualizar'])) {

    $usuario_id = $_SESSION['usuario_id'];

    $sql = "UPDATE encuesta_2026 SET
        nombre_local = '".mysqli_real_escape_string($conexion,$_POST['local'])."',
        direccion = '".mysqli_real_escape_string($conexion,$_POST['direccion'])."',
        representante = '".mysqli_real_escape_string($conexion,$_POST['representante'])."',
        telefono = '".mysqli_real_escape_string($conexion,$_POST['telefono'])."',
        necesidades_productivas = '".mysqli_real_escape_string($conexion,$_POST['necesidades'])."',
        participa_programa_beneficios = '".$_POST['participa']."',
        beneficio_ofrecido = '".mysqli_real_escape_string($conexion,$_POST['beneficio'])."',
        observaciones = '".mysqli_real_escape_string($conexion,$_POST['observaciones'])."',
        updated_at = NOW(),
        updated_by = '$usuario_id'
        WHERE id_encuesta = '$id'";

    if(mysqli_query($conexion,$sql)){
        $mensaje="✅ Actualizado";
        $res = mysqli_query($conexion,"SELECT * FROM encuesta_2026 WHERE id_encuesta='$id'");
        $e = mysqli_fetch_assoc($res);
    }
}
?>
<!DOCTYPE html>
<html>
<body>
<h2>Editar Encuesta</h2>
<?= $mensaje ?>
<form method="POST">
<input type="text" name="local" value="<?= $e['nombre_local'] ?>">
<input type="text" name="direccion" value="<?= $e['direccion'] ?>">
<input type="text" name="representante" value="<?= $e['representante'] ?>">
<input type="text" name="telefono" value="<?= $e['telefono'] ?>">
<textarea name="necesidades"><?= $e['necesidades_productivas'] ?></textarea>
<select name="participa">
<option value="1" <?= $e['participa_programa_beneficios']==1?'selected':'' ?>>Sí</option>
<option value="0" <?= $e['participa_programa_beneficios']==0?'selected':'' ?>>No</option>
</select>
<textarea name="beneficio"><?= $e['beneficio_ofrecido'] ?></textarea>
<textarea name="observaciones"><?= $e['observaciones'] ?></textarea>
<button name="actualizar">Actualizar</button>
</form>
</body>
</html>
