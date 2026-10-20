<?php
require("../require_admin.php");
error_reporting(E_ALL ^ E_NOTICE);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Historial de Turnos</title>
<link rel="stylesheet" href="../estilos.css">
</head>
<body>
<?php require("../menu_interior.php"); ?>
<main class="container">
<div style="width: 60%;float: left" >
<form name="carga" id="carga" action="./" method="post">
<input style="width: 100%;" type="text" name="buscar" value="<?php echo isset($_POST['buscar']) ? htmlspecialchars($_POST['buscar']) : ''; ?>" placeholder="Click aquí para buscar por cliente del turno">
</form>
</div>
<p style="color:#666;">
Este historial se genera automáticamente cada vez que se modifica el estado de un turno. No se edita ni se elimina manualmente, para conservar la trazabilidad de los cambios.
</p>
<table class="table table-striped table-hover">
<tr>
	<th>#</th>
	<th>Turno</th>
	<th>Cliente</th>
	<th>Usuario que hizo el cambio</th>
	<th>Estado anterior</th>
	<th>Estado nuevo</th>
	<th>Fecha del cambio</th>
</tr>
<?php
$buscar = isset($_POST['buscar']) ? $_POST['buscar'] : '';
$like = '%' . $buscar . '%';
$negocio_id = $_SESSION['usuario_negocio_id'];
$sqls = "SELECT h.*, t.cliente_nombre, u.nombre AS usuario_nombre
         FROM historial_turnos h
         JOIN turnos t ON t.id = h.turno_id
         LEFT JOIN usuarios u ON u.id = h.usuario_id
         WHERE t.negocio_id = ? AND t.cliente_nombre LIKE ?
         ORDER BY h.fecha_cambio DESC";
$stmt = $conexion->prepare($sqls);
$stmt->bind_param("is", $negocio_id, $like);
$stmt->execute();
$res = $stmt->get_result();
$i = 1;
while ($fila = $res->fetch_assoc()) {
?>
<tr>
	<td><?php echo $i; ?></td>
	<td>#<?php echo htmlspecialchars($fila['turno_id']); ?></td>
	<td><?php echo htmlspecialchars($fila['cliente_nombre']); ?></td>
	<td><?php echo htmlspecialchars($fila['usuario_nombre'] ?? 'Sistema'); ?></td>
	<td><?php echo htmlspecialchars($fila['estado_anterior'] ?? '-'); ?></td>
	<td><?php echo htmlspecialchars($fila['estado_nuevo']); ?></td>
	<td><?php echo htmlspecialchars($fila['fecha_cambio']); ?></td>
</tr>
<?php $i++; } $stmt->close(); ?>
</table>
</main>
</body>
</html>
