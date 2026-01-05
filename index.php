<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Buscador de Comunidad</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; padding: 20px; }
        .buscador-box { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); max-width: 900px; margin: auto; }
        input[type="text"] { width: 70%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px; }
        button { padding: 12px 25px; background: #2563eb; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; }
        .resultado-card { background: #fff; border-left: 5px solid #2563eb; margin-top: 20px; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .grid-info { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .tag { background: #e0e7ff; color: #3730a3; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .no-results { text-align: center; color: #64748b; margin-top: 20px; }
    </style>
</head>
<body>

<div class="buscador-box">
    <h2 style="text-align:center;">🔍 Buscador de la Comunidad</h2>
    <form method="GET" style="text-align:center;">
        <input type="text" name="buscar" placeholder="Escribe nombre o apellido..." value="<?php echo $_GET['buscar'] ?? ''; ?>">
        <button type="submit">Buscar</button>
    </form>

    <?php
    if (isset($_GET['buscar']) && !empty($_GET['buscar'])) {
        $busqueda = mysqli_real_escape_string($conexion, $_GET['buscar']);
        
        // Esta consulta une PERSONAS con EMPRENDEDORES y CREDITOS
        $sql = "SELECT p.*, e.rubro, e.tipo_negocio, e.limite_credito, c.monto_inicial, c.saldo_inicial
                FROM personas p
                LEFT JOIN emprendedores e ON p.idpersonas = e.personas_idpersonas
                LEFT JOIN creditos c ON e.idemprendedores = c.emprendedores_idemprendedores
                WHERE p.nombres LIKE '%$busqueda%' OR p.apellidos LIKE '%$busqueda%'
                AND p.deleted_at = 0";

        $res = mysqli_query($conexion, $sql);

        if (mysqli_num_rows($res) > 0) {
            while ($f = mysqli_fetch_assoc($res)) {
                ?>
                <div class="resultado-card">
                    <h3><?php echo $f['nombres'] . " " . $f['apellidos']; ?> 
                        <?php if($f['rubro']) echo '<span class="tag">EMPRENDEDOR</span>'; ?>
                    </h3>
                    
                    <div class="grid-info">
                        <div>
                            <p><strong>📞 Teléfono:</strong> <?php echo $f['telefono'] ?? 'No registrado'; ?></p>
                            <p><strong>📧 Email:</strong> <?php echo $f['email'] ?? 'No registrado'; ?></p>
                            <p><strong>🎂 Fec. Nac:</strong> <?php echo $f['fecha_nacimiento']; ?></p>
                        </div>
                        <div>
                            <?php if ($f['rubro']): ?>
                                <p><strong>🏢 Negocio:</strong> <?php echo $f['tipo_negocio'] . " (" . $f['rubro'] . ")"; ?></p>
                                <p><strong>💰 Crédito:</strong> $<?php echo number_format($f['monto_inicial'], 2); ?></p>
                                <p><strong>📉 Saldo Actual:</strong> $<?php echo number_format($f['saldo_inicial'], 2); ?></p>
                            <?php else: ?>
                                <p style="color:gray;"><em>Esta persona aún no tiene negocio registrado.</em></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div style="margin-top: 15px; text-align: right;">
                        <a href="editar_persona.php?id=<?php echo $f['idpersonas']; ?>" style="color: #2563eb; text-decoration: none;">Ver Ficha Completa →</a>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<p class='no-results'>No se encontró a nadie con ese nombre.</p>";
        }
    }
    ?>
    
    <div style="margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px; text-align: center;">
        <a href="personas.php" style="margin-right: 15px;">+ Nueva Persona</a>
        <a href="emprendedores.php">+ Nuevo Emprendedor</a>
    </div>
</div>

</body>
</html>