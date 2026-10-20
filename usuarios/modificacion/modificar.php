<?php
require("../../require_admin.php");
header("Content-Type: text/html;charset=utf-8");

$negocio_id = $_SESSION['usuario_negocio_id'];
$id         = $_POST['id'];
$nombre     = $_POST['nombre'];
$email      = $_POST['email'];
$rol        = $_POST['rol'];
$estado     = $_POST['estado'];

if (!empty($_POST['password'])) {
	$password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
	$stmt = $conexion->prepare("UPDATE usuarios SET nombre = ?, email = ?, password_hash = ?, rol = ?, estado = ? WHERE id = ? AND negocio_id = ?");
	$stmt->bind_param("sssssii", $nombre, $email, $password_hash, $rol, $estado, $id, $negocio_id);
} else {
	$stmt = $conexion->prepare("UPDATE usuarios SET nombre = ?, email = ?, rol = ?, estado = ? WHERE id = ? AND negocio_id = ?");
	$stmt->bind_param("ssssii", $nombre, $email, $rol, $estado, $id, $negocio_id);
}

if ($stmt->execute()) {
	header("Location: ../");
} else {
	die("Error al modificar: " . $stmt->error);
}
$stmt->close();
?>
