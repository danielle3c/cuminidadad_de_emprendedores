<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conexion, $_GET['id']);

    // 1. Ejecutar la eliminación
    $sql = "DELETE FROM carritos WHERE id = '$id'";

    if (mysqli_query($conexion, $sql)) {
        // 2. CORRECCIÓN DE RUTA: 
        // Asegúrate de que el archivo de destino sea el correcto. 
        // Si tu lista está en carritos.php, cámbialo aquí:
        header("Location: carritos.php?msg=eliminado"); 
        exit(); // Siempre usa exit después de un header
    } else {
        echo "Error al intentar eliminar el registro: " . mysqli_error($conexion);
    }
} else {
    // Si no hay ID, volvemos a la lista general
    header("Location: carritos.php");
    exit();
}
?>