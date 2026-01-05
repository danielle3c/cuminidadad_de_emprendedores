<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head><title>Auditoría del Sistema</title>
<style>
    body { font-family: sans-serif; background: #f8fafc; padding: 20px; }
    table { width: 100%; border-collapse: collapse; background: white; }
    th, td { padding: 12px; border: 1px solid #e2e8f0; text-align: left; }
    th { background: #1e293b; color: white; }
    .accion { font-weight: bold; color: #43b02a; }
</style>
</head>
<body>
    <h2>🕵️ Historial de Movimientos (Auditoría)</h2>
    <table>
        <thead>
            <tr>
                <th>Fecha/Hora</th>
                <th>Tabla Afectada</th>
                <th>Acción</th>
                <th>Descripción</th>
                <th>IP</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM auditorias_sistemas ORDER BY created_at DESC LIMIT 50";
            $res = mysqli_query($conexion, $sql);
            while($row = mysqli_fetch_assoc($res)){
                echo "<tr>
                    <td>{$row['created_at']}</td>
                    <td>{$row['tabla_afectada']}</td>
                    <td class='accion'>{$row['accion']}</td>
                    <td>{$row['descripcion']}</td>
                    <td>{$row['ip_address']}</td>
                </tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>