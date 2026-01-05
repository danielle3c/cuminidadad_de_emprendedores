<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Control - Comunidad</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; margin: 0; padding: 20px; }
        .nav-bar { background: #1e293b; padding: 15px; text-align: center; border-radius: 8px; margin-bottom: 25px; }
        .nav-bar a { color: white; text-decoration: none; margin: 0 10px; font-weight: 500; }
        .container { max-width: 1000px; margin: auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .result-card { border: 1px solid #e2e8f0; padding: 20px; border-radius: 10px; margin-bottom: 20px; border-left: 6px solid #2563eb; position: relative; }
        .btn-action { display: inline-block; padding: 8px 12px; border-radius: 5px; text-decoration: none; font-weight: bold; font-size: 0.85em; margin-left: 5px; }
        .btn-del { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
        .btn-del:hover { background: #fecaca; }
        .btn-pay { background: #eff6ff; color: #2563eb; border: 1px solid #dbeafe; }
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
    <a href="talleres.php">🎓 Talleres</a>
</div>

<div class="container">
    <h2 style="text-align:center;">🔍 Gestión de la Comunidad</h2>
    
    <?php if(isset($_GET['msg'])) echo "<p style='color:green; text-align:center; font-weight:bold;'>✔ Operación realizada con éxito.</p>"; ?>

    <form method="GET" style="text-align:center; margin-bottom:30px;">
        <input type="text" name="buscar" placeholder="Nombre, RUT o ID..." style="width:60%; padding:12px; border-radius:8px; border:2px solid #ddd;" value="<?php echo $_GET['buscar'] ?? ''; ?>">
        <button type="submit" style="padding:12px 25px; background:#2563eb; color:white; border:none; border-radius:8px; cursor:pointer;">Buscar</button>
    </form>

    <?php
    if (isset($_GET['buscar']) && !empty($_GET['buscar'])) {
        $busqueda = mysqli_real_escape_string($conexion, $_GET['buscar']);
        
        // Buscamos solo los que NO estén eliminados (deleted_at = 0)
        $sql = "SELECT p.*, e.idemprendedores, e.rubro, c.idcreditos, c.saldo_inicial
                FROM personas p
                LEFT JOIN emprendedores e ON p.idpersonas = e.personas_idpersonas
                LEFT JOIN creditos c ON e.idemprendedores = c.emprendedores_idemprendedores
                WHERE (p.nombres LIKE '%$busqueda%' OR p.apellidos LIKE '%$busqueda%' OR p.rut LIKE '%$busqueda%')
                AND e.deleted_at = 0 OR (e.deleted_at IS NULL AND p.deleted_at = 0)";

        $res = mysqli_query($conexion, $sql);

        while ($f = mysqli_fetch_assoc($res)) {
            ?>
            <div class="result-card">
                <h3><?php echo $f['nombres'] . " " . $f['apellidos']; ?></h3>
                <p><strong>RUT:</strong> <?php echo $f['rut'] ?? 'S/R'; ?> | <strong>Rubro:</strong> <?php echo $f['rubro'] ?? 'No emprendedor'; ?></p>
                
                <div style="text-align:right;">
                    <?php if ($f['idcreditos']): ?>
                        <a href="cobranzas.php?id=<?php echo $f['idcreditos']; ?>" class="btn-action btn-pay">💰 Cobrar</a>
                    <?php endif; ?>

                    <?php if ($f['idemprendedores']): ?>
                        <a href="eliminar_emprendedor.php?id=<?php echo $f['idemprendedores']; ?>" 
                           class="btn-action btn-del" 
                           onclick="return confirm('¿Seguro que deseas eliminar a este emprendedor? Se ocultará pero no se borrarán sus deudas pasadas.')">
                           🗑️ Eliminar Negocio
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php
        }
    }
    ?>
</div>

</body>
</html>