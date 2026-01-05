<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comunidad de Emprendedores - Panel</title>
    <style>
        :root { --p: #2563eb; --s: #64748b; --bg: #f8fafc; --txt: #1e293b; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); color: var(--txt); margin: 0; }
        .nav { background: white; padding: 15px 5%; box-shadow: 0 2px 4px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; }
        .container { max-width: 1100px; margin: 30px auto; padding: 0 20px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }
        .card { background: white; padding: 25px; border-radius: 12px; text-decoration: none; color: var(--txt); box-shadow: 0 2px 10px rgba(0,0,0,0.05); text-align: center; transition: 0.3s; border: 1px solid #e2e8f0; }
        .card:hover { transform: translateY(-5px); border-color: var(--p); }
        .card h3 { margin: 10px 0 5px; }
        .card p { font-size: 0.9em; color: var(--s); }
        .btn-plus { font-size: 24px; color: var(--p); display: block; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 12px; margin-top: 30px; overflow: hidden; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { background: #f1f5f9; color: var(--s); font-size: 12px; text-transform: uppercase; }
    </style>
</head>
<body>
    <div class="nav">
        <h2 style="color:var(--p)">EmprendeGestión 🚀</h2>
        <span>Panel del Trabajador</span>
    </div>
    <div class="container">
        <div class="grid">
            <a href="personas.php" class="card"><span class="btn-plus">👤</span><h3>Personas</h3><p>Registrar y ver lista</p></a>
            <a href="emprendedores.php" class="card"><span class="btn-plus">💼</span><h3>Emprendedores</h3><p>Gestión de negocios</p></a>
            <a href="creditos.php" class="card"><span class="btn-plus">💰</span><h3>Créditos</h3><p>Contratos y cuotas</p></a>
            <a href="talleres.php" class="card"><span class="btn-plus">🎓</span><h3>Talleres</h3><p>Cursos y asistencias</p></a>
        </div>

        <h3>Últimas Personas Registradas</h3>
        <table>
            <thead><tr><th>Nombre</th><th>Teléfono</th><th>Email</th><th>Estado</th></tr></thead>
            <tbody>
                <?php
                $res = mysqli_query($conexion, "SELECT * FROM personas ORDER BY idpersonas DESC LIMIT 5");
                while($f = mysqli_fetch_assoc($res)) {
                    echo "<tr><td>{$f['nombres']} {$f['apellidos']}</td><td>{$f['telefono']}</td><td>{$f['email']}</td><td>Activo</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>