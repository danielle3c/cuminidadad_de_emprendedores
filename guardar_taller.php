<?php
// 1. Datos de conexión
$host = "localhost";
$user = "root";    
$pass = "";        
$db   = "bd_comunidad"; 

$conexion = mysqli_connect($host, $user, $pass, $db);

if (!$conexion) {
    die("Error de conexión a MySQL: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $fecha          = mysqli_real_escape_string($conexion, $_POST['fecha']);
    $hora           = mysqli_real_escape_string($conexion, $_POST['hora']);
    $nombre_taller  = mysqli_real_escape_string($conexion, $_POST['nombre_taller']);
    $relator        = mysqli_real_escape_string($conexion, $_POST['relator']);
    $id_emprendedor = mysqli_real_escape_string($conexion, $_POST['id_emprendedor']);
    $notas          = mysqli_real_escape_string($conexion, $_POST['notas']);

    $sql = "INSERT INTO talleres (fecha, hora, nombre_taller, relator, id_emprendedor, notas) 
            VALUES ('$fecha', '$hora', '$nombre_taller', '$relator', '$id_emprendedor', '$notas')";

    if (mysqli_query($conexion, $sql)) {
        // Redirección automática si se guardó con éxito
        header("Location: https://nonpolarizable-neil-wondrously.ngrok-free.dev/comunidad/talleres.php");
        exit(); // Detiene el script para asegurar la redirección
    } else {
        echo "Error al guardar los datos: " . mysqli_error($conexion);
    }
}

mysqli_close($conexion);
?>