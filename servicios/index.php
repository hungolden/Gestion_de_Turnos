<?php
require("../require_admin.php");
error_reporting(E_ALL ^ E_NOTICE);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Servicios</title>
<link rel="stylesheet" href="../estilos.css">
</head>
<body>
<?php require("../menu_interior.php"); ?>
<main class="container">
<div style="width: 60%;float: left" >
<form name="carga" id="carga" action="./" method="post">
<input style="width: 100%;" type="text" name="buscar" value="<?php echo isset($_POST['buscar']) ? htmlspecialchars($_POST['buscar']) : ''; ?>" placeholder="Click aquí para buscar por servicio">
</form>
</div>
<div align="right">
<button type="button" onclick="location.href='alta/'">Nuevo Servicio</button>
</div>
<?php if (isset($_GET['error']) && $_GET['error'] === 'fk') { ?>
<p style="color:#a8433c;">No se puede eliminar: este servicio ya tiene turnos u otros registros asociados. Si no lo querés seguir ofreciendo, marcalo como "Inactivo" desde "Modificar" en vez de eliminarlo.</p>
<?php } ?>
<?php if (isset($_GET['ok']) && $_GET['ok'] === 'estado') { ?>
<p style="color:#1f6b34;">Estado del servicio actualizado.</p>
<?php } ?>
<table class="table table-striped table-hover">
<tr>
	<th>#</th>
	<th>Servicio</th>
	<th>Duracion (min)</th>
	<th>Precio</th>
	<th>Estado</th>
	<th>Acciones</th>
</tr>
<?php
$buscar = isset($_POST['buscar']) ? $_POST['buscar'] : '';
$like = '%' . $buscar . '%';
$negocio_id = $_SESSION['usuario_negocio_id'];
$sqls = "SELECT s.id, s.nombre, s.duracion_min, s.precio, s.estado
         FROM servicios s
         WHERE s.negocio_id = ? AND s.nombre LIKE ?
         ORDER BY s.nombre";
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
	<td><?php echo htmlspecialchars($fila['nombre']); ?></td>
	<td><?php echo htmlspecialchars($fila['duracion_min']); ?></td>
	<td><?php echo htmlspecialchars($fila['precio']); ?></td>
	<td><?php echo htmlspecialchars($fila['estado']); ?></td>
	<td>
	<form action="estado.php" method="post" style="display:inline;margin:0;">
		<input type="hidden" name="id" value="<?php echo $id; ?>">
		<button type="submit"><?php echo ($fila['estado'] === 'activo') ? 'Desactivar' : 'Activar'; ?></button>
	</form>
	<button type="button" onclick="location.href='modificacion/index.php?id=<?php echo $id; ?>'">Modificar</button>
	<button type="button" onclick="if(confirm('¿Eliminar este servicio?')) location.href='baja/index.php?id=<?php echo $id; ?>'">Eliminar</button>
	</td>
</tr>
<?php $i++; } $stmt->close(); ?>
</table>
</main>
</body>
</html>
