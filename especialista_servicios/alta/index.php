<?php
require("../../require_admin.php");
error_reporting(E_ALL ^ E_NOTICE);
$negocio_id = $_SESSION['usuario_negocio_id'];
$especialista_preseleccionado = isset($_GET['especialista_id']) ? $_GET['especialista_id'] : '';

$stmtE = $conexion->prepare("SELECT id, nombre FROM especialistas WHERE negocio_id = ? AND estado = 'activo' ORDER BY nombre");
$stmtE->bind_param("i", $negocio_id);
$stmtE->execute();
$especialistas = $stmtE->get_result();

$stmtS = $conexion->prepare("SELECT id, nombre FROM servicios WHERE negocio_id = ? AND estado = 'activo' ORDER BY nombre");
$stmtS->bind_param("i", $negocio_id);
$stmtS->execute();
$servicios = $stmtS->get_result();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Nueva Asignación Especialista - Servicio</title>
<link rel="stylesheet" href="../../estilos.css">
</head>
<body>
<?php require("../../menu_interior1.php"); ?>
<main class="container">

<form action="alta.php" method="post">
<h3>Especialista</h3>
<select name="especialista_id" id="especialista_id" required>
<option value="">-- Seleccione --</option>
<?php while ($e = $especialistas->fetch_assoc()) { ?>
<option value="<?php echo $e['id']; ?>" <?php echo ($e['id'] == $especialista_preseleccionado) ? 'selected' : ''; ?>><?php echo htmlspecialchars($e['nombre']); ?></option>
<?php } ?>
</select><br>
<h3>Servicios que puede realizar</h3>
<?php while ($s = $servicios->fetch_assoc()) { ?>
<label style="display:block;">
<input type="checkbox" name="servicio_id[]" value="<?php echo $s['id']; ?>"> <?php echo htmlspecialchars($s['nombre']); ?>
</label>
<?php } ?>
<br>
<input type="submit" value="Guardar">
<button type="button" onclick="location.href='../'">Volver</button>
</form>
</main>
</body>
</html>
