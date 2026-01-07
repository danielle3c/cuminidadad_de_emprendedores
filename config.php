<?php
// config.php - CONEXIÓN Y SEGURIDAD CENTRALIZADA
$conexion = mysqli_connect("localhost", "root", "", "comunidad_de_emprendedores");

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

mysqli_set_charset($conexion, "utf8mb4");

// Iniciar la sesión para reconocer al usuario
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// VALIDACIÓN DE ACCESO
$pagina_actual = basename($_SERVER['PHP_SELF']);

// Si NO hay sesión y NO estás en el login, te manda al login
if (!isset($_SESSION['usuario_id']) && $pagina_actual != 'login.php') {
    header("Location: login.php");
    exit();
}
?>