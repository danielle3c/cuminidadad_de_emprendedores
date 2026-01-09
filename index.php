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
        :root { 
            --bg: #f8fafc; --card: #ffffff; --text: #1e293b; --primary: #43b02a; 
            --primary-soft: #dcfce7; --border: #e2e8f0; --accent: #0f172a;
        }
        [data-theme="dark"] { 
            --bg: #0f172a; --card: #1e293b; --text: #f1f5f9; --primary: #2ecc71; 
            --primary-soft: rgba(46, 204, 113, 0.1); --border: #334155; 
        }

        body { font-family: 'Inter', 'Segoe UI', sans-serif; background: var(--bg); color: var(--text); margin: 0; line-height: 1.5; }
        
        /* Navbar Moderna */
        .nav-bar { background: var(--card); padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); }
        .nav-logo { font-weight: 800; color: var(--primary); font-size: 1.2rem; text-decoration: none; }
        .nav-links a { color: var(--text); text-decoration: none; margin-left: 20px; font-size: 0.9rem; font-weight: 500; transition: 0.2s; }
        .nav-links a:hover { color: var(--primary); }

        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }

        /* Buscador Estilo Hero */
        .hero-search { text-align: center; margin-bottom: 50px; }
        .hero-search h1 { font-size: 2.2rem; margin-bottom: 20px; letter-spacing: -1px; }
        .search-wrapper { position: relative; max-width: 600px; margin: auto; }
        .search-wrapper input { 
            width: 100%; padding: 16px 25px; border: 2px solid var(--border); border-radius: 50px; 
            background: var(--card); color: var(--text); font-size: 1rem; box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            transition: 0.3s; box-sizing: border-box;
        }
        .search-wrapper input:focus { border-color: var(--primary); outline: none; box-shadow: 0 10px 25px rgba(67, 176, 42, 0.15); }
        .btn-search-icon { 
            position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
            background: var(--primary); color: white; border: none; padding: 10px 20px; border-radius: 40px; cursor: pointer;
        }

        /* Ficha Maestra "Premium" */
        .master-card { 
            background: var(--card); border-radius: 20px; margin-bottom: 40px; 
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); border: 1px solid var(--border);
            animation: slideUp 0.4s ease-out;
        }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        .card-header { padding: 30px; display: flex; align-items: center; gap: 25px; border-bottom: 1px solid var(--border); }
        .profile-pic { width: 90px; height: 90px; border-radius: 24px; object-fit: cover; border: 4px solid var(--primary-soft); }
        
        .card-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1px; background: var(--border); }
        .history-box { background: var(--card); padding: 25px; }
        .history-box h4 { 
            margin: 0 0 15px 0; font-size: 0.75rem; text-transform: uppercase; 
            letter-spacing: 1px; color: var(--primary); display: flex; align-items: center; gap: 8px;
        }
        
        .data-value { font-size: 1.1rem; font-weight: 700; display: block; margin-bottom: 4px; }
        .data-label { font-size: 0.8rem; opacity: 0.6; }

        .badge { padding: 4px 10px; border-radius: 6px; font-size: 0.7rem; font-weight: 800; }
        .badge-danger { background: #fee2e2; color: #ef4444; }
        .badge-success { background: var(--primary-soft); color: var(--primary); }

        /* Dashboard Grid */
        .dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 20px; }
        .menu-card { 
            background: var(--card); padding: 30px; border-radius: 20px; text-align: center; 
            text-decoration: none; color: inherit; transition: 0.3s; border: 1px solid var(--border);
        }
        .menu-card:hover { border-color: var(--primary); transform: translateY(-8px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
        .menu-card img { width: 48px; height: 48px; margin-bottom: 15px; filter: grayscale(0.2); }
        .menu-card span { display: block; font-weight: 700; font-size: 0.9rem; }

        hr { border: 0; border-top: 1px solid var(--border); margin: 15px 0; }
    </style>
</head>
<body>

<div class="nav-bar">
    <a href="index.php" class="nav-logo"><?php echo $cfg['nombre_sistema']; ?></a>
    <div class="nav-links">
        <a href="personas.php">Personas</a>
        <a href="emprendedores.php">Negocios</a>
        <a href="configuraciones.php">Ajustes</a>
    </div>
</div>

<div class="container">

    <div class="hero-search">
        <h1>¿A quién buscamos hoy?</h1>
        <form method="GET" class="search-wrapper">
            <input type="text" name="buscar" placeholder="Nombre, apellido o RUT..." value="<?php echo $_GET['buscar'] ?? ''; ?>" autofocus autocomplete="off">
            <button type="submit" class="btn-search-icon">Buscar</button>
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
                    $avatar = "https://ui-avatars.com/api/?name=".urlencode($p['nombres'])."&background=random&color=fff&size=128";
                ?>
                    <div class="master-card">
                        <div class="card-header">
                            <img src="img/fotos/<?php echo $idp; ?>.jpg" class="profile-pic" onerror="this.src='<?php echo $avatar; ?>'">
                            <div>
                                <span class="badge <?php echo ($idp % 2 == 0) ? 'badge-success' : 'badge-success'; ?>">Cliente Verificado</span>
                                <h2 style="margin:5px 0; font-size: 1.8rem;"><?php echo $p['nombres'] . " " . $p['apellidos']; ?></h2>
                                <div style="display:flex; gap: 15px; font-size: 0.9rem; opacity: 0.7;">
                                    <span><?php echo $p['rut']; ?></span>
                                    <span><?php echo $p['telefono'] ?? 'No registrado'; ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="card-grid">
                            <div class="history-box">
                                <h4>Negocio</h4>
                                <?php 
                                $q_emp = mysqli_query($conexion, "SELECT * FROM emprendedores WHERE personas_idpersonas = $idp");
                                if($emp = mysqli_fetch_assoc($q_emp)): $ide = $emp['idemprendedores'];
                                ?>
                                    <span class="data-value"><?php echo $emp['tipo_negocio']; ?></span>
                                    <span class="data-label"><?php echo $emp['rubro']; ?></span>
                                    <hr>
                                    <span class="data-label">Límite: $<?php echo number_format($emp['limite_credito'], 0, ',', '.'); ?></span>
                                <?php else: echo "<p class='data-label'>No vinculado a emprendimientos.</p>"; endif; ?>
                            </div>

                            <div class="history-box">
                                <h4>Créditos</h4>
                                <?php 
                                if(isset($ide)){
                                    $q_cre = mysqli_query($conexion, "SELECT SUM(saldo_inicial) as deuda FROM creditos WHERE emprendedores_idemprendedores = $ide AND estado = 1");
                                    $cre = mysqli_fetch_assoc($q_cre);
                                    if($cre['deuda'] > 0){
                                        echo "<span class='data-value' style='color:#ef4444'>$".number_format($cre['deuda'], 0, ',', '.')."</span>";
                                        echo "<span class='badge badge-danger'>Deuda Pendiente</span>";
                                    } else { 
                                        echo "<span class='data-value'>$0</span>";
                                        echo "<span class='badge badge-success'>Al día</span>";
                                    }
                                } else { echo "<p class='data-label'>Sin historial crediticio.</p>"; }
                                ?>
                            </div>

                            <div class="history-box">
                                <h4>Activos</h4>
                                <?php 
                                if(isset($ide)){
                                    $q_car = mysqli_query($conexion, "SELECT COUNT(*) as total FROM carritos WHERE emprendedores_idemprendedores = $ide");
                                    $car = mysqli_fetch_assoc($q_car);
                                    echo "<span class='data-value'>".$car['total']." Unidad(es)</span>";
                                    echo "<span class='data-label'>Carritos / Puestos</span>";
                                } else { echo "<p class='data-label'>Sin activos asignados.</p>"; }
                                ?>
                            </div>

                            <div class="history-box" style="background: var(--primary-soft); display: flex; flex-direction: column; justify-content: center;">
                                <a href="editar_persona.php?id=<?php echo $idp; ?>" class="nav-links" style="text-align:center; font-weight:800; color: var(--primary); text-decoration:none;">
                                    GESTIONAR PERFIL →
                                </a>
                            </div>
                        </div>
                    </div>
                <?php } 
            else: 
                echo "<div style='text-align:center; padding: 40px;'>
                        <img src='https://cdn-icons-png.flaticon.com/512/6134/6134065.png' width='80' style='opacity:0.2'>
                        <p style='opacity:0.5'>No encontramos a nadie con ese nombre o RUT.</p>
                </div>";
            endif; ?>
        </div>
    <?php else: ?>
        
        <div class="dashboard-grid">
            <a href="personas.php" class="menu-card">
                <img src="https://cdn-icons-png.flaticon.com/512/565/565431.png">
                <span>Personas</span>
            </a>
            <a href="emprendedores.php" class="menu-card">
                <img src="https://thumbs.dreamstime.com/b/s%C3%ADmbolo-avatar-de-perfil-masculino-icono-emprendedor-con-bombilla-para-ideas-creativas-el-desarrollo-negocios-en-pictograma-glifo-152261996.jpg">
                <span>Emprendedores</span>
            </a>
            <a href="creditos.php" class="menu-card">
                <img src="https://us.123rf.com/450wm/jemastock/jemastock1907/jemastock190760457/127255575-hand-holding-a-credit-card-and-money-coins-icon-cartoon-in-black-and-white-vector-illustration.jpg">
                <span>Créditos</span>
            </a>
            <a href="cobranzas.php" class="menu-card">
                <img src="https://cdn-icons-png.flaticon.com/512/1611/1611154.png">
                <span>Cobranzas</span>
            </a>
            <a href="carritos.php" class="menu-card">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQDfZnJWF_fUNzIcD2nZa-SklDug4gKq5Axuw&s">
                <span>Carritos</span>
            </a>
            <a href="configuraciones.php" class="menu-card">
                <img src="https://cdn-icons-png.flaticon.com/512/6063/6063673.png">
                <span>Ajustes</span>
            </a>
        </div>

    <?php endif; ?>
</div>

</body>
</html>