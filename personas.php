<?php 
include 'config.php'; 
// Configuración para el diseño
$res_conf = mysqli_query($conexion, "SELECT * FROM configuraciones WHERE id = 1");
$cfg = mysqli_fetch_assoc($res_conf);
?>
<!DOCTYPE html>
<html lang="es" data-theme="<?php echo $cfg['tema_color']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Personas | <?php echo $cfg['nombre_sistema']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { 
            --bg: #f8fafc; --card: #ffffff; --text: #1e293b; --primary: #43b02a; --border: #e2e8f0; 
        }
        [data-theme="dark"] { 
            --bg: #0f172a; --card: #1e293b; --text: #f1f5f9; --primary: #2ecc71; --border: #334155; 
        }

        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); margin: 0; padding: 20px; }
        
        .form-container { 
            background: var(--card); padding: 35px; border-radius: 20px; 
            max-width: 600px; margin: 40px auto; 
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); 
            border: 1px solid var(--border);
        }

        h2 { margin-top: 0; font-weight: 700; color: var(--primary); text-align: center; }
        p.subtitle { text-align: center; font-size: 0.9rem; opacity: 0.7; margin-bottom: 25px; }

        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .full-width { grid-column: span 2; }

        label { display: block; margin-bottom: 6px; font-weight: 600; font-size: 0.85rem; }
        
        input, select, textarea { 
            width: 100%; padding: 12px; border: 2px solid var(--border); 
            border-radius: 10px; background: var(--bg); color: var(--text);
            font-size: 0.95rem; transition: 0.2s; box-sizing: border-box;
        }

        input:focus, select:focus, textarea:focus { 
            border-color: var(--primary); outline: none; box-shadow: 0 0 0 4px rgba(67, 176, 42, 0.1); 
        }

        button { 
            background: var(--primary); color: white; border: none; padding: 15px; 
            width: 100%; border-radius: 12px; cursor: pointer; font-weight: 700; 
            font-size: 1rem; margin-top: 20px; transition: 0.3s;
        }
        button:hover { opacity: 0.9; transform: translateY(-2px); }

        .alert { padding: 15px; border-radius: 10px; margin-bottom: 20px; font-weight: 600; text-align: center; }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        
        a.back-link { display: block; text-align: center; margin-top: 20px; text-decoration: none; color: var(--text); opacity: 0.6; font-size: 0.9rem; }
        a.back-link:hover { opacity: 1; color: var(--primary); }
    </style>
</head>
<body>

<div class="form-container">
    <h2>👤 Registro de Persona</h2>
    <p class="subtitle">Ingrese los datos para dar de alta en el sistema.</p>

    <?php
    if(isset($_POST['guardar'])){
        $rut = mysqli_real_escape_string($conexion, $_POST['rut']);
        $nom = mysqli_real_escape_string($conexion, $_POST['nombres']);
        $ape = mysqli_real_escape_string($conexion, $_POST['apellidos']);
        $gen = $_POST['genero'];
        $fec = $_POST['fecha_nacimiento'];
        $tel = mysqli_real_escape_string($conexion, $_POST['telefono']);
        $ema = mysqli_real_escape_string($conexion, $_POST['email']);
        $dir = mysqli_real_escape_string($conexion, $_POST['direccion']);

        $sql = "INSERT INTO personas (rut, nombres, apellidos, fecha_nacimiento, genero, telefono, direccion, email, created_at, estado) 
                VALUES ('$rut', '$nom', '$ape', '$fec', '$gen', '$tel', '$dir', '$ema', NOW(), 1)";

        if(mysqli_query($conexion, $sql)){
            echo "<div class='alert alert-success'>✅ Registro exitoso de $nom $ape</div>";
        } else {
            if(mysqli_errno($conexion) == 1062) {
                echo "<div class='alert alert-error'>⚠️ Error: El RUT ya existe.</div>";
            } else {
                echo "<div class='alert alert-error'>❌ Error: " . mysqli_error($conexion) . "</div>";
            }
        }
    }
    ?>

    <form method="POST" autocomplete="off">
        <div class="form-grid">
            <div class="full-width">
                <label>RUT / Documento:</label>
                <input type="text" name="rut" id="rut" placeholder="Ej: 12.345.678-9" required maxlength="12">
            </div>

            <div>
                <label>Nombres:</label>
                <input type="text" name="nombres" placeholder="Ej: Juan Pedro" required>
            </div>
            
            <div>
                <label>Apellidos:</label>
                <input type="text" name="apellidos" placeholder="Ej: Pérez García" required>
            </div>
            
            <div>
                <label>Género:</label>
                <select name="genero">
                    <option value="Masculino">Masculino</option>
                    <option value="Femenino">Femenino</option>
                    <option value="Otro">Otro</option>
                </select>
            </div>

            <div>
                <label>Fecha de Nacimiento:</label>
                <input type="date" name="fecha_nacimiento" max="<?php echo date('Y-m-d'); ?>" required>
            </div>
            
            <div>
                <label>Teléfono:</label>
                <input type="tel" name="telefono" placeholder="+56 9 ...">
            </div>
            
            <div>
                <label>Email:</label>
                <input type="email" name="email" placeholder="correo@ejemplo.com">
            </div>

            <div class="full-width">
                <label>Dirección:</label>
                <textarea name="direccion" rows="2" placeholder="Calle, número y ciudad..."></textarea>
            </div>
        </div>

        <button type="submit" name="guardar">Registrar Persona en Sistema</button>
    </form>

    <a href="index.php" class="back-link">⬅ Volver al Buscador Principal</a>
</div>

<script>
    // Formateador de RUT automático
    const inputRut = document.getElementById('rut');
    inputRut.addEventListener('input', (e) => {
        let value = e.target.value.replace(/\./g, '').replace('-', '');
        if (value.length > 1) {
            let body = value.slice(0, -1);
            let dv = value.slice(-1).toUpperCase();
            value = body + '-' + dv;
        }
        e.target.value = value;
    });
</script>

</body>
</html>