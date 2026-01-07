<?php 
include 'config.php'; 

// 1. Configuración general
$res_conf = mysqli_query($conexion, "SELECT * FROM configuraciones WHERE id = 1");
$cfg = mysqli_fetch_assoc($res_conf);
?>

<!DOCTYPE html>
<html lang="es" data-theme="<?php echo $cfg['tema_color']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $cfg['nombre_sistema']; ?></title>
    <style>
        :root { --bg: #f0f2f5; --card: #ffffff; --text: #333; --primary: #43b02a; --border: #e2e8f0; }
        [data-theme="dark"] { --bg: #18191a; --card: #242526; --text: #e4e6eb; --primary: #2ecc71; --border: #3a3b3c; }

        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); color: var(--text); margin: 0; padding: 0; }
        .nav-bar { background: var(--primary); padding: 15px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .nav-bar a { color: white; text-decoration: none; margin: 0 10px; font-weight: 500; }
        .container { max-width: 1100px; margin: auto; padding: 20px; }

        /* Buscador */
        .search-box { background: var(--card); padding: 25px; border-radius: 15px; text-align: center; margin-bottom: 30px; }
        .search-box input { width: 55%; padding: 12px; border: 2px solid var(--border); border-radius: 8px; background: var(--bg); color: var(--text); }
        .btn-search { padding: 12px 25px; background: var(--primary); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; }

        /* Ficha Maestra de Resultados */
        .master-card { background: var(--card); border-radius: 15px; margin-bottom: 30px; overflow: hidden; box-shadow: 0 8px 20px rgba(0,0,0,0.1); border: 1px solid var(--border); }
        .card-header { background: var(--primary); color: white; padding: 20px; display: flex; align-items: center; gap: 20px; }
        .profile-pic { width: 80px; height: 80px; border-radius: 50%; border: 3px solid white; object-fit: cover; }
        
        .card-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; padding: 20px; }
        .history-box { background: var(--bg); padding: 15px; border-radius: 10px; font-size: 0.9em; border: 1px solid var(--border); }
        .history-box h4 { margin-top: 0; color: var(--primary); border-bottom: 1px solid var(--border); padding-bottom: 5px; margin-bottom: 10px; }
        
        .badge { padding: 3px 8px; border-radius: 5px; font-size: 0.8em; font-weight: bold; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-danger { background: #fee2e2; color: #991b1b; }

        /* Dashboard */
        .dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 15px; }
        .menu-card { background: var(--card); padding: 20px; border-radius: 12px; text-align: center; text-decoration: none; color: inherit; transition: 0.3s; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .menu-card:hover { transform: translateY(-5px); background: var(--primary); color: white !important; }
        .menu-card img { width: 55px; margin-bottom: 12px; }
    </style>
</head>
<body>

<div class="nav-bar">
    <a href="index.php">🏠 Inicio</a>
    <a href="personas.php">👤 Personas</a>
    <a href="configuraciones.php">⚙️ Ajustes</a>
</div>

<div class="container">
    <h1 style="text-align: center; margin-bottom: 30px;">🚀 <?php echo $cfg['nombre_sistema']; ?></h1>

    <div class="search-box">
        <form method="GET">
            <input type="text" name="buscar" placeholder="Escribe nombre o RUT para ver historial..." value="<?php echo $_GET['buscar'] ?? ''; ?>" autofocus>
            <button type="submit" class="btn-search">🔍 Ver Ficha Completa</button>
        </form>
    </div>

    <?php if (isset($_GET['buscar']) && !empty(trim($_GET['buscar']))): ?>
        <div id="resultados">
            <?php 
            $busqueda = mysqli_real_escape_string($conexion, $_GET['buscar']);
            $sql = "SELECT * FROM personas WHERE nombres LIKE '%$busqueda%' OR apellidos LIKE '%$busqueda%' OR rut LIKE '%$busqueda%'";
            $res = mysqli_query($conexion, $sql);

            if(mysqli_num_rows($res) > 0):
                while ($p = mysqli_fetch_assoc($res)) { 
                    $idp = $p['idpersonas'];
                    $avatar = "https://ui-avatars.com/api/?name=".urlencode($p['nombres'])."&background=random&color=fff";
                ?>
                    <div class="master-card">
                        <div class="card-header">
                            <img src="img/fotos/<?php echo $idp; ?>.jpg" class="profile-pic" onerror="this.src='<?php echo $avatar; ?>'">
                            <div>
                                <h2 style="margin:0;"><?php echo $p['nombres'] . " " . $p['apellidos']; ?></h2>
                                <p style="margin:5px 0 0; opacity:0.9;">🆔 RUT: <?php echo $p['rut']; ?> | 📞 <?php echo $p['telefono'] ?? 'S/T'; ?></p>
                            </div>
                        </div>

                        <div class="card-grid">
                            <div class="history-box">
                                <h4>💼 Datos de Negocio</h4>
                                <?php 
                                $q_emp = mysqli_query($conexion, "SELECT * FROM emprendedores WHERE personas_idpersonas = $idp");
                                if($emp = mysqli_fetch_assoc($q_emp)): $ide = $emp['idemprendedores'];
                                ?>
                                    <p><b>Rubro:</b> <?php echo $emp['rubro']; ?></p>
                                    <p><b>Negocio:</b> <?php echo $emp['tipo_negocio']; ?></p>
                                    <p><b>Límite:</b> $<?php echo number_format($emp['limite_credito'], 2); ?></p>
                                <?php else: echo "<p><i>No es emprendedor registrado.</i></p>"; endif; ?>
                            </div>

                            <div class="history-box">
                                <h4>💰 Créditos Activos</h4>
                                <?php 
                                if(isset($ide)){
                                    $q_cre = mysqli_query($conexion, "SELECT * FROM creditos WHERE emprendedores_idemprendedores = $ide AND estado = 1");
                                    if(mysqli_num_rows($q_cre) > 0){
                                        while($c = mysqli_fetch_assoc($q_cre)) {
                                            echo "• Monto: $".number_format($c['monto_inicial'],2)."<br>";
                                            echo "• <b style='color:#e11d48;'>Saldo: $".number_format($c['saldo_inicial'],2)."</b><hr>";
                                        }
                                    } else { echo "<p><i>Sin deudas pendientes.</i></p>"; }
                                } else { echo "<p>---</p>"; }
                                ?>
                            </div>

                            <div class="history-box">
                                <h4>🎪 Carritos Asignados</h4>
                                <?php 
                                if(isset($ide)){
                                    $q_car = mysqli_query($conexion, "SELECT * FROM carritos WHERE emprendedores_idemprendedores = $ide");
                                    if(mysqli_num_rows($q_car) > 0){
                                        while($ca = mysqli_fetch_assoc($q_car)) echo "✅ {$ca['nombre_carrito']}<br><small>Equip: {$ca['equipamiento']}</small><br>";
                                    } else { echo "<p><i>Sin carritos asignados.</i></p>"; }
                                } else { echo "<p>---</p>"; }
                                ?>
                            </div>

                            <div class="history-box">
                                <h4>🧾 Historial de Pagos</h4>
                                <?php 
                                if(isset($ide)){
                                    $q_pag = mysqli_query($conexion, "SELECT cob.* FROM cobranzas cob 
                                             JOIN creditos cre ON cob.creditos_idcreditos = cre.idcreditos 
                                             WHERE cre.emprendedores_idemprendedores = $ide ORDER BY cob.fecha_hora DESC LIMIT 3");
                                    if(mysqli_num_rows($q_pag) > 0){
                                        while($pg = mysqli_fetch_assoc($q_pag)) echo "💵 $".number_format($pg['monto'],2)." <small>(".date('d/m/y', strtotime($pg['fecha_hora'])).")</small><br>";
                                    } else { echo "<p><i>No registra pagos.</i></p>"; }
                                } else { echo "<p>---</p>"; }
                                ?>
                            </div>
                        </div>
                        <div style="padding: 15px; text-align: right; background: var(--bg);">
                             <a href="editar_persona.php?id=<?php echo $idp; ?>" style="color:var(--primary); font-weight:bold; text-decoration:none;">✏️ Editar Perfil Completo</a>
                        </div>
                    </div>
                <?php } 
            else: 
                echo "<div class='alert alert-error'>❌ No se encontró historial para: <b>$busqueda</b></div>";
            endif; ?>
            <p style="text-align: center;"><a href="index.php">← Volver al Menú</a></p>
        </div>
    <?php else: ?>
        <div class="dashboard-grid">
            <a href="personas.php" class="menu-card">
                <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png">
                <span>Personas</span>
            </a>
            <a href="emprendedores.php" class="menu-card">
                <img src="https://cdn-icons-png.flaticon.com/512/3135/3135706.png">
                <span>Negocios</span>
            </a>
            <a href="creditos.php" class="menu-card">
                <img src="https://cdn-icons-png.flaticon.com/512/2489/2489756.png">
                <span>Créditos</span>
            </a>
            <a href="cobranzas.php" class="menu-card">
                <img src="https://cdn-icons-png.flaticon.com/512/1019/1019607.png">
                <span>Cobranzas</span>
            </a>
            <a href="carritos.php" class="menu-card">
                <img src="https://cdn-icons-png.flaticon.com/512/1170/1170678.png">
                <span>Carritos</span>
            </a>
            <a href="configuraciones.php" class="menu-card">
                <img src="https://cdn-icons-png.flaticon.com/512/3953/3953226.png">
                <span>Ajustes</span>
            </a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>