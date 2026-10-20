<?php
require("../../require_admin.php");
error_reporting(E_ALL ^ E_NOTICE);
$negocio_id = $_SESSION['usuario_negocio_id'];

$stmtE = $conexion->prepare("SELECT id, nombre FROM especialistas WHERE negocio_id = ? AND estado = 'activo' ORDER BY nombre");
$stmtE->bind_param("i", $negocio_id);
$stmtE->execute();
$especialistas = $stmtE->get_result();

$stmtS = $conexion->prepare("SELECT id, nombre, duracion_min FROM servicios WHERE negocio_id = ? AND estado = 'activo' ORDER BY nombre");
$stmtS->bind_param("i", $negocio_id);
$stmtS->execute();
$servicios = $stmtS->get_result();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Nuevo Turno</title>
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
<option value="<?php echo $e['id']; ?>"><?php echo htmlspecialchars($e['nombre']); ?></option>
<?php } ?>
</select><br>
<h3>Servicio</h3>
<select name="servicio_id" id="servicio_id" required>
<option value="">-- Seleccione --</option>
<?php while ($s = $servicios->fetch_assoc()) { ?>
<option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['nombre']); ?> (<?php echo $s['duracion_min']; ?> min)</option>
<?php } ?>
</select><br>
<h3>Cliente</h3>
<input type="text" name="cliente_nombre" id="cliente_nombre" required><br>
<h3>Contacto del cliente</h3>
<input type="text" name="cliente_contacto" id="cliente_contacto"><br>
<h3>Fecha</h3>
<input type="date" name="fecha" id="fecha" required><br>
<h3>Hora de inicio</h3>
<input type="time" name="hora_inicio" id="hora_inicio" required><br>
<p style="color:#666;font-size:0.9em;">La hora de fin se calcula automáticamente según la duración del servicio elegido.</p>
<br><br>
<input type="submit" value="Guardar">
<button type="button" onclick="location.href='../'">Volver</button>
</form>
</main>
</body>
</html>
