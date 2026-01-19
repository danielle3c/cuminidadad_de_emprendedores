<?php
include 'config.php';
$res = mysqli_query($conexion, "
SELECT e.*, u.username 
FROM encuesta_2026 e
LEFT JOIN Usuarios u ON e.created_by = u.idUsuarios
WHERE e.deleted_at = 0
ORDER BY e.id_encuesta DESC
");
?>
<!DOCTYPE html>
<html>
<body>
<h2>Encuestas 2026</h2>
<a href="crear_encuesta_2026.php">➕ Nueva Encuesta</a>
<table border="1" cellpadding="5">
<tr>
<th>ID</th><th>Fecha</th><th>Local</th><th>Representante</th><th>Usuario</th><th>Acciones</th>
</tr>
<?php while($row=mysqli_fetch_assoc($res)){ ?>
<tr>
<td><?= $row['id_encuesta'] ?></td>
<td><?= $row['fecha_encuesta'] ?></td>
<td><?= $row['nombre_local'] ?></td>
<td><?= $row['representante'] ?></td>
<td><?= $row['username'] ?></td>
<td>
<a href="editar_encuesta_2026.php?id=<?= $row['id_encuesta'] ?>">✏️ Editar</a>
</td>
</tr>
<?php } ?>
</table>
</body>
</html>
