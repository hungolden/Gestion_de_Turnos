<?php
require("../../require_admin.php");
header("Content-Type: text/html;charset=utf-8");

$negocio_id    = $_SESSION['usuario_negocio_id'];
$nombre        = $_POST['nombre'];
$email         = $_POST['email'];
$password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
$rol           = $_POST['rol'];
$estado        = $_POST['estado'];

// Un mismo email no puede repetirse entre cuentas
$stmtCheck = $conexion->prepare("SELECT id FROM usuarios WHERE email = ?");
$stmtCheck->bind_param("s", $email);
$stmtCheck->execute();
$existe = $stmtCheck->get_result()->fetch_assoc();
$stmtCheck->close();

if ($existe) {
	die("Ya existe una cuenta con ese email.");
}

$stmt = $conexion->prepare("INSERT INTO usuarios (negocio_id, nombre, email, password_hash, rol, estado) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isssss", $negocio_id, $nombre, $email, $password_hash, $rol, $estado);

if ($stmt->execute()) {
	header("Location: ../");
} else {
	die("Error al guardar: " . $stmt->error);
}
$stmt->close();
?>
