<?php
require("../../require_admin.php");
error_reporting(E_ALL ^ E_NOTICE);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Nuevo Servicio</title>
<link rel="stylesheet" href="../../estilos.css">
</head>
<body>
<?php require("../../menu_interior1.php"); ?>
<main class="container">

<?php if (isset($_GET['error']) && $_GET['error'] === 'duplicado') { ?>
<p style="color:#a8433c;">Ya existe un servicio con ese nombre. Elegí otro nombre.</p>
<?php } ?>
<form action="alta.php" method="post">
<h3>Servicio</h3>
<input type="text" name="nombre" id="nombre" value="<?php echo isset($_GET['nombre']) ? htmlspecialchars($_GET['nombre']) : ''; ?>" required><br>
<h3>Duracion (minutos)</h3>
<input type="number" name="duracion_min" id="duracion_min" min="1" value="<?php echo isset($_GET['duracion_min']) ? htmlspecialchars($_GET['duracion_min']) : ''; ?>" required><br>
<h3>Precio</h3>
<input type="number" step="0.01" min="0" name="precio" id="precio" value="<?php echo isset($_GET['precio']) ? htmlspecialchars($_GET['precio']) : ''; ?>" required><br>
<h3>Estado</h3>
<select name="estado" id="estado">
<option value="activo">Activo</option>
<option value="inactivo" <?php echo (isset($_GET['estado']) && $_GET['estado'] === 'inactivo') ? 'selected' : ''; ?>>Inactivo</option>
</select><br>
<br><br>
<input type="submit" value="Guardar">
<button type="button" onclick="location.href='../'">Volver</button>
</form>
</main>
</body>
</html>
