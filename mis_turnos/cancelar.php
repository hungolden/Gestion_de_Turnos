<?php
require("../auth_check.php");
header("Content-Type: text/html;charset=utf-8");

$id = isset($_GET['id']) ? $_GET['id'] : '';
$usuario_id = $_SESSION['usuario_id'];

// Solo puede cancelar un turno que sea suyo
$stmt = $conexion->prepare("SELECT estado FROM turnos WHERE id = ? AND cliente_usuario_id = ?");
$stmt->bind_param("ii", $id, $usuario_id);
$stmt->execute();
$turno = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$turno) {
	die("Turno no encontrado.");
}

$estado_anterior = $turno['estado'];

$stmt = $conexion->prepare("UPDATE turnos SET estado = 'Cancelado' WHERE id = ? AND cliente_usuario_id = ?");
$stmt->bind_param("ii", $id, $usuario_id);

if ($stmt->execute()) {
	if ($estado_anterior !== 'Cancelado') {
		$stmtHist = $conexion->prepare("INSERT INTO historial_turnos (turno_id, usuario_id, estado_anterior, estado_nuevo) VALUES (?, ?, ?, 'Cancelado')");
		$stmtHist->bind_param("iis", $id, $usuario_id, $estado_anterior);
		$stmtHist->execute();
		$stmtHist->close();
	}
	header("Location: ./");
} else {
	die("Error al cancelar: " . $stmt->error);
}
$stmt->close();
?>
