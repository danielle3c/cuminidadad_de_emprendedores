<?php 
include 'config.php'; 

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conexion, $_GET['id']);
    
    // Marcamos como eliminado (deleted_at = 1) y desactivamos (estado = 0)
    $sql = "UPDATE emprendedores SET deleted_at = 1, estado = 0 WHERE idemprendedores = '$id'";
    
    if (mysqli_query($conexion, $sql)) {
        // Registrar la acción en la tabla de auditoría que tienes en tu SQL
        $descripcion = "Eliminación lógica del emprendedor ID: " . $id;
        mysqli_query($conexion, "INSERT INTO auditorias_sistemas (tabla_afectada, accion, descripcion, created_at) 
                                VALUES ('emprendedores', 'DELETE', '$descripcion', NOW())");
        
        header("Location: index.php?msg=emprendedor_eliminado");
    } else {
        echo "Error al procesar la solicitud: " . mysqli_error($conexion);
    }
}
?>