<?php
require("../conexion.php");
header("Content-Type: text/html;charset=utf-8");

$nombre   = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$email    = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if ($nombre === '' || $email === '' || $password === '') {
	header("Location: index.php?error=2");
	exit;
}

// Chequeamos que el email no esté usado por otra cuenta
$stmtCheck = $conexion->prepare("SELECT id FROM usuarios WHERE email = ?");
$stmtCheck->bind_param("s", $email);
$stmtCheck->execute();
$existe = $stmtCheck->get_result()->fetch_assoc();
$stmtCheck->close();

if ($existe) {
	header("Location: index.php?error=1");
	exit;
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Todo el que se registra públicamente entra como 'usuario' simple,
// sin negocio asignado. El rol 'admin' se obtiene registrando un negocio
// (ver negocio_config/registrar/), y el rol 'especialista' lo asigna
// el administrador de un negocio desde el panel de "Usuarios".
$rol    = 'usuario';
$estado = 'activo';

$stmt = $conexion->prepare("INSERT INTO usuarios (negocio_id, nombre, email, password_hash, rol, estado) VALUES (NULL, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $nombre, $email, $password_hash, $rol, $estado);

if ($stmt->execute()) {
	header("Location: ../login/?registrado=1");
} else {
	die("Error al registrar: " . $stmt->error);
}
$stmt->close();
?>
