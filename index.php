<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Control - Comunidad</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: #f0f2f5; margin: 0; padding: 20px; }
        .nav-bar { background: #43b02a; padding: 15px; text-align: center; border-radius: 8px; margin-bottom: 25px; }
        .nav-bar a { color: white; text-decoration: none; margin: 0 15px; font-weight: 500; }
        .container { max-width: 1000px; margin: auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        
        /* Estilo para el mensaje de error */
        .error-msg { 
            background: #fee2e2; 
            color: #43b02a; 
            padding: 15px; 
            border-radius: 8px; 
            border: 1px solid #fecaca; 
            text-align: center; 
            margin-top: 20px;
            font-weight: bold;
        }

        .result-card { border: 1px solid #e2e8f0; padding: 20px; border-radius: 10px; margin-bottom: 20px; border-left: 6px solid #2563eb; }
        input[type="text"] { width: 60%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px; }
        .btn-search { padding: 12px 25px; background: #43b02a; color: white; border: none; border-radius: 8px; cursor: pointer; }
    </style>
</head>
<body>

<div class="nav-bar">
    <a href="index.php">Buscar</a>
    <a href="personas.php">Personas</a>
    <a href="emprendedores.php">Negocios</a>
    <a href="contratos.php">Contratos</a>
    <a href="creditos.php">Créditos</a>
    <a href="cobranzas.php">Cobranzas</a>
    <a href="carritos.php">Carritos</a> <a href="jornadas.php">Jornadas</a> </div>
<div class="container">
    <h2 style="text-align:center;">Buscador de la Comunidad</h2>
    
    <form method="GET" style="text-align:center; margin-bottom:30px;">
        <input type="text" name="buscar" placeholder="Ingrese Nombre, RUT o ID..." value="<?php echo $_GET['buscar'] ?? ''; ?>" required>
        <button type="submit" class="btn-search">Consultar</button>
    </form>

    <?php
    if (isset($_GET['buscar']) && !empty(trim($_GET['buscar']))) {
        $busqueda = mysqli_real_escape_string($conexion, $_GET['buscar']);
        
        // Consulta unificando personas y sus estados
        $sql = "SELECT p.*, e.idemprendedores, c.saldo_inicial 
                FROM personas p
                LEFT JOIN emprendedores e ON p.idpersonas = e.personas_idpersonas
                LEFT JOIN creditos c ON e.idemprendedores = c.emprendedores_idemprendedores
                WHERE (p.nombres LIKE '%$busqueda%' 
                OR p.apellidos LIKE '%$busqueda%' 
                OR p.rut LIKE '%$busqueda%' 
                OR p.idpersonas = '$busqueda')
                AND p.deleted_at = 0";

        $res = mysqli_query($conexion, $sql);

        // VALIDACIÓN: Si hay 0 filas, el ID o Nombre es incorrecto
        if (mysqli_num_rows($res) > 0) {
            while ($f = mysqli_fetch_assoc($res)) {
                ?>
                <div class="result-card">
                    <h3><?php echo $f['nombres'] . " " . $f['apellidos']; ?> (ID: <?php echo $f['idpersonas']; ?>)</h3>
                    <p><strong>RUT:</strong> <?php echo $f['rut']; ?></p>
                    <p><strong>Estado:</strong> <?php echo ($f['idemprendedores']) ? "Emprendedor Activo" : "Solo Persona"; ?></p>
                    <?php if(isset($f['saldo_inicial'])): ?>
                        <p style="color:red;"><strong>Deuda:</strong> $<?php echo number_format($f['saldo_inicial'], 2); ?></p>
                    <?php endif; ?>
                </div>
                <?php
            }
        } else {
            // MENSAJE DE ERROR SI NO HAY RESULTADOS
            echo "<div class='error-msg'>ID o Nombre incorrecto. No se encontró ninguna persona activa con esos datos.</div>";
        }
    }
    ?>
</div>

</body>
</html>