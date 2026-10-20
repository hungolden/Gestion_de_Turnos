<?php
require("../auth_check.php");
header("Content-Type: text/html;charset=utf-8");

$especialista_id = isset($_GET['especialista_id']) ? $_GET['especialista_id'] : '';
$servicio_id      = isset($_GET['servicio_id']) ? $_GET['servicio_id'] : '';
$fecha            = isset($_GET['fecha']) ? $_GET['fecha'] : '';
$hora             = isset($_GET['hora']) ? $_GET['hora'] : '';

if ($especialista_id === '' || $servicio_id === '' || $fecha === '' || $hora === '') {
	die("Faltan datos para reservar el turno.");
}

// Traemos el servicio y confirmamos que ese especialista realmente lo ofrezca,
// y de paso obtenemos el negocio_id (el cliente nunca lo elige a mano)
$stmt = $conexion->prepare("SELECT sv.negocio_id, sv.duracion_min
                            FROM servicios sv
                            JOIN especialista_servicios esv ON esv.servicio_id = sv.id
                            WHERE sv.id = ? AND esv.especialista_id = ? AND sv.estado = 'activo'");
$stmt->bind_param("ii", $servicio_id, $especialista_id);
$stmt->execute();
$servicio = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$servicio) {
	die("El servicio elegido ya no está disponible.");
}

$negocio_id   = $servicio['negocio_id'];
$duracion_min = $servicio['duracion_min'];
$hora_inicio  = $hora . ':00';
$hora_fin     = date("H:i:s", strtotime($hora_inicio) + ($duracion_min * 60));

// Revalidamos que el horario siga libre (evita que dos personas reserven
// el mismo horario si llegan casi al mismo tiempo)
$stmtCheck = $conexion->prepare("SELECT id FROM turnos
                                 WHERE especialista_id = ? AND fecha = ? AND estado <> 'Cancelado'
                                 AND hora_inicio < ? AND hora_fin > ?");
$stmtCheck->bind_param("isss", $especialista_id, $fecha, $hora_fin, $hora_inicio);
$stmtCheck->execute();
$ocupado = $stmtCheck->get_result()->fetch_assoc();
$stmtCheck->close();

if ($ocupado) {
	header("Location: tienda.php?id=" . $negocio_id . "&servicio_id=" . $servicio_id . "&fecha=" . urlencode($fecha) . "&error=1");
	exit;
}

$cliente_usuario_id = $_SESSION['usuario_id'];
$cliente_nombre      = $_SESSION['usuario_nombre'];
$cliente_contacto    = isset($_SESSION['usuario_email']) ? $_SESSION['usuario_email'] : '';

$stmt = $conexion->prepare("INSERT INTO turnos (negocio_id, especialista_id, servicio_id, cliente_nombre, cliente_contacto, cliente_usuario_id, fecha, hora_inicio, hora_fin, estado)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Confirmado')");
$stmt->bind_param("iiississs", $negocio_id, $especialista_id, $servicio_id, $cliente_nombre, $cliente_contacto, $cliente_usuario_id, $fecha, $hora_inicio, $hora_fin);

if ($stmt->execute()) {
	header("Location: ../mis_turnos/?reservado=1");
} else {
	die("Error al reservar: " . $stmt->error);
}
$stmt->close();
?>
