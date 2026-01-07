<?php include 'config.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Gestión de Usuarios</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f4f7f6; }
        .user-table { width: 100%; border-collapse: collapse; background: white; }
        .user-table th, .user-table td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        .user-table th { background: #43b02a; color: white; }
        .btn-edit { background: #f97316; color: white; padding: 5px 10px; text-decoration: none; border-radius: 4px; }
    </style>
</head>
<body>
    <h2>👥 Usuarios del Sistema</h2>
    <table class="user-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Persona Asociada</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT u.idUsuarios, u.username, u.estado, p.nombres, p.apellidos 
                    FROM Usuarios u 
                    LEFT JOIN personas p ON u.personas_idpersonas = p.idpersonas 
                    WHERE u.deleted_at = 0";
            $res = mysqli_query($conexion, $sql);
            while($u = mysqli_fetch_assoc($res)){
                $est = ($u['estado'] == 1) ? "Activo" : "Bloqueado";
                echo "<tr>
                    <td>{$u['idUsuarios']}</td>
                    <td>{$u['username']}</td>
                    <td>{$u['nombres']} {$u['apellidos']}</td>
                    <td>$est</td>
                    <td><a href='editar_usuario.php?id={$u['idUsuarios']}' class='btn-edit'>Modificar</a></td>
                </tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>