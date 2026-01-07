<thead>
    <tr>
        <th>Fecha</th>
        <th>Responsable / Teléfono</th>
        <th>Carrito</th>
        <th>Asistencia</th>
        <th>Detalles</th>
        <th>Acciones</th> </tr>
</thead>
<tbody>
    <?php
    $query = "SELECT * FROM carritos ORDER BY created_at DESC";
    $res = mysqli_query($conexion, $query);

    if(mysqli_num_rows($res) > 0) {
        while($row = mysqli_fetch_assoc($res)) {
            $clase_ast = ($row['asistencia'] == 'SÍ VINO') ? 'si' : 'no';
            ?>
            <tr>
                <td>
                    <strong><?php echo date("d/m/Y", strtotime($row['created_at'])); ?></strong><br>
                    <small style="opacity: 0.6;"><?php echo date("H:i", strtotime($row['created_at'])); ?></small>
                </td>
                <td>
                    <strong><?php echo $row['nombre_responsable']; ?></strong><br>
                    <small><i class="fas fa-phone"></i> <?php echo $row['telefono_responsable']; ?></small>
                </td>
                <td><?php echo $row['nombre_carrito']; ?></td>
                <td><span class="badge <?php echo $clase_ast; ?>"><?php echo $row['asistencia']; ?></span></td>
                <td>
                    <small><strong>Estado:</strong> <?php echo $row['descripcion']; ?></small><br>
                    <small><strong>Equip:</strong> <?php echo $row['equipamiento']; ?></small>
                </td>
                <td>
                    <a href="editar_carrito.php?id=<?php echo $row['id']; ?>" 
                       style="color: #3b82f6; font-size: 1.2rem;" title="Editar">
                       <i class="fas fa-edit"></i>
                    </a>
                </td>
            </tr>
            <?php
        }
    } else {
        echo "<tr><td colspan='6' style='text-align:center; padding:30px;'>No hay registros guardados.</td></tr>";
    }
    ?>
</tbody>