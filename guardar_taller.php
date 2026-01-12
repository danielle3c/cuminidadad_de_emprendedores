<?php
// 1. Datos de conexión a MySQL
$host = "localhost";
$user = "root";    // Usuario por defecto de XAMPP
$pass = "";        // Contraseña por defecto de XAMPP
$db   = "bd_comunidad"; // El nombre que creamos en phpMyAdmin

// Crear la conexión
$conexion = mysqli_connect($host, $user, $pass, $db);

// 2. Verificar si la conexión falló
if (!$conexion) {
    die("Error de conexión a MySQL: " . mysqli_connect_error());
}

// 3. Procesar los datos cuando se envía el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Limpiar los datos para que MySQL los acepte correctamente
    $fecha          = mysqli_real_escape_string($conexion, $_POST['fecha']);
    $hora           = mysqli_real_escape_string($conexion, $_POST['hora']);
    $nombre_taller  = mysqli_real_escape_string($conexion, $_POST['nombre_taller']);
    $relator        = mysqli_real_escape_string($conexion, $_POST['relator']);
    $id_emprendedor = mysqli_real_escape_string($conexion, $_POST['id_emprendedor']);
    $notas          = mysqli_real_escape_string($conexion, $_POST['notas']);

    // 4. La orden SQL para INSERTAR los datos en la tabla
    $sql = "INSERT INTO talleres (fecha, hora, nombre_taller, relator, id_emprendedor, notas) 
            VALUES ('$fecha', '$hora', '$nombre_taller', '$relator', '$id_emprendedor', '$notas')";

    // 5. Ejecutar la orden y avisar al usuario
    if (mysqli_query($conexion, $sql)) {
        echo "<h1>¡Datos guardados en MySQL exitosamente!</h1>";
        echo "<p>El taller '$nombre_taller' ha sido registrado.</p>";
        echo "<a href='index.html'>Volver al inicio</a>";
    } else {
        echo "Error al guardar los datos: " . mysqli_error($conexion);
    }
}

// 6. Cerrar la conexión por seguridad
mysqli_close($conexion);
?>