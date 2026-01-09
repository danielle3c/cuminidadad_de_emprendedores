<?php 
include 'config.php'; 

$mensaje = "";

// 1. OBTENER LA CONFIGURACIÓN ACTUAL
$consulta = mysqli_query($conexion, "SELECT * FROM configuraciones WHERE id = 1");
$cfg = mysqli_fetch_assoc($consulta);

// 2. SISTEMA DE TRADUCCIÓN SIMPLE
$lang = $cfg['idioma'];
$textos = [
    'es' => [
        'titulo' => 'Configuración del Sistema',
        'nombre' => 'Nombre de la Aplicación',
        'tema'   => 'Tema Visual',
        'idioma' => 'Idioma',
        'guardar'=> 'Guardar Cambios',
        'moneda' => 'Símbolo de Moneda',
        'zona'   => 'Zona Horaria',
        'paginas'=> 'Registros por página'
    ],
    'en' => [
        'titulo' => 'System Settings',
        'nombre' => 'Application Name',
        'tema'   => 'Visual Theme',
        'idioma' => 'Language',
        'guardar'=> 'Save Changes',
        'moneda' => 'Currency Symbol',
        'zona'   => 'Timezone',
        'paginas'=> 'Records per page'
    ]
];
$t = $textos[$lang];

// 3. PROCESAR LA ACTUALIZACIÓN
if (isset($_POST['actualizar'])) {
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre_sistema']);
    $tema   = mysqli_real_escape_string($conexion, $_POST['tema_color']);
    $idioma = mysqli_real_escape_string($conexion, $_POST['idioma']);
    
    // Estos campos deben existir en tu tabla 'configuraciones'
    $moneda = mysqli_real_escape_string($conexion, $_POST['moneda']);
    $paginas = (int)$_POST['registros_pagina'];

    $sql = "UPDATE configuraciones SET 
            nombre_sistema = '$nombre', 
            tema_color = '$tema', 
            idioma = '$idioma',
            simbolo_moneda = '$moneda',
            registros_pagina = '$paginas'
            WHERE id = 1";

    if (mysqli_query($conexion, $sql)) {
        echo "<script>window.location.href='configuraciones.php';</script>"; // Recarga para aplicar cambios
    } else {
        $mensaje = "<div class='alert error'>❌ Error: " . mysqli_error($conexion) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="<?php echo $cfg['idioma']; ?>" data-theme="<?php echo $cfg['tema_color']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $t['titulo']; ?> - <?php echo $cfg['nombre_sistema']; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --bg: #f4f7f6; --text: #333; --card: #fff; --primary: #43b02a; --secondary: #6c757d; }
        [data-theme="dark"] { --bg: #1a1a1a; --text: #f0f0f0; --card: #2d2d2d; --primary: #2ecc71; --secondary: #495057; }
        [data-theme="blue"] { --bg: #e0e6ed; --text: #1a2a3a; --card: #fff; --primary: #0056b3; --secondary: #5a6268; }

        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); color: var(--text); padding: 20px; transition: 0.3s; }
        .container { max-width: 700px; margin: auto; background: var(--card); padding: 30px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        h2 { text-align: center; color: var(--primary); margin-bottom: 30px; }
        label { display: block; margin-top: 15px; font-weight: bold; font-size: 0.85em; text-transform: uppercase; color: var(--secondary); }
        input, select { width: 100%; padding: 12px; margin-top: 5px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; background: var(--card); color: var(--text); }
        
        .btn-save { background: var(--primary); color: white; border: none; padding: 15px; width: 100%; border-radius: 10px; cursor: pointer; font-weight: bold; margin-top: 30px; font-size: 1.1em; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        
        .nav-buttons { display: flex; gap: 10px; margin-top: 25px; }
        .btn-nav { flex: 1; text-align: center; padding: 12px; text-decoration: none; border-radius: 8px; font-size: 0.9em; font-weight: bold; color: white !important; }
        .btn-home { background: #333; }
    </style>
</head>
<body>

<div class="container">
    <h2><i class="fas fa-cogs"></i> <?php echo $t['titulo']; ?></h2>
    
    <?php echo $mensaje; ?>

    <form method="POST">
        <label><?php echo $t['nombre']; ?>:</label>
        <input type="text" name="nombre_sistema" value="<?php echo $cfg['nombre_sistema']; ?>" required>

        <div class="grid">
            <div>
                <label><?php echo $t['tema']; ?>:</label>
                <select name="tema_color">
                    <option value="light" <?php if($cfg['tema_color'] == 'light') echo 'selected'; ?>>Light / Claro</option>
                    <option value="dark" <?php if($cfg['tema_color'] == 'dark') echo 'selected'; ?>>Dark / Oscuro</option>
                    <option value="blue" <?php if($cfg['tema_color'] == 'blue') echo 'selected'; ?>>Blue / Azul</option>
                </select>
            </div>
            <div>
                <label><?php echo $t['idioma']; ?>:</label>
                <select name="idioma">
                    <option value="es" <?php if($cfg['idioma'] == 'es') echo 'selected'; ?>>Español</option>
                    <option value="en" <?php if($cfg['idioma'] == 'en') echo 'selected'; ?>>English</option>
                </select>
            </div>
        </div>

        <div class="grid">
            <div>
                <label><?php echo $t['moneda']; ?>:</label>
                <input type="text" name="moneda" value="<?php echo $cfg['simbolo_moneda'] ?? '$'; ?>">
            </div>
            <div>
                <label><?php echo $t['paginas']; ?>:</label>
                <input type="number" name="registros_pagina" value="<?php echo $cfg['registros_pagina'] ?? '10'; ?>">
            </div>
        </div>

        <button type="submit" name="actualizar" class="btn-save">
            <i class="fas fa-save"></i> <?php echo $t['guardar']; ?>
        </button>
    </form>

    <div class="nav-buttons">
        <a href="index.php" class="btn-nav btn-home">Volver</a>
    </div>
</div>

</body>
</html>