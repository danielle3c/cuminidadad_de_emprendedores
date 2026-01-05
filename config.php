<?php
// config.php
// NO pongas "include" de este mismo archivo aquí.

$host = "localhost:8012"; // Tu puerto específico
$user = "root";
$pass = ""; 
$db   = "comunidad_de_emprendedores";

$conexion = mysqli_connect($host, $user, $pass, $db);

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

mysqli_set_charset($conexion, "utf8mb4");
?>