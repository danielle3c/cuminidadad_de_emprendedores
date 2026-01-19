<?php
include 'config.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$mensaje = "";
if (isset($_POST['guardar'])) {
    // Escapar todos los campos para seguridad
    $datos = array_map(function($val) use ($conexion) {
        return mysqli_real_escape_string($conexion, $val);
    }, $_POST);

    $sql = "INSERT INTO encuesta_2026 (
        fecha_encuesta, nombre_local, direccion, representante, cargo, telefono,
        necesidades_productivas, participa_programa_beneficios, beneficio_ofrecido,
        observaciones, conoce_corporacion, contacto_municipalidad, interes_iniciativas, participar_video,
        created_at, created_by
    ) VALUES (
        '{$datos['fecha']}', '{$datos['local']}', '{$datos['direccion']}', '{$datos['rep']}', '{$datos['cargo']}', '{$datos['tel']}',
        '{$datos['necesidades']}', '{$datos['participa']}', '{$datos['beneficio']}',
        '{$datos['obs']}', '{$datos['conoce_corp']}', '{$datos['cont_muni']}', '{$datos['interes']}', '{$datos['video']}',
        NOW(), '{$_SESSION['usuario_id']}'
    )";

    if (mysqli_query($conexion, $sql)) {
        $mensaje = "<div style='background:#def7ec; color:#03543f; padding:15px; border-radius:10px;'>✅ Datos de la planilla guardados con éxito.</div>";
    }
}
?>

<form method="POST" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
    <div style="grid-column: 1 / -1;">
        <h3>Datos de Identificación (Foto 2)</h3>
    </div>
    <input type="date" name="fecha" value="2026-01-15" required>
    <input type="text" name="local" placeholder="Barrio/Local (Ej: Joaquin Edwards Bello)" required>
    <input type="text" name="direccion" placeholder="Dirección Exacta" required>
    <input type="text" name="rep" placeholder="Representante (Ej: Jaime Vergara)" required>
    <input type="text" name="cargo" placeholder="Cargo (Ej: Dueño / Administrador)">
    <input type="text" name="tel" placeholder="Teléfono / Fono">

    <div style="grid-column: 1 / -1;">
        <h3>Necesidades y Beneficios (Foto 1)</h3>
    </div>
    <textarea name="necesidades" placeholder="¿Qué necesidades tiene su local?" style="grid-column: 1 / -1;"></textarea>
    <select name="participa">
        <option value="">¿Participaría en programa de beneficios?</option>
        <option value="SI">SÍ</option>
        <option value="NO">NO</option>
        <option value="EVALUACION">EN EVALUACIÓN</option>
    </select>
    <textarea name="beneficio" placeholder="¿Qué beneficio podría entregar su local?"></textarea>
    <textarea name="obs" placeholder="¿Cómo podría la Corporación ayudar? / Sugerencias" style="grid-column: 1 / -1;"></textarea>

    <div style="grid-column: 1 / -1;">
        <h3>Indicadores Sí/No (Foto 3)</h3>
    </div>
    <select name="conoce_corp"><option value="">¿Conoce la Corporación?</option><option value="SI">SI</option><option value="NO">NO</option></select>
    <select name="cont_muni"><option value="">¿Contacto con Municipalidad?</option><option value="SI">SI</option><option value="NO">NO</option></select>
    <select name="interes"><option value="">¿Interés en Iniciativas?</option><option value="SI">SI</option><option value="NO">NO</option></select>
    <select name="video"><option value="">¿Participar en Video?</option><option value="SI">SI</option><option value="NO">NO</option></select>

    <button name="guardar" style="grid-column: 1 / -1; background: #55b83e; color: white; padding: 20px; border: none; border-radius: 10px; font-weight: bold; cursor: pointer;">
        GUARDAR REGISTRO DE PLANILLA
    </button>
</form>