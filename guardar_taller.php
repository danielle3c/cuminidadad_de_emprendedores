<?php
// Esto te permite ver si los datos están llegando correctamente
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo "<h1>¡Datos recibidos!</h1>";
    echo "<pre>";
    print_r($_POST); // Esto imprimirá en pantalla todo lo que enviaste
    echo "</pre>";
} else {
    echo "No se han enviado datos por el método POST.";
}
?>