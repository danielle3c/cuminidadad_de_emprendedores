<?php
// config.php

// 1. Datos de conexión
$host = "localhost"; // Quitamos el :8012 de aquí, MySQL no usa el puerto de la web
$user = "root";
$pass = ""; 
$db   = "comunidad_de_emprendedores";

// 2. Intentar la conexión
$conexion = mysqli_connect($host, $user, $pass, $db);

// 3. Verificar si funcionó
if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

// 4. Configurar idioma
mysqli_set_charset($conexion, "utf8mb4");
?>