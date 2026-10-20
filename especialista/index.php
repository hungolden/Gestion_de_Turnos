<?php
require("../require_especialista.php");
error_reporting(E_ALL ^ E_NOTICE);

// Mi agenda: los turnos del especialista logueado, de un día (por defecto hoy)
$fecha = (isset($_GET['fecha']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['fecha'])) ? $_GET['fecha'] : date('Y-m-d');
$ayer   = date('Y-m-d', strtotime($fecha . ' -1 day'));
$manana = date('Y-m-d', strtotime($fecha . ' +1 day'));

$stmt = $conexion->prepare("SELECT t.id, t.hora_inicio, t.hora_fin, t.cliente_nombre, t.cliente_contacto, t.estado, sv.nombre AS servicio
                            FROM turnos t
                            JOIN servicios sv ON sv.id = t.servicio_id
                            WHERE t.especialista_id = ? AND t.negocio_id = ? AND t.fecha = ?
                            ORDER BY t.hora_inicio");
$stmt->bind_param("iis", $mi['id'], $negocio_id, $fecha);
$stmt->execute();
$turnos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Mi agenda</title>
<link rel="stylesheet" href="../estilos.css">
<link rel="stylesheet" href="especialista.css">
</head>
<body>
<?php require("../menu_especialista.php"); ?>
<main class="container">
<h2 style="margin-top:0;">Mi agenda · <?php echo h($mi['nombre_local']); ?></h2>

<?php if (isset($_GET['ok'])) { ?>
<p class="aviso-ok"><?php echo ($_GET['ok'] === '2') ? 'Turno registrado correctamente.' : 'Estado actualizado.'; ?></p>
<?php } ?>
<?php if (isset($_GET['error']) && $_GET['error'] === 'cerrado') { ?>
<p class="aviso-err">Un turno Finalizado o Cancelado ya no se puede modificar.</p>
<?php } ?>
<?php if (isset($_GET['error']) && $_GET['error'] === 'choque') { ?>
<p class="aviso-err">No se pudo reactivar el turno: ese horario ya lo ocupa otro turno.</p>
<?php } ?>

<form class="filtros" action="./" method="get">
	<button type="button" onclick="location.href='?fecha=<?php echo $ayer; ?>'">&laquo; Día anterior</button>
	<input type="date" name="fecha" value="<?php echo h($fecha); ?>" onchange="this.form.submit()">
	<button type="button" onclick="location.href='?fecha=<?php echo $manana; ?>'">Día siguiente &raquo;</button>
	<button type="button" onclick="location.href='./'">Hoy</button>
	<span style="flex:1"></span>
	<button type="button" onclick="location.href='nuevo.php?fecha=<?php echo h($fecha); ?>'">Registrar nuevo turno</button>
</form>

<table class="table table-striped table-hover">
<tr><th>Horario</th><th>Servicio</th><th>Cliente</th><th>Contacto</th><th>Estado</th><th>Cambiar estado</th></tr>
<?php foreach ($turnos as $t) {
	// Un turno Finalizado o Cancelado ya no admite cambios de estado
	$cerrado = in_array($t['estado'], array('Finalizado', 'Cancelado'), true);
?>
<tr>
	<td><?php echo h(substr($t['hora_inicio'], 0, 5)); ?> – <?php echo h(substr($t['hora_fin'], 0, 5)); ?></td>
	<td><?php echo h($t['servicio']); ?></td>
	<td><?php echo h($t['cliente_nombre']); ?></td>
	<td><?php echo h($t['cliente_contacto']); ?></td>
	<td><span class="estado est-<?php echo h($t['estado']); ?>"><?php echo h($t['estado']); ?></span></td>
	<td>
	<form class="inline" action="estado.php" method="post">
		<input type="hidden" name="turno_id" value="<?php echo $t['id']; ?>">
		<input type="hidden" name="fecha" value="<?php echo h($fecha); ?>">
		<select name="estado"<?php echo $cerrado ? ' disabled' : ''; ?>>
		<?php foreach ($ESTADOS_TURNO as $e) { ?>
			<option value="<?php echo $e; ?>" <?php echo ($e === $t['estado']) ? 'selected' : ''; ?>><?php echo $e; ?></option>
		<?php } ?>
		</select>
		<button type="submit"<?php echo $cerrado ? ' disabled title="Este turno ya no se puede modificar"' : ''; ?>>Guardar</button>
	</form>
	</td>
</tr>
<?php } ?>
<?php if (empty($turnos)) { ?>
<tr><td colspan="6">No tenés turnos para este día.</td></tr>
<?php } ?>
</table>
</main>
</body>
</html>
