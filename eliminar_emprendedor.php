<?php 
include 'config.php'; 

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conexion, $_GET['id']);
    
    // Usamos NOW() para registrar el momento exacto de la eliminación lógica
    $sql = "UPDATE emprendedores SET deleted_at = 1, estado = 0 WHERE idemprendedores = '$id'";
    
    if (mysqli_query($conexion, $sql)) {
        // Opcional: Registrar en la tabla de auditoría quien lo eliminó
        $desc = "Se eliminó lógicamente al emprendedor con ID: " . $id;
        mysqli_query($conexion, "INSERT INTO auditorias_sistemas (tabla_afectada, accion, descripcion, created_at) 
                            VALUES ('emprendedores', 'DELETE', '$desc', NOW())");
        
        header("Location: index.php?msg=Eliminado correctamente");
    } else {
        echo "Error al eliminar: " . mysqli_error($conexion);
    }
}
?>