<?php 
include 'config.php'; 

// Lógica para guardar los datos al presionar "Guardar"
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = mysqli_real_escape_string($conexion, $_POST['id_escuela']);
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $negocio = mysqli_real_escape_string($conexion, $_POST['negocio']);
    $n1 = $_POST['n1']; $n2 = $_POST['n2']; $n3 = $_POST['n3']; $n4 = $_POST['n4'];
    $opinion = mysqli_real_escape_string($conexion, $_POST['opinion']);
    $interes = mysqli_real_escape_string($conexion, $_POST['interes']);

    $insertar = "INSERT INTO escuela_verano (id_escuela, nombre_emprendedor, nombre_negocio, nota_general, nota_modulos, nota_funcionarios, nota_espacio, opinion_texto, capacitacion_interes) 
                 VALUES ('$id', '$nombre', '$negocio', '$n1', '$n2', '$n3', '$n4', '$opinion', '$interes')
                 ON DUPLICATE KEY UPDATE nombre_emprendedor='$nombre', nota_general='$n1', opinion_texto='$opinion'";

    if (mysqli_query($conexion, $insertar)) {
        echo "<script>alert('Registro digitalizado con éxito'); window.location='index.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Digitalizar Escuela de Verano</title>
    <style>
        .form-container { max-width: 600px; margin: 40px auto; background: white; padding: 30px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); font-family: sans-serif; }
        .grid-notas { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; color: #2b3674; font-size: 0.9rem; }
        input, textarea, select { width: 100%; padding: 12px; border: 1px solid #e0e5f2; border-radius: 12px; box-sizing: border-box; margin-bottom: 15px; }
        .btn-guardar { background: #55b83e; color: white; border: none; padding: 15px; width: 100%; border-radius: 12px; font-weight: bold; cursor: pointer; font-size: 1rem; }
        .header-form { text-align: center; margin-bottom: 30px; }
        .header-form i { color: #55b83e; font-size: 2rem; }
    </style>
</head>
<body style="background: #f4f7fe;">

<div class="form-container">
    <div class="header-form">
        <h1>Digitalizar Encuesta</h1>
        <p>Escuela de Verano 2026</p>
    </div>

    <form method="POST">
        <label>ID del Participante (Según Excel)</label>
        <input type="number" name="id_escuela" placeholder="Ej: 1" required>

        <label>Nombre del Emprendedor</label>
        <input type="text" name="nombre" placeholder="Nombre completo" required>

        <label>Nombre del Negocio / Emprendimiento</label>
        <input type="text" name="negocio" placeholder="Ej: Repostería Doña Inés">

        <div class="grid-notas">
            <div>
                <label>Nota General</label>
                <select name="n1"><?php for($i=1;$i<=5;$i++) echo "<option value='$i'>$i</option>"; ?></select>
            </div>
            <div>
                <label>Nota Módulos</label>
                <select name="n2"><?php for($i=1;$i<=5;$i++) echo "<option value='$i'>$i</option>"; ?></select>
            </div>
            <div>
                <label>Nota Funcionarios</label>
                <select name="n3"><?php for($i=1;$i<=5;$i++) echo "<option value='$i'>$i</option>"; ?></select>
            </div>
            <div>
                <label>Nota Espacio</label>
                <select name="n4"><?php for($i=1;$i<=5;$i++) echo "<option value='$i'>$i</option>"; ?></select>
            </div>
        </div>

        <label>Opinión General de la Actividad</label>
        <textarea name="opinion" rows="3" placeholder="¿Qué le pareció la escuela?"></textarea>

        <label>¿Qué otra capacitación le gustaría?</label>
        <input type="text" name="interes" placeholder="Ej: Marketing Digital, Costos...">

        <button type="submit" class="btn-guardar">GUARDAR RESPUESTA</button>
    </form>
</div>

</body>
</html>