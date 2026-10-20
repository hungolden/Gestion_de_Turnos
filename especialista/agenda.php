<?php
require("../require_especialista.php");
error_reporting(E_ALL ^ E_NOTICE);

// Agenda del local: turnos de TODOS los especialistas del negocio. Solo consulta.
$fecha = (isset($_GET['fecha']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['fecha'])) ? $_GET['fecha'] : date('Y-m-d');
$filtro = isset($_GET['especialista_id']) ? (int)$_GET['especialista_id'] : 0;

$stmtE = $conexion->prepare("SELECT id, nombre FROM especialistas WHERE negocio_id = ? AND estado = 'activo' ORDER BY nombre");
$stmtE->bind_param("i", $negocio_id);
$stmtE->execute();
$especialistas = $stmtE->get_result()->fetch_all(MYSQLI_ASSOC);
$stmtE->close();

$stmt = $conexion->prepare("SELECT t.especialista_id, t.hora_inicio, t.hora_fin, t.cliente_nombre, t.estado, es.nombre AS especialista, sv.nombre AS servicio
                            FROM turnos t
                            JOIN especialistas es ON es.id = t.especialista_id
                            JOIN servicios sv ON sv.id = t.servicio_id
                            WHERE t.negocio_id = ? AND t.fecha = ? AND (? = 0 OR t.especialista_id = ?)
                            ORDER BY t.hora_inicio, es.nombre");
$stmt->bind_param("isii", $negocio_id, $fecha, $filtro, $filtro);
$stmt->execute();
$turnos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Agenda del local</title>
<link rel="stylesheet" href="../estilos.css">
<link rel="stylesheet" href="especialista.css">
</head>
<body>
<?php require("../menu_especialista.php"); ?>
<main class="container">
<h2 style="margin-top:0;">Agenda del local · <?php echo h($mi['nombre_local']); ?></h2>

<form class="filtros" action="agenda.php" method="get">
	<input type="date" name="fecha" value="<?php echo h($fecha); ?>" onchange="this.form.submit()">
	<select name="especialista_id" onchange="this.form.submit()">
		<option value="0">Todos los especialistas</option>
		<?php foreach ($especialistas as $e) { ?>
		<option value="<?php echo $e['id']; ?>" <?php echo ($e['id'] == $filtro) ? 'selected' : ''; ?>><?php echo h($e['nombre']); ?></option>
		<?php } ?>
	</select>
	<button type="button" onclick="location.href='agenda.php'">Hoy</button>
</form>
<p style="color:var(--text-muted);margin-top:0;">Solo consulta. Tus propios turnos se gestionan desde "Mi agenda" (se muestran en negrita).</p>

<table class="table table-striped table-hover">
<tr><th>Horario</th><th>Especialista</th><th>Servicio</th><th>Cliente</th><th>Estado</th></tr>
<?php foreach ($turnos as $t) { ?>
<tr class="<?php echo ($t['especialista_id'] == $mi['id']) ? 'mio' : ''; ?>">
	<td><?php echo h(substr($t['hora_inicio'], 0, 5)); ?> – <?php echo h(substr($t['hora_fin'], 0, 5)); ?></td>
	<td><?php echo h($t['especialista']); ?></td>
	<td><?php echo h($t['servicio']); ?></td>
	<td><?php echo h($t['cliente_nombre']); ?></td>
	<td><span class="estado est-<?php echo h($t['estado']); ?>"><?php echo h($t['estado']); ?></span></td>
</tr>
<?php } ?>
<?php if (empty($turnos)) { ?>
<tr><td colspan="5">No hay turnos para ese día.</td></tr>
<?php } ?>
</table>
</main>
</body>
</html>
