<?php 
include 'config.php'; 

// 1. Cargar configuración del sistema
$res_conf = mysqli_query($conexion, "SELECT * FROM configuraciones WHERE id = 1");
$cfg = mysqli_fetch_assoc($res_conf);

$mensaje = "";

// 2. Lógica para guardar el registro
if(isset($_POST['save_car'])){
    $nombre_persona = mysqli_real_escape_string($conexion, $_POST['nombre_persona']); 
    $telefono       = mysqli_real_escape_string($conexion, $_POST['telefono']); 
    $nom_carrito    = mysqli_real_escape_string($conexion, $_POST['nombre_c']); 
    $des            = mysqli_real_escape_string($conexion, $_POST['desc']); 
    $equ            = mysqli_real_escape_string($conexion, $_POST['equip']);
    $ast            = mysqli_real_escape_string($conexion, $_POST['asistencia']); 
    
    // CAPTURA DE FECHA Y HORA DEL FORMULARIO
    $fecha_reg      = mysqli_real_escape_string($conexion, $_POST['fecha_reg']);
    $hora_reg       = mysqli_real_escape_string($conexion, $_POST['hora_reg']);
    
    // Unimos ambos valores para la base de datos
    $fecha_final    = $fecha_reg . " " . $hora_reg . ":00";

    $sql = "INSERT INTO carritos (nombre_responsable, telefono_responsable, nombre_carrito, descripcion, equipamiento, asistencia, created_at) 
            VALUES ('$nombre_persona', '$telefono', '$nom_carrito', '$des', '$equ', '$ast', '$fecha_final')";

    if(mysqli_query($conexion, $sql)){
        $mensaje = "<div class='alert success'>✅ Registro guardado con éxito a las $hora_reg</div>";
    } else {
        $mensaje = "<div class='alert error'> ❌ Error: " . mysqli_error($conexion) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es" data-theme="<?php echo $cfg['tema_color']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Carritos | <?php echo $cfg['nombre_sistema']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --bg: #f8fafc; --card: #ffffff; --text: #1e293b; --primary: #43b02a; --border: #e2e8f0; }
        [data-theme="dark"] { --bg: #0f172a; --card: #1e293b; --text: #f1f5f9; --primary: #2ecc71; --border: #334155; }
        
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); padding: 20px; }
        .box { background: var(--card); padding: 30px; border-radius: 20px; max-width: 650px; margin: auto; border: 1px solid var(--border); box-shadow: 0 10px 15px rgba(0,0,0,0.05); }
        
        h2 { text-align: center; margin-bottom: 25px; color: var(--text); }
        label { display: block; margin-bottom: 8px; font-weight: 700; font-size: 0.8rem; text-transform: uppercase; color: var(--primary); }
        
        input, textarea { width: 100%; padding: 12px; margin-bottom: 15px; border: 2px solid var(--border); border-radius: 10px; background: transparent; color: var(--text); font-size: 1rem; box-sizing: border-box; transition: 0.3s; }
        input:focus { border-color: var(--primary); outline: none; }

        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }

        .asistencia-container { display: flex; gap: 10px; margin-bottom: 20px; }
        .asistencia-btn { flex: 1; border: 2px solid var(--border); padding: 15px; border-radius: 12px; text-align: center; cursor: pointer; font-weight: 700; transition: 0.3s; }
        .asistencia-btn input { display: none; }
        .asistencia-btn:has(input:checked) { background: var(--primary); color: white; border-color: var(--primary); }

        .btn-save { background: var(--primary); color: white; border: none; padding: 18px; width: 100%; border-radius: 12px; cursor: pointer; font-weight: 700; font-size: 1.1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .btn-save:hover { opacity: 0.9; transform: translateY(-1px); }

        .alert { padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center; font-weight: 600; }
        .success { background: rgba(34, 197, 94, 0.1); color: #22c55e; border: 1px solid #22c55e; }
        .error { background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid #ef4444; }
        
        .footer-links { display: flex; justify-content: space-between; margin-top: 25px; }
        .footer-links a { text-decoration: none; color: var(--text); font-weight: 600; opacity: 0.6; font-size: 0.9rem; }
        .footer-links a:hover { opacity: 1; color: var(--primary); }
    </style>
</head>
<body>

<div class="box">
    <h2><i class="fas fa-clipboard-list"></i> Registro de Carritos</h2>
    
    <?php echo $mensaje; ?>

    <form method="POST">
        <div class="grid-2">
            <div>
                <label><i class="fas fa-calendar-alt"></i> Fecha:</label>
                <input type="date" name="fecha_reg" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div>
                <label><i class="fas fa-clock"></i> Hora:</label>
                <input type="time" name="hora_reg" id="hora_actual" value="<?php echo date('H:i'); ?>" required>
            </div>
        </div>

        <div class="grid-2">
            <div>
                <label><i class="fas fa-user"></i> Responsable:</label>
                <input type="text" name="nombre_persona" placeholder="Nombre completo" required>
            </div>
            <div>
                <label><i class="fas fa-phone"></i> Teléfono:</label>
                <input type="tel" name="telefono" placeholder="Ej: 987654321">
            </div>
        </div>

        <label><i class="fas fa-check-circle"></i> ¿Asistió hoy?</label>
        <div class="asistencia-container">
            <label class="asistencia-btn">
                <input type="radio" name="asistencia" value="SÍ VINO" checked> ✅ SÍ VINO
            </label>
            <label class="asistencia-btn">
                <input type="radio" name="asistencia" value="NO VINO"> ❌ NO VINO
            </label>
        </div>

        <label><i class="fas fa-store"></i> Identificación del Carrito:</label>
        <input type="text" name="nombre_c" placeholder="Ej: Carrito #05" required>

        <div class="grid-2">
            <div>
                <label>Estado Estético:</label>
                <textarea name="desc" rows="3" placeholder="Rayones, limpieza, etc."></textarea>
            </div>
            <div>
                <label>Equipamiento:</label>
                <textarea name="equip" rows="3" placeholder="Pan, Empanadas, Queque, Jugo..."></textarea>
            </div>
        </div>

        <button type="submit" name="save_car" class="btn-save">
            <i class="fas fa-save"></i> Guardar Registro
        </button>
    </form>

    <div class="footer-links">
        <a href="index.php"><i class="fas fa-arrow-left"></i> Volver al Inicio</a>
        <a href="lista_carritos.php">Ver Historial <i class="fas fa-history"></i></a>
    </div>
</div>

<script>
    // Esto pone la hora actual si el usuario no ha tocado el campo todavía
    function actualizarHora() {
        const ahora = new Date();
        const horas = String(ahora.getHours()).padStart(2, '0');
        const minutos = String(ahora.getMinutes()).padStart(2, '0');
        document.getElementById('hora_actual').value = `${horas}:${minutos}`;
    }

    // Si quieres que la hora se quede estática al cargar, borra la línea de abajo.
    // actualizarHora(); 
</script>

</body>
</html>