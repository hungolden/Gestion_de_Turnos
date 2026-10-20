<?php
require("../../auth_check.php");
error_reporting(E_ALL ^ E_NOTICE);

// Solo un usuario que todavía no tiene negocio propio puede registrar uno
if ($_SESSION['usuario_rol'] !== 'usuario' || !empty($_SESSION['usuario_negocio_id'])) {
	header("Location: " . BASE_URL . "/");
	exit;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Registrar mi negocio</title>
<link rel="stylesheet" href="../../estilos.css">
</head>
<body>
<main class="auth-box">
<h1>Registrar mi negocio</h1>
<p style="color:#666;font-size:.9rem;">Al crear tu negocio, tu cuenta pasa a ser la administradora de ese negocio.</p>

<form action="registrar.php" method="post">
<h3>Nombre del local</h3>
<input type="text" name="nombre_local" id="nombre_local" required><br>
<h3>Rubro</h3>
<input type="text" name="rubro" id="rubro" required><br>
<h3>Contacto</h3>
<input type="text" name="contacto" id="contacto"><br>
<h3>Apertura</h3>
<input type="time" name="apertura" id="apertura" required><br>
<h3>Cierre</h3>
<input type="time" name="cierre" id="cierre" required><br>
<h3>Cancelacion (hs de anticipación)</h3>
<input type="number" name="cancelacion_hs" id="cancelacion_hs" min="0" value="2"><br>
<br><br>
<input type="submit" value="Crear negocio">
</form>

<p style="text-align:center;font-size:.9rem;"><a href="../../">Volver a Inicio</a></p>
</main>
</body>
</html>
