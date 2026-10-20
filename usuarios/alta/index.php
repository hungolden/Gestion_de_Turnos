<?php
require("../../require_admin.php");
error_reporting(E_ALL ^ E_NOTICE);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Nuevo Usuario</title>
<link rel="stylesheet" href="../../estilos.css">
</head>
<body>
<?php require("../../menu_interior1.php"); ?>
<main class="container">

<form action="alta.php" method="post">
<h3>Nombre</h3>
<input type="text" name="nombre" id="nombre" required><br>
<h3>Email</h3>
<input type="email" name="email" id="email" required><br>
<h3>Contraseña</h3>
<input type="password" name="password" id="password" required><br>
<h3>Rol</h3>
<select name="rol" id="rol">
<option value="especialista">Especialista</option>
<option value="admin">Admin</option>
</select><br>
<h3>Estado</h3>
<select name="estado" id="estado">
<option value="activo">Activo</option>
<option value="inactivo">Inactivo</option>
</select><br>
<br><br>
<input type="submit" value="Guardar">
<button type="button" onclick="location.href='../'">Volver</button>
</form>
</main>
</body>
</html>
