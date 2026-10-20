<?php
require("../../require_admin.php");
error_reporting(E_ALL ^ E_NOTICE);

// Un admin solo puede ver/editar el negocio que le pertenece a su propia cuenta,
// nunca uno elegido por id externo.
$id = $_SESSION['usuario_negocio_id'];
if (empty($id)) {
	header("Location: " . BASE_URL . "/");
	exit;
}

$stmt = $conexion->prepare("SELECT * FROM negocio_config WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$fila = $res->fetch_assoc();
$stmt->close();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Mi Negocio</title>
<link rel="stylesheet" href="../../estilos.css">
</head>
<body>
<?php require("../../menu_interior1.php"); ?>
<main class="container">
<form action="modificar.php" method="post">
<h3>Nombre Local</h3>
<input type="text" name="nombre_local" id="nombre_local" value="<?php echo htmlspecialchars($fila['nombre_local']); ?>" required><br>
<h3>Rubro</h3>
<input type="text" name="rubro" id="rubro" value="<?php echo htmlspecialchars($fila['rubro']); ?>" required><br>
<h3>Contacto</h3>
<input type="text" name="contacto" id="contacto" value="<?php echo htmlspecialchars($fila['contacto']); ?>"><br>
<h3>Apertura</h3>
<input type="time" name="apertura" id="apertura" value="<?php echo htmlspecialchars($fila['apertura']); ?>" required><br>
<h3>Cierre</h3>
<input type="time" name="cierre" id="cierre" value="<?php echo htmlspecialchars($fila['cierre']); ?>" required><br>
<h3>Cancelacion (hs de anticipación)</h3>
<input type="number" name="cancelacion_hs" id="cancelacion_hs" min="0" value="<?php echo htmlspecialchars($fila['cancelacion_hs']); ?>"><br>
<br><br>
<input type="submit" value="Guardar">
</form>
</main>
</body>
</html>
