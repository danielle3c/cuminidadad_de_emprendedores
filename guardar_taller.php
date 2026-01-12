<?php
// 1. Configuración de la conexión
$host = "localhost";
$user = "root"; // Usuario por defecto de XAMPP
$pass = "";     // Contraseña por defecto (vacía)
$db   = "nombre_de_tu_base_de_datos"; // CAMBIA ESTO por el nombre real de tu BD

$db = "comunidad_db";
// Verificar la conexión
if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

// 2. Recibir los datos del formulario
// Usamos mysqli_real_escape_string para evitar errores con comillas o caracteres especiales
$fecha          = mysqli_real_escape_string($conexion, $_POST['fecha']);
$hora           = mysqli_real_escape_string($conexion, $_POST['hora']);
$nombre_taller  = mysqli_real_escape_string($conexion, $_POST['nombre_taller']);
$relator        = mysqli_real_escape_string($conexion, $_POST['relator']);
$id_emprendedor = mysqli_real_escape_string($conexion, $_POST['id_emprendedor']);
$notas          = mysqli_real_escape_string($conexion, $_POST['notas']);

// 3. Crear la consulta SQL (Asegúrate de que los nombres de las columnas coincidan con tu tabla)
$sql = "INSERT INTO talleres (fecha, hora, nombre_taller, relator, id_emprendedor, notas) 
        VALUES ('$fecha', '$hora', '$nombre_taller', '$relator', '$id_emprendedor', '$notas')";

// 4. Ejecutar y verificar
if (mysqli_query($conexion, $sql)) {
    echo "¡Taller guardado exitosamente en la base de datos!";
    echo "<br><a href='index.html'>Volver</a>"; // Cambia index.html por tu página principal
} else {
    echo "Error al guardar: " . mysqli_error($conexion);
}

// 5. Cerrar conexión
mysqli_close($conexion);
?>