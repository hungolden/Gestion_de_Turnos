<?php
require("../../require_admin.php");
header("Content-Type: text/html;charset=utf-8");

$negocio_id      = $_SESSION['usuario_negocio_id'];
$especialista_id = $_GET['especialista_id'];
$servicio_id     = $_GET['servicio_id'];

// El especialista tiene que ser de mi negocio para poder borrar la asignación
$stmtCheck = $conexion->prepare("SELECT id FROM especialistas WHERE id = ? AND negocio_id = ?");
$stmtCheck->bind_param("ii", $especialista_id, $negocio_id);
$stmtCheck->execute();
if (!$stmtCheck->get_result()->fetch_assoc()) {
	die("Asignación no encontrada.");
}
$stmtCheck->close();

$stmt = $conexion->prepare("DELETE FROM especialista_servicios WHERE especialista_id = ? AND servicio_id = ?");
$stmt->bind_param("ii", $especialista_id, $servicio_id);

if ($stmt->execute()) {
	header("Location: ../");
} else {
	die("Error al eliminar: " . $stmt->error);
}
$stmt->close();
?>
