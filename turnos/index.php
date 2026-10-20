<?php
require("../require_admin.php");
error_reporting(E_ALL ^ E_NOTICE);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Turnos</title>
<link rel="stylesheet" href="../estilos.css">
</head>
<body>
<?php require("../menu_interior.php"); ?>
<main class="container">
<div style="width: 60%;float: left" >
<form name="carga" id="carga" action="./" method="post">
<input style="width: 100%;" type="text" name="buscar" value="<?php echo isset($_POST['buscar']) ? htmlspecialchars($_POST['buscar']) : ''; ?>" placeholder="Click aquí para buscar por cliente">
</form>
</div>
<div align="right">
<button type="button" onclick="location.href='alta/'">Nuevo Turno</button>
</div>
<?php if (isset($_GET['error']) && $_GET['error'] === 'fk') { ?>
<p style="color:#a8433c;">No se puede eliminar: este turno tiene historial de cambios asociado. Cambiale el estado a "Cancelado" desde "Modificar" en vez de eliminarlo.</p>
<?php } ?>
<table class="table table-striped table-hover">
<tr>
	<th>#</th>
	<th>Especialista</th>
	<th>Servicio</th>
	<th>Cliente</th>
	<th>Contacto</th>
	<th>Fecha</th>
	<th>Inicio</th>
	<th>Fin</th>
	<th>Estado</th>
	<th>Acciones</th>
</tr>
<?php
$buscar = isset($_POST['buscar']) ? $_POST['buscar'] : '';
$like = '%' . $buscar . '%';
$negocio_id = $_SESSION['usuario_negocio_id'];
$sqls = "SELECT t.*, es.nombre AS especialista, sv.nombre AS servicio
         FROM turnos t
         JOIN especialistas es ON es.id = t.especialista_id
         JOIN servicios sv ON sv.id = t.servicio_id
         WHERE t.negocio_id = ? AND t.cliente_nombre LIKE ?
         ORDER BY t.fecha DESC, t.hora_inicio DESC";
$stmt = $conexion->prepare($sqls);
$stmt->bind_param("is", $negocio_id, $like);
$stmt->execute();
$res = $stmt->get_result();
$i = 1;
while ($fila = $res->fetch_assoc()) {
$id = $fila['id'];
?>
<tr>
	<td><?php echo $i; ?></td>
	<td><?php echo htmlspecialchars($fila['especialista']); ?></td>
	<td><?php echo htmlspecialchars($fila['servicio']); ?></td>
	<td><?php echo htmlspecialchars($fila['cliente_nombre']); ?></td>
	<td><?php echo htmlspecialchars($fila['cliente_contacto']); ?></td>
	<td><?php echo htmlspecialchars($fila['fecha']); ?></td>
	<td><?php echo htmlspecialchars($fila['hora_inicio']); ?></td>
	<td><?php echo htmlspecialchars($fila['hora_fin']); ?></td>
	<td><?php echo htmlspecialchars($fila['estado']); ?></td>
	<td>
	<button type="button" onclick="location.href='modificacion/index.php?id=<?php echo $id; ?>'">Modificar</button>
	<button type="button" onclick="if(confirm('¿Eliminar este turno?')) location.href='baja/index.php?id=<?php echo $id; ?>'">Eliminar</button>
	</td>
</tr>
<?php $i++; } $stmt->close(); ?>
</table>
</main>
</body>
</html>
