<?php
// config.php
$conexion = mysqli_connect("localhost", "root", "", "comunidad_de_emprendedores");

if (session_status() === PHP_SESSION_NONE) { session_start(); }

$pagina_actual = basename($_SERVER['PHP_SELF']);

// Agregamos 'usuarios_agregar.php' a la lista de permitidos sin login
$paginas_publicas = ['login.php', 'usuarios_agregar.php'];

if (!isset($_SESSION['usuario_id']) && !in_array($pagina_actual, $paginas_publicas)) {
    header("Location: login.php");
    exit();
}
?>