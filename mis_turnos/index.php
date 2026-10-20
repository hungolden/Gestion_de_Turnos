<?php
require("../auth_check.php");
error_reporting(E_ALL ^ E_NOTICE);

$stmt = $conexion->prepare("SELECT t.*, n.nombre_local, es.nombre AS especialista, sv.nombre AS servicio
                            FROM turnos t
                            JOIN negocio_config n ON n.id = t.negocio_id
                            JOIN especialistas es ON es.id = t.especialista_id
                            JOIN servicios sv ON sv.id = t.servicio_id
                            WHERE t.cliente_usuario_id = ?
                            ORDER BY t.fecha DESC, t.hora_inicio DESC");
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$turnos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Mis Turnos</title>
<link rel="stylesheet" href="../estilos.css">
</head>
<body>
<?php require("../menu_usuario.php"); ?>
<main class="container">
<h2>Mis turnos</h2>

<?php if (isset($_GET['reservado'])) { ?>
<p style="color:#2e7d32;">¡Turno reservado con éxito!</p>
<?php } ?>

<?php if (empty($turnos)) { ?>
<p>Todavía no reservaste ningún turno. <a href="../turnos_disponibles/">Buscar un turno</a>.</p>
<?php } else { ?>
<table class="table table-striped table-hover">
<tr>
	<th>Negocio</th>
	<th>Servicio</th>
	<th>Especialista</th>
	<th>Fecha</th>
	<th>Horario</th>
	<th>Estado</th>
	<th>Acciones</th>
</tr>
<?php foreach ($turnos as $t) { ?>
<tr>
	<td><?php echo htmlspecialchars($t['nombre_local']); ?></td>
	<td><?php echo htmlspecialchars($t['servicio']); ?></td>
	<td><?php echo htmlspecialchars($t['especialista']); ?></td>
	<td><?php echo htmlspecialchars($t['fecha']); ?></td>
	<td><?php echo htmlspecialchars(substr($t['hora_inicio'], 0, 5)); ?> - <?php echo htmlspecialchars(substr($t['hora_fin'], 0, 5)); ?></td>
	<td><?php echo htmlspecialchars($t['estado']); ?></td>
	<td>
	<?php if ($t['estado'] === 'Confirmado') { ?>
	<button type="button" onclick="if(confirm('¿Cancelar este turno?')) location.href='cancelar.php?id=<?php echo $t['id']; ?>'">Cancelar</button>
	<?php } ?>
	</td>
</tr>
<?php } ?>
</table>
<?php } ?>
</main>
</body>
</html>
