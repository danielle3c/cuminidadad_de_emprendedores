<?php 
include 'config.php'; 

$id = $_GET['id'];
$res_query = mysqli_query($conexion, "SELECT * FROM carritos WHERE id = $id");
$data = mysqli_fetch_assoc($res_query);

// Separamos la fecha y la hora del valor guardado en 'created_at' (Entrada)
$fecha_db = date('Y-m-d', strtotime($data['created_at']));
$hora_db  = date('H:i', strtotime($data['created_at']));

// Obtenemos la hora de salida (si existe)
$hora_salida_db = (!empty($data['hora_salida'])) ? date('H:i', strtotime($data['hora_salida'])) : "";

if(isset($_POST['update_car'])){
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre_persona']); 
    $telef  = mysqli_real_escape_string($conexion, $_POST['telefono']); 
    $carro  = mysqli_real_escape_string($conexion, $_POST['nombre_c']); 
    $desc   = mysqli_real_escape_string($conexion, $_POST['desc']); 
    $equip  = mysqli_real_escape_string($conexion, $_POST['equip']);
    $asist  = mysqli_real_escape_string($conexion, $_POST['asistencia']); 
    
    // Capturamos la nueva fecha y hora de entrada
    $n_fecha = $_POST['fecha_reg'];
    $n_hora  = $_POST['hora_reg'];
    $fecha_final = $n_fecha . " " . $n_hora . ":00";

    // Capturamos la hora de salida
    $n_hora_salida = $_POST['hora_salida'];
    $hora_salida_val = (!empty($n_hora_salida)) ? "'$n_hora_salida'" : "NULL";

    $sql = "UPDATE carritos SET 
            nombre_responsable = '$nombre', 
            telefono_responsable = '$telef', 
            nombre_carrito = '$carro', 
            descripcion = '$desc', 
            equipamiento = '$equip', 
            asistencia = '$asist',
            created_at = '$fecha_final',
            hora_salida = $hora_salida_val 
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Registro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f1f5f9; display: flex; justify-content: center; padding: 20px; }
        .form-card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); width: 100%; max-width: 550px; }
        h3 { color: #1e293b; text-align: center; margin-bottom: 25px; }
        label { display: block; font-weight: bold; font-size: 0.85rem; color: #55b83e; margin-bottom: 5px; text-transform: uppercase; }
        input, textarea, select { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #e2e8f0; border-radius: 10px; box-sizing: border-box; font-size: 1rem; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .grid-3 { display: grid; grid-template-columns: 1.2fr 1fr 1fr; gap: 15px; }
        .btn-update { background: #55b83e; color: white; border: none; padding: 15px; width: 100%; border-radius: 10px; cursor: pointer; font-weight: bold; font-size: 1rem; transition: 0.3s; }
        .btn-update:hover { background: #55b83e; }
        .tag-info { font-size: 0.7rem; color: #64748b; margin-top: -10px; margin-bottom: 10px; display: block; }
    </style>
</head>
<body>

<div class="form-card">
    <h3><i class="fas fa-edit"></i> Modificar Registro</h3>
    <form method="POST">
        
        <div class="grid-3">
            <div>
                <label><i class="fas fa-calendar"></i> Fecha:</label>
                <input type="date" name="fecha_reg" value="<?php echo $fecha_db; ?>" required>
            </div>
            <div>
                <label><i class="fas fa-sign-in-alt"></i> Entrada:</label>
                <input type="time" name="hora_reg" value="<?php echo $hora_db; ?>" required>
            </div>
            <div>
                <label><i class="fas fa-sign-out-alt"></i> Salida:</label>
                <input type="time" name="hora_salida" value="<?php echo $hora_salida_db; ?>">
            </div>
        </div>
        <span class="tag-info">* Puedes dejar la salida vacía si aún no se retira.</span>

        <div class="grid-2">
            <div>
                <label>Responsable:</label>
                <input type="text" name="nombre_persona" value="<?php echo htmlspecialchars($data['nombre_responsable']); ?>" required>
            </div>
            <div>
                <label>Teléfono:</label>
                <input type="text" name="telefono" value="<?php echo htmlspecialchars($data['telefono_responsable']); ?>">
            </div>
        </div>

        <label>Carrito:</label>
        <input type="text" name="nombre_c" value="<?php echo htmlspecialchars($data['nombre_carrito']); ?>" required>

        <label>Asistencia:</label>
        <select name="asistencia">
            <option value="SÍ VINO" <?php if($data['asistencia'] == 'SÍ VINO') echo 'selected'; ?>>SÍ VINO</option>
            <option value="NO VINO" <?php if($data['asistencia'] == 'NO VINO') echo 'selected'; ?>>NO VINO</option>
        </select>

        <label>Estado Estético:</label>
        <textarea name="desc" rows="2"><?php echo htmlspecialchars($data['descripcion']); ?></textarea>

        <label>Equipamiento:</label>
        <textarea name="equip" rows="2"><?php echo htmlspecialchars($data['equipamiento']); ?></textarea>

        <button type="submit" name="update_car" class="btn-update">
            <i class="fas fa-save"></i> Guardar Cambios
        </button>
        <a href="lista_carritos.php" style="display:block; text-align:center; margin-top:15px; text-decoration:none; color:#64748b; font-weight: 600;">
            <i class="fas fa-times"></i> Cancelar
        </a>
    </form>
</div>

</body>
</html>