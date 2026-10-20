<?php
require("../require_admin.php");
error_reporting(E_ALL ^ E_NOTICE);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Usuarios</title>
<link rel="stylesheet" href="../estilos.css">
</head>
<body>
<?php require("../menu_interior.php"); ?>
<main class="container">
<div style="width: 60%;float: left" >
<form name="carga" id="carga" action="./" method="post">
<input style="width: 100%;" type="text" name="buscar" value="<?php echo isset($_POST['buscar']) ? htmlspecialchars($_POST['buscar']) : ''; ?>" placeholder="Click aquí para buscar por usuario">
</form>
</div>
<div align="right">
<button type="button" onclick="location.href='alta/'">Nuevo Usuario</button>
</div>
<?php if (isset($_GET['error']) && $_GET['error'] === 'fk') { ?>
<p style="color:#a8433c;">No se puede eliminar: esta cuenta está asociada a un especialista o a turnos ya reservados. Marcala como "Inactivo" desde "Modificar" en vez de eliminarla.</p>
<?php } ?>
<p style="color:#666;font-size:0.9em;">Se muestran solo los usuarios (admins y especialistas) de tu negocio. Los clientes que se registran por su cuenta no aparecen acá hasta que registren un negocio o sean asignados como especialistas.</p>
<table class="table table-striped table-hover">
<tr>
	<th>#</th>
	<th>Nombre</th>
	<th>Email</th>
	<th>Rol</th>
	<th>Estado</th>
	<th>Acciones</th>
</tr>
<?php
$buscar = isset($_POST['buscar']) ? $_POST['buscar'] : '';
$like = '%' . $buscar . '%';
$negocio_id = $_SESSION['usuario_negocio_id'];
$sqls = "SELECT u.id, u.nombre, u.email, u.rol, u.estado
         FROM usuarios u
         WHERE u.negocio_id = ? AND u.nombre LIKE ?
         ORDER BY u.nombre";
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
	<td><?php echo htmlspecialchars($fila['email']); ?></td>
	<td><?php echo htmlspecialchars($fila['rol']); ?></td>
	<td><?php echo htmlspecialchars($fila['estado']); ?></td>
	<td>
	<button type="button" onclick="location.href='modificacion/index.php?id=<?php echo $id; ?>'">Modificar</button>
	<button type="button" onclick="if(confirm('¿Eliminar este usuario?')) location.href='baja/index.php?id=<?php echo $id; ?>'">Eliminar</button>
	</td>
</tr>
<?php $i++; } $stmt->close(); ?>
</table>
</main>
</body>
</html>
