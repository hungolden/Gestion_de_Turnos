<?php
require("../require_admin.php");
error_reporting(E_ALL ^ E_NOTICE);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Especialidades</title>
<link rel="stylesheet" href="../estilos.css">
</head>
<body>
<?php require("../menu_interior.php"); ?>
<main class="container">
<div style="width: 60%;float: left" >
<form name="carga" id="carga" action="./" method="post">
<input style="width: 100%;" type="text" name="buscar" value="<?php echo isset($_POST['buscar']) ? htmlspecialchars($_POST['buscar']) : ''; ?>" placeholder="Click aquí para buscar por especialidad">
</form>
</div>
<div align="right">
<button type="button" onclick="location.href='alta/'">Nueva Especialidad</button>
</div>
<?php if (isset($_GET['error']) && $_GET['error'] === 'fk') { ?>
<p style="color:#a8433c;">No se puede eliminar: hay especialistas cargados con esta especialidad. Primero reasignalos a otra especialidad, o eliminalos, y después probá de nuevo.</p>
<?php } ?>
<table class="table table-striped table-hover">
<tr>
	<th>#</th>
	<th>Especialidad</th>
	<th>Acciones</th>
</tr>
<?php
$buscar = isset($_POST['buscar']) ? $_POST['buscar'] : '';
$like = '%' . $buscar . '%';
$negocio_id = $_SESSION['usuario_negocio_id'];
$sqls = "SELECT e.id, e.nombre
         FROM especialidades e
         WHERE e.negocio_id = ? AND e.nombre LIKE ?
         ORDER BY e.nombre";
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
	<td>
	<button type="button" onclick="location.href='modificacion/index.php?id=<?php echo $id; ?>'">Modificar</button>
	<button type="button" onclick="if(confirm('¿Eliminar esta especialidad?')) location.href='baja/index.php?id=<?php echo $id; ?>'">Eliminar</button>
	</td>
</tr>
<?php $i++; } $stmt->close(); ?>
</table>
</main>
</body>
</html>
