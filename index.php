<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Control - Comunidad</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: #f0f2f5; margin: 0; padding: 20px; }
        /* Menú de navegación actualizado */
        .nav-bar { background: #1e293b; padding: 15px; text-align: center; border-radius: 8px; margin-bottom: 25px; }
        .nav-bar a { color: white; text-decoration: none; margin: 0 10px; font-weight: 500; }
        .nav-bar a:hover { text-decoration: underline; }
        
        .container { max-width: 1000px; margin: auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .search-form { text-align: center; margin-bottom: 30px; }
        input[type="text"] { width: 60%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px; }
        .btn-search { padding: 12px 25px; background: #2563eb; color: white; border: none; border-radius: 8px; cursor: pointer; }
        
        .result-card { border: 1px solid #e2e8f0; padding: 20px; border-radius: 10px; margin-bottom: 20px; border-left: 6px solid #2563eb; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .status-tag { background: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 20px; font-size: 0.8em; font-weight: bold; }
        .debt { color: #dc2626; font-weight: bold; }
        .btn-action { display: inline-block; margin-top: 10px; padding: 8px 12px; border-radius: 5px; text-decoration: none; font-weight: bold; font-size: 0.9em; }
    </style>
</head>
<body>

<div class="nav-bar">
    <a href="index.php">🔍 Buscar</a>
    <a href="personas.php">👤 Personas</a>
    <a href="emprendedores.php">💼 Negocios</a>
    <a href="contratos.php">📝 Contratos</a> <a href="creditos.php">💰 Créditos</a>
    <a href="cobranzas.php">💵 Cobranzas</a>
    <a href="talleres.php">🎓 Talleres</a>
</div>

<div class="container">
    <h2 style="text-align:center;">🔍 Gestión de la Comunidad</h2>
    
    <form method="GET" class="search-form">
        <input type="text" name="buscar" placeholder="Nombre, Apellido o ID..." value="<?php echo $_GET['buscar'] ?? ''; ?>">
        <button type="submit" class="btn-search">Buscar Ahora</button>
    </form>

    <?php
    if (isset($_GET['buscar']) && !empty($_GET['buscar'])) {
        $busqueda = mysqli_real_escape_string($conexion, $_GET['buscar']);
        
        // Consulta extendida para verificar si tiene contrato
        $sql = "SELECT p.*, e.idemprendedores, e.rubro, e.tipo_negocio, 
                       c.idcreditos, c.saldo_inicial,
                       con.idContratos
                FROM personas p
                LEFT JOIN emprendedores e ON p.idpersonas = e.personas_idpersonas
                LEFT JOIN Contratos con ON e.idemprendedores = con.emprendedores_idemprendedores
                LEFT JOIN creditos c ON e.idemprendedores = c.emprendedores_idemprendedores
                WHERE (p.nombres LIKE '%$busqueda%' OR p.apellidos LIKE '%$busqueda%' OR p.idpersonas = '$busqueda')
                AND p.deleted_at = 0";

        $res = mysqli_query($conexion, $sql);

        if (mysqli_num_rows($res) > 0) {
            while ($f = mysqli_fetch_assoc($res)) {
                ?>
                <div class="result-card">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <h3><?php echo $f['nombres'] . " " . $f['apellidos']; ?> <small style="color:gray;">(ID: <?php echo $f['idpersonas']; ?>)</small></h3>
                        <?php if($f['idemprendedores']) echo '<span class="status-tag">EMPRENDEDOR</span>'; ?>
                    </div>
                    
                    <div class="grid">
                        <div>
                            <p><strong>📞 Teléfono:</strong> <?php echo $f['telefono'] ?: 'N/A'; ?></p>
                            <p><strong>🏢 Negocio:</strong> <?php echo $f['tipo_negocio'] ?: 'No registrado'; ?></p>
                        </div>
                        <div>
                            <?php if ($f['idcreditos']): ?>
                                <p><strong>📉 Saldo Pendiente:</strong> <span class="debt">$<?php echo number_format($f['saldo_inicial'], 2); ?></span></p>
                            <?php elseif ($f['idContratos']): ?>
                                <p style="color:#059669;"><strong>✔️ Contrato Firmado:</strong> Esperando activación de crédito.</p>
                            <?php else: ?>
                                <p style="color:gray; font-style:italic;">Sin trámites financieros activos</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div style="text-align:right; margin-top:15px; border-top: 1px solid #eee; padding-top: 10px;">
                        <?php if ($f['idemprendedores'] && !$f['idContratos']): ?>
                            <a href="contratos.php?id_emp=<?php echo $f['idemprendedores']; ?>" class="btn-action" style="background:#ecfdf5; color:#059669;">+ Crear Contrato</a>
                        <?php endif; ?>

                        <?php if ($f['idcreditos']): ?>
                            <a href="cobranzas.php?id_credito=<?php echo $f['idcreditos']; ?>" class="btn-action" style="background:#eff6ff; color:#2563eb;">Registrar Pago →</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<p style='text-align:center; color:gray;'>No se encontraron resultados.</p>";
        }
    }
    ?>
</div>

</body>
</html>