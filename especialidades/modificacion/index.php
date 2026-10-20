<?php
require("../../require_admin.php");
error_reporting(E_ALL ^ E_NOTICE);
$id = $_GET['id'];

$stmt = $conexion->prepare("SELECT * FROM especialidades WHERE id = ? AND negocio_id = ?");
$stmt->bind_param("ii", $id, $_SESSION['usuario_negocio_id']);
$stmt->execute();
$fila = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Si no existe o no pertenece a mi negocio, no la puedo editar
if (!$fila) {
	die("Especialidad no encontrada.");
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Modificar Especialidad</title>
<link rel="stylesheet" href="../../estilos.css">
</head>
<body>
<?php require("../../menu_interior1.php"); ?>
<main class="container">
<form action="modificar.php" method="post">
<input type="hidden" name="id" id="id" value="<?php echo htmlspecialchars($id); ?>">
<h3>Especialidad</h3>
<input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($fila['nombre']); ?>" required><br>
<br><br>
<input type="submit" value="Guardar">
<button type="button" onclick="location.href='../'">Volver</button>
</form>
</main>
</body>
</html>
