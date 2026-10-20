<?php
require("../require_especialista.php");
header("Content-Type: text/html;charset=utf-8");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header("Location: ./");
	exit;
}

$turno_id = isset($_POST['turno_id']) ? (int)$_POST['turno_id'] : 0;
$nuevo    = isset($_POST['estado']) ? $_POST['estado'] : '';
$fecha    = (isset($_POST['fecha']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST['fecha'])) ? $_POST['fecha'] : date('Y-m-d');

if (!in_array($nuevo, $ESTADOS_TURNO, true)) {
	die("Estado inválido.");
}

// Solo puede tocar turnos que sean suyos y de su negocio
$stmt = $conexion->prepare("SELECT estado, fecha, hora_inicio, hora_fin FROM turnos WHERE id = ? AND especialista_id = ? AND negocio_id = ?");
$stmt->bind_param("iii", $turno_id, $mi['id'], $negocio_id);
$stmt->execute();
$turno = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$turno) {
	die("Turno no encontrado.");
}

$anterior = $turno['estado'];

// Un turno Finalizado o Cancelado ya no puede cambiar de estado
if (in_array($anterior, array('Finalizado', 'Cancelado'), true)) {
	header("Location: ./?fecha=" . urlencode($fecha) . "&error=cerrado");
	exit;
}

if ($anterior !== $nuevo) {
	// Si se reactiva un turno cancelado, su horario no puede haber sido tomado por otro
	if ($anterior === 'Cancelado') {
		$stmtC = $conexion->prepare("SELECT id FROM turnos WHERE especialista_id = ? AND fecha = ? AND id <> ? AND estado <> 'Cancelado' AND hora_inicio < ? AND hora_fin > ?");
		$stmtC->bind_param("isiss", $mi['id'], $turno['fecha'], $turno_id, $turno['hora_fin'], $turno['hora_inicio']);
		$stmtC->execute();
		$choque = $stmtC->get_result()->fetch_assoc();
		$stmtC->close();
		if ($choque) {
			header("Location: ./?fecha=" . urlencode($fecha) . "&error=choque");
			exit;
		}
	}

	$stmtU = $conexion->prepare("UPDATE turnos SET estado = ? WHERE id = ? AND especialista_id = ? AND negocio_id = ?");
	$stmtU->bind_param("siii", $nuevo, $turno_id, $mi['id'], $negocio_id);
	$stmtU->execute();
	$stmtU->close();

	// Queda registrado en la bitácora (quién, de qué estado a cuál, cuándo)
	$stmtH = $conexion->prepare("INSERT INTO historial_turnos (turno_id, usuario_id, estado_anterior, estado_nuevo) VALUES (?, ?, ?, ?)");
	$stmtH->bind_param("iiss", $turno_id, $_SESSION['usuario_id'], $anterior, $nuevo);
	$stmtH->execute();
	$stmtH->close();
}

header("Location: ./?fecha=" . urlencode($fecha) . "&ok=1");
exit;
?>
