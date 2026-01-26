<?php
include 'config.php';

// Variable para manejar el ID recién guardado y mostrar el botón de ver reporte individual
$ultimo_id = isset($_GET['ultimo_id']) ? $_GET['ultimo_id'] : null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = mysqli_real_escape_string($conexion, $_POST['id_emprendedor']);
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $negocio = mysqli_real_escape_string($conexion, $_POST['negocio']);
    
    mysqli_begin_transaction($conexion);

    try {
        // Hoja 1: Datos básicos
        mysqli_query($conexion, "INSERT INTO emprendedores (id_emprendedor, nombre, emprendimiento, fecha_registro) 
            VALUES ('$id', '$nombre', '$negocio', NOW()) 
            ON DUPLICATE KEY UPDATE nombre='$nombre', emprendimiento='$negocio'");

        // Hoja 2: Evaluaciones
        mysqli_query($conexion, "INSERT INTO evaluaciones_notas (id_emprendedor, evaluacion_general, evaluacion_modulos, evaluacion_funcionarios, evaluacion_espacio) 
            VALUES ('$id', '{$_POST['n1']}', '{$_POST['n2']}', '{$_POST['n3']}', '{$_POST['n4']}')");

        // Hoja 3 y 4: Opiniones
        mysqli_query($conexion, "INSERT INTO opiniones (id_emprendedor, opinion_general, mejoras, capacitacion_deseada, critica_adicional) 
            VALUES ('$id', '{$_POST['opinion']}', '{$_POST['mejoras']}', '{$_POST['interes']}', '{$_POST['critica']}')");

        mysqli_commit($conexion);
        
        echo "<script>alert('Encuesta digitalizada correctamente'); window.location='digitalizar_escuela.php?ultimo_id=$id';</script>";
    } catch (Exception $e) {
        mysqli_rollback($conexion);
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Digitalización Escuela de Verano</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'DM Sans', sans-serif; background: #f4f7fe; padding: 20px; color: #2b3674; }
        .form-container { background: white; max-width: 800px; margin: auto; padding: 30px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .section-title { color: #55b83e; border-bottom: 2px solid #f1c40f; padding-bottom: 10px; margin-top: 30px; font-weight: bold; }
        .input-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; font-size: 0.9rem; }
        input[type="text"], textarea, select { width: 100%; padding: 10px; border: 1px solid #e0e5f2; border-radius: 10px; box-sizing: border-box; }
        
        /* Estilos de botones */
        .header-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 10px; }
        .nav-group { display: flex; gap: 10px; }
        .btn-nav { text-decoration: none; padding: 10px 18px; border-radius: 10px; font-weight: bold; font-size: 0.8rem; display: flex; align-items: center; gap: 8px; }
        
        .btn-back { background: #707eae; color: white; }
        .btn-history { background: #422afb; color: white; } /* BOTÓN NUEVO */
        .btn-view { background: #f1c40f; color: #000; }
        .btn-save { background: #55b83e; color: white; border: none; padding: 15px 30px; border-radius: 12px; cursor: pointer; width: 100%; font-weight: bold; margin-top: 20px; font-size: 1rem; }
        .btn-save:hover { background: #45a032; }

        /* Banner de éxito */
        .success-banner { background: #e6fffa; border: 1px solid #b2f5ea; padding: 15px; border-radius: 12px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
    </style>
</head>
<body>

<div class="form-container">
    <div class="header-actions">
        <div class="nav-group">
            <a href="index.php" class="btn-nav btn-back"><i class="fas fa-arrow-left"></i> INICIO</a>
            <a href="consultar_historial.php" class="btn-nav btn-history"><i class="fas fa-list-ul"></i> VER HISTORIAL GENERAL</a>
        </div>
        
        <?php if($ultimo_id): ?>
            <a href="ver_historial_escuela.php?id=<?php echo $ultimo_id; ?>" class="btn-nav btn-view"><i class="fas fa-eye"></i> VER REPORTE RECIENTE</a>
        <?php endif; ?>
    </div>

    <?php if($ultimo_id): ?>
        <div class="success-banner">
            <span><i class="fas fa-check-circle" style="color: #38a169;"></i> ¡Encuesta guardada correctamente!</span>
            <a href="ver_historial_escuela.php?id=<?php echo $ultimo_id; ?>" style="color: #2c7a7b; font-weight: bold; text-decoration: none;">Ver Ficha Individual <i class="fas fa-external-link-alt"></i></a>
        </div>
    <?php endif; ?>

    <h1 style="margin-top: 0;"><i class="fas fa-edit" style="color: #55b83e;"></i> Digitalizar Encuesta</h1>
    
    <form method="POST">
        <div class="section-title">1. Identificación (Hoja 1)</div>
        <div class="grid" style="display: grid; grid-template-columns: 1fr 3fr; gap: 10px; margin-top: 15px;">
            <div class="input-group">
                <label>ID Excel</label>
                <input type="text" name="id_emprendedor" required placeholder="Ej: 45">
            </div>
            <div class="input-group">
                <label>Nombre Completo</label>
                <input type="text" name="nombre" required placeholder="Nombre del emprendedor">
            </div>
        </div>
        <div class="input-group">
            <label>Emprendimiento / Rubro</label>
            <input type="text" name="negocio" placeholder="Ej: Artesanía en madera">
        </div>

        <div class="section-title">2. Evaluaciones Cuantitativas (Hoja 2)</div>
        <p><small>Por favor, seleccione la nota del 1 al 5 otorgada por el emprendedor.</small></p>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div class="input-group">
                <label>Evaluación General</label>
                <select name="n1">
                    <?php for($i=5;$i>=1;$i--) echo "<option value='$i'>$i - " . ($i==5 ? 'Excelente' : ($i==1 ? 'Muy Malo' : '')) . "</option>"; ?>
                </select>
            </div>
            <div class="input-group">
                <label>Evaluación Módulos</label>
                <select name="n2">
                    <?php for($i=5;$i>=1;$i--) echo "<option value='$i'>$i</option>"; ?>
                </select>
            </div>
            <div class="input-group">
                <label>Evaluación Funcionarios</label>
                <select name="n3">
                    <?php for($i=5;$i>=1;$i--) echo "<option value='$i'>$i</option>"; ?>
                </select>
            </div>
            <div class="input-group">
                <label>Evaluación Espacio</label>
                <select name="n4">
                    <?php for($i=5;$i>=1;$i--) echo "<option value='$i'>$i</option>"; ?>
                </select>
            </div>
        </div>

        <div class="section-title">3. Comentarios y Sugerencias (Hoja 3 y 4)</div>
        <div class="input-group">
            <label>¿Cuál es su opinión de la actividad?</label>
            <textarea name="opinion" rows="3" placeholder="Escriba aquí la opinión general..."></textarea>
        </div>
        <div class="input-group">
            <label>¿Qué mejoraría o corregiría?</label>
            <textarea name="mejoras" rows="3" placeholder="Sugerencias de mejora..."></textarea>
        </div>
        <div class="input-group">
            <label>¿Qué capacitación le gustaría recibir?</label>
            <input type="text" name="interes" placeholder="Ej: Marketing Digital, Contabilidad...">
        </div>
        <div class="input-group">
            <label>Crítica o comentario adicional</label>
            <textarea name="critica" rows="2"></textarea>
        </div>

        <button type="submit" class="btn-save"><i class="fas fa-save"></i> GUARDAR EVALUACIÓN</button>
    </form>
</div>

</body>
</html>