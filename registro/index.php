<?php
require("../conexion.php");
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
// Si ya está logueado, no tiene sentido mostrarle el registro
if (isset($_SESSION['usuario_id'])) {
	header("Location: " . BASE_URL . "/");
	exit;
}
error_reporting(E_ALL ^ E_NOTICE);

$error = isset($_GET['error']) ? $_GET['error'] : '';
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Registrarme</title>
<link rel="stylesheet" href="../estilos.css">
</head>
<body>
<main class="auth-box">
<h1>Crear cuenta</h1>

<?php if ($error === '1') { ?>
<p style="color:red;">Ese email ya está registrado.</p>
<?php } elseif ($error === '2') { ?>
<p style="color:red;">Completá todos los campos obligatorios.</p>
<?php } ?>

<form action="registrar.php" method="post">
<h3>Nombre</h3>
<input type="text" name="nombre" id="nombre" required><br>
<h3>Email</h3>
<input type="email" name="email" id="email" required><br>
<h3>Contraseña</h3>
<input type="password" name="password" id="password" required><br>
<br><br>
<input type="submit" value="Registrarme">
</form>

<p style="text-align:center;font-size:.9rem;">
¿Ya tenés cuenta? <a href="../login/">Iniciar sesión</a>
</p>
</main>
</body>
</html>
