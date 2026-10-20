<?php
require("../../require_admin.php");
error_reporting(E_ALL ^ E_NOTICE);

$negocio_id      = $_SESSION['usuario_negocio_id'];
$especialista_id = isset($_GET['especialista_id']) ? (int)$_GET['especialista_id'] : 0;

// Especialistas ya cargados en el apartado "Especialistas" (activos, de mi negocio)
$stmt = $conexion->prepare("SELECT id, nombre FROM especialistas WHERE negocio_id = ? AND estado = 'activo' ORDER BY nombre");
$stmt->bind_param("i", $negocio_id);
$stmt->execute();
$especialistas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Si llega un id, tiene que ser de uno de mis especialistas
$elegido = false;
foreach ($especialistas as $e) {
	if ($e['id'] == $especialista_id) { $elegido = true; break; }
}
if (!$elegido) { $especialista_id = 0; }

// Servicios activos del negocio y cuáles ya tiene asignados este especialista
$servicios = array();
$asignados = array();
if ($especialista_id) {
	$stmt = $conexion->prepare("SELECT id, nombre, duracion_min, precio FROM servicios WHERE negocio_id = ? AND estado = 'activo' ORDER BY nombre");
	$stmt->bind_param("i", $negocio_id);
	$stmt->execute();
	$servicios = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
	$stmt->close();

	$stmt = $conexion->prepare("SELECT servicio_id FROM especialista_servicios WHERE especialista_id = ?");
	$stmt->bind_param("i", $especialista_id);
	$stmt->execute();
	foreach ($stmt->get_result()->fetch_all(MYSQLI_ASSOC) as $r) { $asignados[] = $r['servicio_id']; }
	$stmt->close();
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Editar servicios del especialista</title>
<link rel="stylesheet" href="../../estilos.css">
</head>
<body>
<?php require("../../menu_interior1.php"); ?>
<main class="container">
<form action="modificar.php" method="post">
<h3>Especialista</h3>
<select name="especialista_id" required onchange="location.href='?especialista_id=' + this.value">
	<option value="">-- Seleccione --</option>
	<?php foreach ($especialistas as $e) { ?>
	<option value="<?php echo $e['id']; ?>" <?php echo ($e['id'] == $especialista_id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($e['nombre']); ?></option>
	<?php } ?>
</select>

<?php if ($especialista_id) { ?>
	<h3>Servicios que puede realizar</h3>
	<?php if (empty($servicios)) { ?>
		<p>Todavía no hay servicios activos. Cargalos primero en el apartado <a href="../../servicios/">Servicios</a>.</p>
	<?php } ?>
	<?php foreach ($servicios as $s) { ?>
	<label style="display:block;margin-bottom:.3rem;">
		<input type="checkbox" name="servicio_id[]" value="<?php echo $s['id']; ?>" <?php echo in_array($s['id'], $asignados) ? 'checked' : ''; ?>>
		<?php echo htmlspecialchars($s['nombre']); ?>
		<span style="color:var(--text-muted);">(<?php echo (int)$s['duracion_min']; ?> min · $<?php echo htmlspecialchars($s['precio']); ?>)</span>
	</label>
	<?php } ?>
	<br>
	<input type="submit" value="Guardar">
<?php } else { ?>
	<p style="color:var(--text-muted);">Elegí un especialista para asignarle servicios.</p>
<?php } ?>
<button type="button" onclick="location.href='../<?php echo $especialista_id ? '?sel=' . $especialista_id : ''; ?>'">Volver</button>
</form>
</main>
</body>
</html>
