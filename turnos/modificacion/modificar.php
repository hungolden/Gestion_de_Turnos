<?php
require("../../require_admin.php");
header("Content-Type: text/html;charset=utf-8");

$negocio_id       = $_SESSION['usuario_negocio_id'];
$id               = $_POST['id'];
$especialista_id  = $_POST['especialista_id'];
$servicio_id      = $_POST['servicio_id'];
$cliente_nombre   = $_POST['cliente_nombre'];
$cliente_contacto = $_POST['cliente_contacto'];
$fecha            = $_POST['fecha'];
$hora_inicio      = $_POST['hora_inicio'];
$estado           = $_POST['estado'];

// Recalculamos hora_fin según la duración del servicio elegido
// (validando que el servicio pertenezca a mi negocio)
$stmtServ = $conexion->prepare("SELECT duracion_min FROM servicios WHERE id = ? AND negocio_id = ?");
$stmtServ->bind_param("ii", $servicio_id, $negocio_id);
$stmtServ->execute();
$servicio = $stmtServ->get_result()->fetch_assoc();
$stmtServ->close();

if (!$servicio) {
	die("El servicio seleccionado no existe.");
}

$duracion_min = $servicio['duracion_min'];
$hora_fin = date("H:i:s", strtotime($hora_inicio) + ($duracion_min * 60));

// Guardamos el estado actual del turno ANTES de modificarlo, para poder
// registrar el cambio en historial_turnos si el estado se modifica
$stmtActual = $conexion->prepare("SELECT estado FROM turnos WHERE id = ? AND negocio_id = ?");
$stmtActual->bind_param("ii", $id, $negocio_id);
$stmtActual->execute();
$turnoActual = $stmtActual->get_result()->fetch_assoc();
$stmtActual->close();

if (!$turnoActual) {
	die("Turno no encontrado.");
}
$estado_anterior = $turnoActual['estado'];

$stmt = $conexion->prepare("UPDATE turnos SET especialista_id = ?, servicio_id = ?, cliente_nombre = ?, cliente_contacto = ?, fecha = ?, hora_inicio = ?, hora_fin = ?, estado = ? WHERE id = ? AND negocio_id = ?");
$stmt->bind_param("iissssssii", $especialista_id, $servicio_id, $cliente_nombre, $cliente_contacto, $fecha, $hora_inicio, $hora_fin, $estado, $id, $negocio_id);

if ($stmt->execute()) {
	// Si el estado cambió, dejamos constancia en historial_turnos
	if ($estado_anterior !== $estado) {
		$usuario_id = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;
		$stmtHist = $conexion->prepare("INSERT INTO historial_turnos (turno_id, usuario_id, estado_anterior, estado_nuevo) VALUES (?, ?, ?, ?)");
		$stmtHist->bind_param("iiss", $id, $usuario_id, $estado_anterior, $estado);
		$stmtHist->execute();
		$stmtHist->close();
	}
	header("Location: ../");
} else {
	die("Error al modificar: " . $stmt->error);
}
$stmt->close();
?>
