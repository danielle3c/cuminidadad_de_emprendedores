<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conexion, $_GET['id']);

    // Ejecutar la eliminación en la base de datos
    $sql = "DELETE FROM carritos WHERE id = '$id'";

    if (mysqli_query($conexion, $sql)) {
        // Redirigir de vuelta con éxito
        header("Location: lista_carritos.php?msg=eliminado");
    } else {
        echo "Error al intentar eliminar el registro: " . mysqli_error($conexion);
    }
} else {
    header("Location: lista_carritos.php");
}
?>