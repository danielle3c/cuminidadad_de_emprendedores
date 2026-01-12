<?php
// 1. Configuración de la conexión
$host = "localhost";
$user = "root"; 
$pass = ""; 
// --- CAMBIA EL NOMBRE DE ABAJO POR EL QUE VES EN PHPMYADMIN ---
$db   = "nombre_real_de_tu_base_de_datos"; 

// Intentar conectar
$conexion = mysqli_connect($host, $user, $pass, $db);

// 2. Verificar la conexión inmediatamente
if (!$conexion) {
    die("Hubo un error de conexión: " . mysqli_connect_error());
}

// 3. Si llega aquí, es que la conexión es exitosa. Recibir datos:
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Limpiar los datos para evitar errores
    $fecha          = mysqli_real_escape_string($conexion, $_POST['fecha']);
    $hora           = mysqli_real_escape_string($conexion, $_POST['hora']);
    $nombre_taller  = mysqli_real_escape_string($conexion, $_POST['nombre_taller']);
    $relator        = mysqli_real_escape_string($conexion, $_POST['relator']);
    $id_emprendedor = mysqli_real_escape_string($conexion, $_POST['id_emprendedor']);
    $notas          = mysqli_real_escape_string($conexion, $_POST['notas']);

    // --- CAMBIA 'talleres' POR EL NOMBRE REAL DE TU TABLA ---
    $sql = "INSERT INTO talleres (fecha, hora, nombre_taller, relator, id_emprendedor, notas) 
            VALUES ('$fecha', '$hora', '$nombre_taller', '$relator', '$id_emprendedor', '$notas')";

    if (mysqli_query($conexion, $sql)) {
        echo "<h1>¡Taller guardado exitosamente!</h1>";
        echo "<a href='index.html'>Volver al formulario</a>";
    } else {
        echo "Error en el SQL: " . mysqli_error($conexion);
    }

} else {
    echo "No se recibieron datos.";
}

// 4. Cerrar conexión
mysqli_close($conexion);
?>