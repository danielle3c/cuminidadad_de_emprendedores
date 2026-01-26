<?php 
include 'config.php'; 

// Lógica para guardar los datos al presionar "Guardar"
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Limpiamos los datos para evitar errores de MySQL
    $id = mysqli_real_escape_string($conexion, $_POST['id_escuela']);
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $negocio = mysqli_real_escape_string($conexion, $_POST['negocio']);
    $n1 = $_POST['n1']; 
    $n2 = $_POST['n2']; 
    $n3 = $_POST['n3']; 
    $n4 = $_POST['n4'];
    $opinion = mysqli_real_escape_string($conexion, $_POST['opinion']);
    $mejoras = mysqli_real_escape_string($conexion, $_POST['mejoras']);
    $interes = mysqli_real_escape_string($conexion, $_POST['interes']);
    $critica = mysqli_real_escape_string($conexion, $_POST['critica']);

    // Insertar o actualizar si el ID ya existe
    $insertar = "INSERT INTO escuela_verano (id_escuela, nombre_emprendedor, nombre_negocio, nota_general, nota_modulos, nota_funcionarios, nota_espacio, opinion_texto, mejoras_texto, capacitacion_interes, critica_adicional) 
                 VALUES ('$id', '$nombre', '$negocio', '$n1', '$n2', '$n3', '$n4', '$opinion', '$mejoras', '$interes', '$critica')
                 ON DUPLICATE KEY UPDATE 
                 nombre_emprendedor='$nombre', nombre_negocio='$negocio', nota_general='$n1', nota_modulos='$n2', opinion_texto='$opinion'";

    if (mysqli_query($conexion, $insertar)) {
        echo "<script>alert('Registro guardado correctamente'); window.location='index.php';</script>";
    } else {
        echo "Error al guardar: " . mysqli_error($conexion);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Digitalizar Escuela de Verano</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: sans-serif; background: #f4f7fe; color: #2b3674; padding: 20px; }
        .form-container { max-width: 600px; margin: auto; background: white; padding: 30px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .header-form { text-align: center; margin-bottom: 25px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; font-size: 0.9rem; }
        input, textarea, select { width: 100%; padding: 12px; border: 1px solid #e0e5f2; border-radius: 12px; margin-bottom: 15px; box-sizing: border-box; }
        .grid-notas { display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 10px; }
        .btn-guardar { background: #55b83e; color: white; border: none; padding: 15px; width: 100%; border-radius: 12px; font-weight: bold; cursor: pointer; font-size: 1rem; }
        .btn-volver { display: block; text-align: center; margin-top: 15px; color: #a3aed0; text-decoration: none; font-size: 0.9rem; }
    </style>
</head>
<body>

<div class="form-container">
    <div class="header-form">
        <i class="fas fa-edit" style="font-size: 2rem; color: #55b83e;"></i>
        <h1>Nueva Encuesta</h1>
        <p>Escuela de Verano 2026</p>
    </div>

    <form method="POST">
        <label>ID Participante (Excel)</label>
        <input type="number" name="id_escuela" required placeholder="Ej: 45">

        <label>Nombre del Emprendedor</label>
        <input type="text" name="nombre" required>

        <label>Nombre del Negocio</label>
        <input type="text" name="negocio">

        <label>Calificaciones (1 a 5)</label>
        <div class="grid-notas">
            <div><small>Gral</small><select name="n1"><?php for($i=5;$i>=1;$i--) echo "<option value='$i'>$i</option>"; ?></select></div>
            <div><small>Mod</small><select name="n2"><?php for($i=5;$i>=1;$i--) echo "<option value='$i'>$i</option>"; ?></select></div>
            <div><small>Func</small><select name="n3"><?php for($i=5;$i>=1;$i--) echo "<option value='$i'>$i</option>"; ?></select></div>
            <div><small>Esp</small><select name="n4"><?php for($i=5;$i>=1;$i--) echo "<option value='$i'>$i</option>"; ?></select></div>
        </div>

        <label>Opinión General</label>
        <textarea name="opinion" rows="2"></textarea>

        <label>¿Qué mejoraría?</label>
        <textarea name="mejoras" rows="2"></textarea>

        <label>Capacitación de Interés</label>
        <input type="text" name="interes">

        <label>Crítica Adicional</label>
        <textarea name="critica" rows="2"></textarea>

        <button type="submit" class="btn-guardar">GUARDAR REGISTRO</button>
        <a href="index.php" class="btn-volver">Volver al Buscador</a>
    </form>
</div>

</body>
</html>