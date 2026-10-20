<?php
require("../../require_admin.php");
error_reporting(E_ALL ^ E_NOTICE);
$id = $_GET['id'];
$negocio_id = $_SESSION['usuario_negocio_id'];

$stmt = $conexion->prepare("SELECT * FROM turnos WHERE id = ? AND negocio_id = ?");
$stmt->bind_param("ii", $id, $negocio_id);
$stmt->execute();
$fila = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$fila) {
	die("Turno no encontrado.");
}

$stmtE = $conexion->prepare("SELECT id, nombre FROM especialistas WHERE negocio_id = ? ORDER BY nombre");
$stmtE->bind_param("i", $negocio_id);
$stmtE->execute();
$especialistas = $stmtE->get_result();

$stmtS = $conexion->prepare("SELECT id, nombre, duracion_min FROM servicios WHERE negocio_id = ? ORDER BY nombre");
$stmtS->bind_param("i", $negocio_id);
$stmtS->execute();
$servicios = $stmtS->get_result();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Modificar Turno</title>
<link rel="stylesheet" href="../../estilos.css">
</head>
<body>
<?php require("../../menu_interior1.php"); ?>
<main class="container">
<form action="modificar.php" method="post">
<input type="hidden" name="id" id="id" value="<?php echo htmlspecialchars($id); ?>">
<h3>Especialista</h3>
<select name="especialista_id" id="especialista_id" required>
<?php while ($e = $especialistas->fetch_assoc()) { ?>
<option value="<?php echo $e['id']; ?>" <?php echo ($e['id'] == $fila['especialista_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($e['nombre']); ?></option>
<?php } ?>
</select><br>
<h3>Servicio</h3>
<select name="servicio_id" id="servicio_id" required>
<?php while ($s = $servicios->fetch_assoc()) { ?>
<option value="<?php echo $s['id']; ?>" <?php echo ($s['id'] == $fila['servicio_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($s['nombre']); ?> (<?php echo $s['duracion_min']; ?> min)</option>
<?php } ?>
</select><br>
<h3>Cliente</h3>
<input type="text" name="cliente_nombre" id="cliente_nombre" value="<?php echo htmlspecialchars($fila['cliente_nombre']); ?>" required><br>
<h3>Contacto del cliente</h3>
<input type="text" name="cliente_contacto" id="cliente_contacto" value="<?php echo htmlspecialchars($fila['cliente_contacto']); ?>"><br>
<h3>Fecha</h3>
<input type="date" name="fecha" id="fecha" value="<?php echo htmlspecialchars($fila['fecha']); ?>" required><br>
<h3>Hora de inicio</h3>
<input type="time" name="hora_inicio" id="hora_inicio" value="<?php echo htmlspecialchars($fila['hora_inicio']); ?>" required><br>
<p style="color:#666;font-size:0.9em;">La hora de fin se recalcula automáticamente según la duración del servicio elegido.</p>
<h3>Estado</h3>
<select name="estado" id="estado">
<option value="Confirmado" <?php echo ($fila['estado'] == 'Confirmado') ? 'selected' : ''; ?>>Confirmado</option>
<option value="Retrasado" <?php echo ($fila['estado'] == 'Retrasado') ? 'selected' : ''; ?>>Retrasado</option>
<option value="Asistido" <?php echo ($fila['estado'] == 'Asistido') ? 'selected' : ''; ?>>Asistido</option>
<option value="Finalizado" <?php echo ($fila['estado'] == 'Finalizado') ? 'selected' : ''; ?>>Finalizado</option>
<option value="Cancelado" <?php echo ($fila['estado'] == 'Cancelado') ? 'selected' : ''; ?>>Cancelado</option>
</select><br>
<br><br>
<input type="submit" value="Guardar">
<button type="button" onclick="location.href='../'">Volver</button>
</form>
</main>
</body>
</html>
