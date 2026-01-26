<?php 
include 'config.php'; 

// Lógica para guardar los datos
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = mysqli_real_escape_string($conexion, $_POST['id_escuela']);
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $negocio = mysqli_real_escape_string($conexion, $_POST['negocio']);
    $n1 = $_POST['n1']; $n2 = $_POST['n2']; $n3 = $_POST['n3']; $n4 = $_POST['n4'];
    
    // Nuevos campos de texto del Excel
    $opinion = mysqli_real_escape_string($conexion, $_POST['opinion']);
    $mejoras = mysqli_real_escape_string($conexion, $_POST['mejoras']);
    $interes = mysqli_real_escape_string($conexion, $_POST['interes']);
    $critica = mysqli_real_escape_string($conexion, $_POST['critica']);

    $insertar = "INSERT INTO escuela_verano (id_escuela, nombre_emprendedor, nombre_negocio, nota_general, nota_modulos, nota_funcionarios, nota_espacio, opinion_texto, mejoras_texto, capacitacion_interes, critica_adicional) 
                 VALUES ('$id', '$nombre', '$negocio', '$n1', '$n2', '$n3', '$n4', '$opinion', '$mejoras', '$interes', '$critica')
                 ON DUPLICATE KEY UPDATE 
                 nombre_emprendedor='$nombre', nombre_negocio='$negocio', nota_general='$n1', nota_modulos='$n2', 
                 nota_funcionarios='$n3', nota_espacio='$n4', opinion_texto='$opinion', mejoras_texto='$mejoras', 
                 capacitacion_interes='$interes', critica_adicional='$critica'";

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary: #55b83e; --text: #2b3674; --bg: #f4f7fe; }
        body { background: var(--bg); font-family: 'DM Sans', sans-serif; color: var(--text); }
        .form-container { max-width: 700px; margin: 40px auto; background: white; padding: 30px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        
        .nav-buttons { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .btn-nav { text-decoration: none; color: #707eae; font-weight: bold; font-size: 0.9rem; display: flex; align-items: center; gap: 8px; }
        
        .header-form { text-align: center; margin-bottom: 30px; }
        .header-form h1 { margin: 0; color: var(--text); }
        
        .grid-notas { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-bottom: 20px; background: #f8fafd; padding: 15px; border-radius: 12px; }
        
        label { display: block; font-weight: bold; margin-bottom: 8px; font-size: 0.85rem; margin-top: 15px; }
        input, textarea, select { width: 100%; padding: 12px; border: 1px solid #e0e5f2; border-radius: 12px; box-sizing: border-box; }
        textarea { resize: none; }
        
        .btn-guardar { background: var(--primary); color: white; border: none; padding: 18px; width: 100%; border-radius: 12px; font-weight: bold; cursor: pointer; font-size: 1rem; margin-top: 25px; transition: 0.3s; }
        .btn-guardar:hover { background: #449d2f; transform: translateY(-2px); }
    </style>
</head>
<body>

<div class="form-container">
    <div class="nav-buttons">
        <a href="index.php" class="btn-nav"><i class="fas fa-arrow-left"></i> Volver al Buscador</a>
        <a href="ver_historial_escuela.php" class="btn-nav" style="color: var(--primary);"><i class="fas fa-list"></i> Ver Historial Escuela</a>
    </div>

    <div class="header-form">
        <i class="fas fa-sun" style="font-size: 2rem; color: #f1c40f;"></i>
        <h1>Digitalizar Encuesta</h1>
        <p>Escuela de Verano 2026</p>
    </div>

    <form method="POST">
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 15px;">
            <div>
                <label>ID Participante</label>
                <input type="number" name="id_escuela" placeholder="Ej: 1" required>
            </div>
            <div>
                <label>Nombre del Emprendedor</label>
                <input type="text" name="nombre" placeholder="Nombre completo" required>
            </div>
        </div>

        <label>Nombre del Negocio / Emprendimiento</label>
        <input type="text" name="negocio" placeholder="Nombre de la marca o rubro">

        <label style="text-align: center; margin-top: 25px;">Evaluación de Satisfacción (1 a 5)</label>
        <div class="grid-notas">
            <div>
                <label>General</label>
                <select name="n1"><?php for($i=5;$i>=1;$i--) echo "<option value='$i'>$i</option>"; ?></select>
            </div>
            <div>
                <label>Módulos</label>
                <select name="n2"><?php for($i=5;$i>=1;$i--) echo "<option value='$i'>$i</option>"; ?></select>
            </div>
            <div>
                <label>Equipo</label>
                <select name="n3"><?php for($i=5;$i>=1;$i--) echo "<option value='$i'>$i</option>"; ?></select>
            </div>
            <div>
                <label>Espacio</label>
                <select name="n4"><?php for($i=5;$i>=1;$i--) echo "<option value='$i'>$i</option>"; ?></select>
            </div>
        </div>

        <label>1. ¿Cuál es su opinión de esta actividad en términos generales?</label>
        <textarea name="opinion" rows="2"></textarea>

        <label>2. ¿Qué mejoraría, modificaría o corregiría para una futura versión?</label>
        <textarea name="mejoras" rows="2"></textarea>

        <label>3. ¿Qué módulo o capacitación le gustaría recibir o agregar?</label>
        <input type="text" name="interes" placeholder="Ej: Contabilidad, Redes Sociales...">

        <label>4. ¿Qué crítica o comentario adicional le gustaría agregar?</label>
        <textarea name="critica" rows="2"></textarea>

        <button type="submit" class="btn-guardar">
            <i class="fas fa-save"></i> GUARDAR EN BASE DE DATOS
        </button>
    </form>
</div>

</body>
</html>