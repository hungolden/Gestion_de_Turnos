<?php
require("../../require_admin.php");
header("Content-Type: text/html;charset=utf-8");

$id     = $_POST['id'];
$nombre = $_POST['nombre'];

// El WHERE incluye negocio_id: un admin nunca puede modificar una
// especialidad que no sea de su propio negocio, aunque adivine el id.
$stmt = $conexion->prepare("UPDATE especialidades SET nombre = ? WHERE id = ? AND negocio_id = ?");
$stmt->bind_param("sii", $nombre, $id, $_SESSION['usuario_negocio_id']);

if ($stmt->execute()) {
	header("Location: ../");
} else {
	die("Error al modificar: " . $stmt->error);
}
$stmt->close();
?>
