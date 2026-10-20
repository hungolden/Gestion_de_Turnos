<?php
require("../../require_admin.php");
header("Content-Type: text/html;charset=utf-8");

$negocio_id       = $_SESSION['usuario_negocio_id'];
$especialista_id  = $_POST['especialista_id'];
$servicio_id      = $_POST['servicio_id'];
$cliente_nombre   = $_POST['cliente_nombre'];
$cliente_contacto = $_POST['cliente_contacto'];
$fecha            = $_POST['fecha'];
$hora_inicio      = $_POST['hora_inicio'];

// Calculamos hora_fin en base a la duración del servicio elegido.
// El WHERE con negocio_id evita que se pueda cargar un turno usando
// un servicio de otro negocio (por ejemplo, editando el HTML a mano).
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

$stmt = $conexion->prepare("INSERT INTO turnos (negocio_id, especialista_id, servicio_id, cliente_nombre, cliente_contacto, fecha, hora_inicio, hora_fin, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Confirmado')");
$stmt->bind_param("iiisssss", $negocio_id, $especialista_id, $servicio_id, $cliente_nombre, $cliente_contacto, $fecha, $hora_inicio, $hora_fin);

if ($stmt->execute()) {
	header("Location: ../");
} else {
	die("Error al guardar: " . $stmt->error);
}
$stmt->close();
?>
