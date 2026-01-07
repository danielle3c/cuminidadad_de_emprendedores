<?php 
include 'config.php'; 

$id = $_GET['id'];
$res_query = mysqli_query($conexion, "SELECT * FROM carritos WHERE id = $id");
$data = mysqli_fetch_assoc($res_query);

if(isset($_POST['update_car'])){
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre_persona']); 
    $telef  = mysqli_real_escape_string($conexion, $_POST['telefono']); 
    $carro  = mysqli_real_escape_string($conexion, $_POST['nombre_c']); 
    $desc   = mysqli_real_escape_string($conexion, $_POST['desc']); 
    $equip  = mysqli_real_escape_string($conexion, $_POST['equip']);
    $asist  = mysqli_real_escape_string($conexion, $_POST['asistencia']); 

    $sql = "UPDATE carritos SET 
            nombre_responsable = '$nombre', 
            telefono_responsable = '$telef', 
            nombre_carrito = '$carro', 
            descripcion = '$desc', 
            equipamiento = '$equip', 
            asistencia = '$asist' 
            WHERE id = $id";

    if(mysqli_query($conexion, $sql)){
        header("Location: lista_carritos.php?msg=editado");
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Registro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: sans-serif; background: #f4f7f6; display: flex; justify-content: center; padding: 40px; }
        .form-card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 100%; max-width: 500px; }
        input, textarea, select { width: 100%; padding: 10px; margin: 10px 0 20px 0; border: 1px solid #ccc; border-radius: 8px; box-sizing: border-box; }
        .btn-update { background: #43b02a; color: white; border: none; padding: 15px; width: 100%; border-radius: 8px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>

<div class="form-card">
    <h3><i class="fas fa-edit"></i> Modificar Registro</h3>
    <form method="POST">
        <label>Responsable:</label>
        <input type="text" name="nombre_persona" value="<?php echo $data['nombre_responsable']; ?>" required>

        <label>Teléfono:</label>
        <input type="text" name="telefono" value="<?php echo $data['telefono_responsable']; ?>">

        <label>Carrito:</label>
        <input type="text" name="nombre_c" value="<?php echo $data['nombre_carrito']; ?>" required>

        <label>Asistencia:</label>
        <select name="asistencia">
            <option value="SÍ VINO" <?php if($data['asistencia'] == 'SÍ VINO') echo 'selected'; ?>>SÍ VINO</option>
            <option value="NO VINO" <?php if($data['asistencia'] == 'NO VINO') echo 'selected'; ?>>NO VINO</option>
        </select>

        <label>Estado Estético:</label>
        <textarea name="desc"><?php echo $data['descripcion']; ?></textarea>

        <label>Equipamiento:</label>
        <textarea name="equip"><?php echo $data['equipamiento']; ?></textarea>

        <button type="submit" name="update_car" class="btn-update">Actualizar Cambios</button>
        <a href="lista_carritos.php" style="display:block; text-align:center; margin-top:15px; text-decoration:none; color:#666;">Cancelar</a>
    </form>
</div>

</body>
</html>