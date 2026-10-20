<?php
require("../../require_admin.php");
error_reporting(E_ALL ^ E_NOTICE);
$id = $_GET['id'];

$stmt = $conexion->prepare("SELECT * FROM servicios WHERE id = ? AND negocio_id = ?");
$stmt->bind_param("ii", $id, $_SESSION['usuario_negocio_id']);
$stmt->execute();
$fila = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$fila) {
	die("Servicio no encontrado.");
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Modificar Servicio</title>
<link rel="stylesheet" href="../../estilos.css">
</head>
<body>
<?php require("../../menu_interior1.php"); ?>
<main class="container">
<?php if (isset($_GET['error']) && $_GET['error'] === 'duplicado') { ?>
<p style="color:#a8433c;">Ya existe otro servicio con ese nombre. Elegí otro nombre.</p>
<?php } ?>
<form action="modificar.php" method="post">
<input type="hidden" name="id" id="id" value="<?php echo htmlspecialchars($id); ?>">
<h3>Servicio</h3>
<input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($fila['nombre']); ?>" required><br>
<h3>Duracion (minutos)</h3>
<input type="number" name="duracion_min" id="duracion_min" min="1" value="<?php echo htmlspecialchars($fila['duracion_min']); ?>" required><br>
<h3>Precio</h3>
<input type="number" step="0.01" min="0" name="precio" id="precio" value="<?php echo htmlspecialchars($fila['precio']); ?>" required><br>
<h3>Estado</h3>
<select name="estado" id="estado">
<option value="activo" <?php echo ($fila['estado'] == 'activo') ? 'selected' : ''; ?>>Activo</option>
<option value="inactivo" <?php echo ($fila['estado'] == 'inactivo') ? 'selected' : ''; ?>>Inactivo</option>
</select><br>
<br><br>
<input type="submit" value="Guardar">
<button type="button" onclick="location.href='../'">Volver</button>
</form>
</main>
</body>
</html>
