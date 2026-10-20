<?php
require("../../require_admin.php");
header("Content-Type: text/html;charset=utf-8");

$id              = $_POST['id'];
$especialidad_id = $_POST['especialidad_id'];
$usuario_id      = (!empty($_POST['usuario_id'])) ? $_POST['usuario_id'] : null;
$nombre          = $_POST['nombre'];
$estado          = $_POST['estado'];

$stmt = $conexion->prepare("UPDATE especialistas SET usuario_id = IF(estado IN ('pendiente','rechazado'), usuario_id, COALESCE(?, usuario_id)), especialidad_id = ?, nombre = ?, estado = IF(estado IN ('pendiente','rechazado'), estado, ?) WHERE id = ? AND negocio_id = ?");
$stmt->bind_param("iissii", $usuario_id, $especialidad_id, $nombre, $estado, $id, $_SESSION['usuario_negocio_id']);

if ($stmt->execute()) {
	header("Location: ../");
} else {
	die("Error al modificar: " . $stmt->error);
}
$stmt->close();
?>
