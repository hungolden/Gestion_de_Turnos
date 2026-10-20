<?php
require("../../require_admin.php");
header("Content-Type: text/html;charset=utf-8");

$negocio_id = $_SESSION['usuario_negocio_id'];
$nombre     = $_POST['nombre'];

$stmt = $conexion->prepare("INSERT INTO especialidades (negocio_id, nombre) VALUES (?, ?)");
$stmt->bind_param("is", $negocio_id, $nombre);

if ($stmt->execute()) {
	header("Location: ../");
} else {
	die("Error al guardar: " . $stmt->error);
}
$stmt->close();
?>
