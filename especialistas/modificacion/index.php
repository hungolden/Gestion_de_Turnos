<?php
require("../../require_admin.php");
error_reporting(E_ALL ^ E_NOTICE);
$id = $_GET['id'];
$negocio_id = $_SESSION['usuario_negocio_id'];

$stmt = $conexion->prepare("SELECT * FROM especialistas WHERE id = ? AND negocio_id = ?");
$stmt->bind_param("ii", $id, $negocio_id);
$stmt->execute();
$fila = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$fila) {
	die("Especialista no encontrado.");
}

$stmtEd = $conexion->prepare("SELECT id, nombre FROM especialidades WHERE negocio_id = ? ORDER BY nombre");
$stmtEd->bind_param("i", $negocio_id);
$stmtEd->execute();
$especialidades = $stmtEd->get_result();

$stmtU = $conexion->prepare("SELECT id, nombre FROM usuarios WHERE rol = 'especialista' AND negocio_id = ? ORDER BY nombre");
$stmtU->bind_param("i", $negocio_id);
$stmtU->execute();
$usuarios = $stmtU->get_result();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Modificar Especialista</title>
<link rel="stylesheet" href="../../estilos.css">
</head>
<body>
<?php require("../../menu_interior1.php"); ?>
<main class="container">
<form action="modificar.php" method="post">
<input type="hidden" name="id" id="id" value="<?php echo htmlspecialchars($id); ?>">
<h3>Especialidad</h3>
<select name="especialidad_id" id="especialidad_id" required>
<?php while ($e = $especialidades->fetch_assoc()) { ?>
<option value="<?php echo $e['id']; ?>" <?php echo ($e['id'] == $fila['especialidad_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($e['nombre']); ?></option>
<?php } ?>
</select><br>
<h3>Usuario asociado (opcional)</h3>
<select name="usuario_id" id="usuario_id">
<option value="">-- Ninguno --</option>
<?php while ($u = $usuarios->fetch_assoc()) { ?>
<option value="<?php echo $u['id']; ?>" <?php echo ($u['id'] == $fila['usuario_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($u['nombre']); ?></option>
<?php } ?>
</select><br>
<h3>Nombre</h3>
<input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($fila['nombre']); ?>" required><br>
<h3>Estado</h3>
<?php if ($fila['estado'] === 'pendiente' || $fila['estado'] === 'rechazado') { ?>
<p style="color:#666;"><?php echo ($fila['estado'] === 'pendiente') ? 'Pendiente: la persona todavía no respondió la invitación.' : 'Rechazado: la persona rechazó la invitación.'; ?> El estado cambia solo cuando ella responde.</p>
<?php } else { ?>
<select name="estado" id="estado">
<option value="activo" <?php echo ($fila['estado'] == 'activo') ? 'selected' : ''; ?>>Activo</option>
<option value="inactivo" <?php echo ($fila['estado'] == 'inactivo') ? 'selected' : ''; ?>>Inactivo</option>
</select><br>
<?php } ?>
<br><br>
<input type="submit" value="Guardar">
<button type="button" onclick="location.href='../'">Volver</button>
</form>
</main>
</body>
</html>
