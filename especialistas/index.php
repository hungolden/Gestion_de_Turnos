<?php
require("../require_admin.php");
error_reporting(E_ALL ^ E_NOTICE);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Especialistas</title>
<link rel="stylesheet" href="../estilos.css">
</head>
<body>
<?php require("../menu_interior.php"); ?>
<main class="container">
<div style="width: 60%;float: left" >
<form name="carga" id="carga" action="./" method="post">
<input style="width: 100%;" type="text" name="buscar" value="<?php echo isset($_POST['buscar']) ? htmlspecialchars($_POST['buscar']) : ''; ?>" placeholder="Click aquí para buscar por especialista">
</form>
</div>
<div align="right">
<button type="button" onclick="location.href='alta/'">Nuevo Especialista</button>
</div>
<?php if (isset($_GET['invitado'])) { ?>
<p style="color:#1f6b34;">Invitación enviada. El especialista queda como "pendiente" hasta que la acepte desde sus notificaciones.</p>
<?php } ?>
<?php if (isset($_GET['error']) && $_GET['error'] === 'fk') { ?>
<p style="color:#a8433c;">No se puede eliminar: este especialista ya tiene turnos u otros registros asociados. Si dejó de trabajar acá, marcalo como "Inactivo" desde "Modificar" en vez de eliminarlo.</p>
<?php } ?>
<table class="table table-striped table-hover">
<tr>
	<th>#</th>
	<th>Nombre</th>
	<th>Especialidad</th>
	<th>Estado</th>
	<th>Acciones</th>
</tr>
<?php
$buscar = isset($_POST['buscar']) ? $_POST['buscar'] : '';
$like = '%' . $buscar . '%';
$negocio_id = $_SESSION['usuario_negocio_id'];
$sqls = "SELECT es.id, es.nombre, es.estado, ed.nombre AS especialidad
         FROM especialistas es
         JOIN especialidades ed ON ed.id = es.especialidad_id
         WHERE es.negocio_id = ? AND es.nombre LIKE ?
         ORDER BY es.nombre";
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
	<td><?php echo htmlspecialchars($fila['especialidad']); ?></td>
	<td><?php echo htmlspecialchars($fila['estado']); ?></td>
	<td>
	<button type="button" onclick="location.href='modificacion/index.php?id=<?php echo $id; ?>'">Modificar</button>
	<button type="button" onclick="if(confirm('¿Eliminar este especialista?')) location.href='baja/index.php?id=<?php echo $id; ?>'">Eliminar</button>
	</td>
</tr>
<?php $i++; } $stmt->close(); ?>
</table>
</main>
</body>
</html>
