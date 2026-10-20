<?php
require("../conexion.php");
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
header("Content-Type: text/html;charset=utf-8");

$email    = $_POST['email'];
$password = $_POST['password'];

$stmt = $conexion->prepare("SELECT id, nombre, email, password_hash, rol, estado, negocio_id FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Verificamos que el usuario exista y que la contraseña coincida con el hash guardado
if (!$usuario || !password_verify($password, $usuario['password_hash'])) {
	header("Location: index.php?error=1");
	exit;
}

// Bloqueamos el ingreso si el usuario está marcado como inactivo
if ($usuario['estado'] !== 'activo') {
	header("Location: index.php?error=2");
	exit;
}

// Credenciales correctas: guardamos los datos del usuario en la sesión.
// usuario_id es el que usa historial_turnos para registrar quién hizo cada cambio.
$_SESSION['usuario_id']         = $usuario['id'];
$_SESSION['usuario_nombre']     = $usuario['nombre'];
$_SESSION['usuario_email']      = $usuario['email'];
$_SESSION['usuario_rol']        = $usuario['rol'];
$_SESSION['usuario_negocio_id'] = $usuario['negocio_id'];

header("Location: " . BASE_URL . "/");
exit;
?>
