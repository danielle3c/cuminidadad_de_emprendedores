<?php
// Usamos el puerto 8012 que aparece en tu phpMyAdmin
$conexion = mysqli_connect("localhost:8012", "root", "", "comunidad_de_emprendedores");

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}
// Esto es para que se guarden bien los acentos y la Ñ
mysqli_set_charset($conexion, "utf8mb4");
?>