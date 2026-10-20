<?php
require("../conexion.php");
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
// Si ya está logueado, no tiene sentido mostrarle el login de nuevo
if (isset($_SESSION['usuario_id'])) {
	header("Location: " . BASE_URL . "/");
	exit;
}
$error = isset($_GET['error']) ? $_GET['error'] : '';
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Iniciar sesión</title>
<link rel="stylesheet" href="../estilos.css">
</head>
<body>
<main class="auth-box">
<h1>Iniciar sesión</h1>
<?php if ($error === '1') { ?>
<p style="color:red;">Email o contraseña incorrectos.</p>
<?php } elseif ($error === '2') { ?>
<p style="color:red;">Tu usuario está inactivo. Contactá al administrador.</p>
<?php } ?>
<?php if (isset($_GET['registrado'])) { ?>
<p style="color:#2e7d32;">Cuenta creada con éxito. Ya podés iniciar sesión.</p>
<?php } ?>
<form action="login.php" method="post">
<h3>Email</h3>
<input type="email" name="email" id="email" required autofocus><br>
<h3>Contraseña</h3>
<input type="password" name="password" id="password" required><br>
<br><br>
<input type="submit" value="Ingresar">
</form>

<p style="text-align:center;font-size:.9rem;">
¿No tenés cuenta? <a href="../registro/">Registrarme</a>
</p>
</main>
</body>
</html>
