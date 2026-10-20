<?php
require("../../require_admin.php");
error_reporting(E_ALL ^ E_NOTICE);
$id = $_GET['id'];

$stmt = $conexion->prepare("SELECT * FROM usuarios WHERE id = ? AND negocio_id = ?");
$stmt->bind_param("ii", $id, $_SESSION['usuario_negocio_id']);
$stmt->execute();
$fila = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$fila) {
	die("Usuario no encontrado.");
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Modificar Usuario</title>
<link rel="stylesheet" href="../../estilos.css">
</head>
<body>
<?php require("../../menu_interior1.php"); ?>
<main class="container">
<form action="modificar.php" method="post">
<input type="hidden" name="id" id="id" value="<?php echo htmlspecialchars($id); ?>">
<h3>Nombre</h3>
<input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($fila['nombre']); ?>" required><br>
<h3>Email</h3>
<input type="email" name="email" id="email" value="<?php echo htmlspecialchars($fila['email']); ?>" required><br>
<h3>Contraseña (dejar en blanco para no modificarla)</h3>
<input type="password" name="password" id="password"><br>
<h3>Rol</h3>
<select name="rol" id="rol">
<option value="admin" <?php echo ($fila['rol'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
<option value="especialista" <?php echo ($fila['rol'] == 'especialista') ? 'selected' : ''; ?>>Especialista</option>
</select><br>
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
