<?php
// config.php - ESTE ARCHIVO NO DEBE TENER INCLUDES

$host = "localhost:8012"; 
$user = "root";
$pass = "123456789"; 
$db   = "comunidad_de_emprendedores";

$conexion = mysqli_connect($host, $user, $pass, $db);

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

mysqli_set_charset($conexion, "utf8mb4");
?>