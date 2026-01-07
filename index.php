<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Control - Comunidad</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: #f0f2f5; margin: 0; padding: 20px; }
        .nav-bar { background: #43b02a; padding: 15px; text-align: center; border-radius: 8px; margin-bottom: 25px; }
        .nav-bar a { color: white; text-decoration: none; margin: 0 12px; font-weight: 500; font-size: 0.95em; }
        .nav-bar a:hover { text-decoration: underline; }
        
        .container { max-width: 1000px; margin: auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        
        .error-msg { background: #fee2e2; color: #b91c1c; padding: 15px; border-radius: 8px; border: 1px solid #fecaca; text-align: center; margin-top: 20px; font-weight: bold; }
        
        .result-card { border: 1px solid #e2e8f0; padding: 20px; border-radius: 10px; margin-bottom: 20px; border-left: 6px solid #43b02a; position: relative; }
        
        input[type="text"] { width: 65%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px; }
        .btn-search { padding: 12px 25px; background: #43b02a; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; }
        
        .actions { margin-top: 15px; display: flex; gap: 10px; justify-content: flex-end; border-top: 1px solid #eee; padding-top: 12px; }
        .btn { padding: 8px 14px; text-decoration: none; border-radius: 6px; font-size: 0.85em; font-weight: bold; border: 1px solid transparent; }
        .btn-edit { background: #fef3c7; color: #92400e; border-color: #fcd34d; }
        .btn-user { background: #dcfce7; color: #166534; border-color: #86efac; } /* Estilo para cuando es usuario */
        .btn-pay { background: #e0f2fe; color: #0369a1; border-color: #bae6fd; }
        .btn-del { background: #fee2e2; color: #991b1b; border-color: #fecaca; }
        
        .warning { color: #d97706; font-weight: bold; font-size: 0.85em; }
        .badge { background: #e2e8f0; padding: 3px 8px; border-radius: 4px; font-size: 0.8em; }
        .user-label { background: #43b02a; color: white; padding: 2px 6px; border-radius: 4px; font-size: 0.7em; vertical-align: middle; }
    </style>
</head>
<body>

<div class="nav-bar">
    <a href="index.php">🔍 Buscar</a>
    <a href="personas.php">👤 Personas</a>
    <a href="emprendedores.php">💼 Negocios</a>
    <a href="contratos.php">📝 Contratos</a>
    <a href="creditos.php">💰 Créditos</a>
    <a href="cobranzas.php">💵 Cobranzas</a>
    <a href="carritos.php">🛒 Carritos</a>
    <a href="jornadas.php">🏪 Jornadas</a>
    <a href="usuarios_lista.php">👥 Usuarios</a>
</div>

<div class="container">
    <h2 style="text-align:center; color: #333;">Buscador de la Comunidad</h2>
    
    <form method="GET" style="text-align:center; margin-bottom:30px;">
        <input type="text" name="buscar" placeholder="Ingrese Nombre, RUT o ID de Persona..." value="<?php echo $_GET['buscar'] ?? ''; ?>" required>
        <button type="submit" class="btn-search">Consultar</button>
    </form>

    <?php
    if (isset($_GET['buscar']) && !empty(trim($_GET['buscar']))) {
        $busqueda = mysqli_real_escape_string($conexion, $_GET['buscar']);
        
        // Unimos con Usuarios para saber si enviamos al editor completo
        $sql = "SELECT p.*, e.idemprendedores, e.rubro, cr.idcreditos, cr.saldo_inicial, u.idUsuarios 
                FROM personas p
                LEFT JOIN emprendedores e ON p.idpersonas = e.personas_idpersonas AND e.deleted_at = 0
                LEFT JOIN creditos cr ON e.idemprendedores = cr.emprendedores_idemprendedores AND cr.estado = 1
                LEFT JOIN Usuarios u ON p.idpersonas = u.personas_idpersonas
                WHERE (p.nombres LIKE '%$busqueda%' 
                OR p.apellidos LIKE '%$busqueda%' 
                OR p.rut LIKE '%$busqueda%' 
                OR p.idpersonas = '$busqueda')
                AND p.deleted_at = 0";

        $res = mysqli_query($conexion, $sql);

        if (mysqli_num_rows($res) > 0) {
            while ($f = mysqli_fetch_assoc($res)) {
                ?>
                <div class="result-card">
                    <div style="display: flex; justify-content: space-between;">
                        <div>
                            <span class="badge">ID: <?php echo $f['idpersonas']; ?></span>
                            <h3>
                                <?php echo $f['nombres'] . " " . $f['apellidos']; ?>
                                <?php if($f['idUsuarios']) echo '<span class="user-label">USUARIO SISTEMA</span>'; ?>
                            </h3>
                            
                            <p><strong>RUT:</strong> 
                                <?php echo !empty($f['rut']) ? $f['rut'] : '<span class="warning">⚠️ RUT NO REGISTRADO</span>'; ?>
                            </p>

                            <p><strong>Tipo:</strong> 
                                <?php echo ($f['idemprendedores']) ? "💼 Emprendedor (" . $f['rubro'] . ")" : "👤 Persona Natural"; ?>
                            </p>

                            <?php if($f['idcreditos']): ?>
                                <p style="color: #b91c1c; font-weight: bold;">
                                    📉 Saldo Pendiente: $<?php echo number_format($f['saldo_inicial'], 2); ?>
                                </p>
                            <?php endif; ?>
                        </div>

                        <div style="text-align: right; font-size: 0.85em; color: #666;">
                            <p>Ingreso: <?php echo date('d/m/Y', strtotime($f['created_at'])); ?></p>
                        </div>
                    </div>

                    <div class="actions">
                        <?php if($f['idUsuarios']): ?>
                            <a href="editar_usuario.php?id=<?php echo $f['idUsuarios']; ?>" class="btn btn-user">⚙️ Gestionar Cuenta</a>
                        <?php else: ?>
                            <a href="editar_persona.php?id=<?php echo $f['idpersonas']; ?>" class="btn btn-edit">📝 Editar Ficha</a>
                        <?php endif; ?>

                        <?php if($f['idcreditos']): ?>
                            <a href="cobranzas.php?id_credito=<?php echo $f['idcreditos']; ?>" class="btn btn-pay">💵 Registrar Pago</a>
                        <?php endif; ?>

                        <?php if($f['idemprendedores']): ?>
                            <a href="eliminar_emprendedor.php?id=<?php echo $f['idemprendedores']; ?>" 
                               class="btn btn-del" 
                               onclick="return confirm('¿Desea dar de baja este emprendimiento?')">🗑️ Quitar Negocio</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<div class='error-msg'>❌ ID o Nombre incorrecto. No se encontró ninguna persona activa.</div>";
        }
    }
    ?>
</div>

</body>
</html>