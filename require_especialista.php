<?php
// Se incluye al principio de toda página del panel del especialista.
// Exige: sesión iniciada + rol 'especialista' + ficha de especialista ACTIVA
// en el negocio (o sea, que ya aceptó la invitación del local).
// Deja disponible $mi (id, nombre, nombre_local, apertura, cierre) y $negocio_id.
require_once __DIR__ . "/auth_check.php";
date_default_timezone_set('America/Argentina/Buenos_Aires');

if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'especialista') {
	header("Location: " . BASE_URL . "/");
	exit;
}

$negocio_id = (int)$_SESSION['usuario_negocio_id'];
$stmtMi = $conexion->prepare("SELECT es.id, es.nombre, n.nombre_local, n.apertura, n.cierre
                              FROM especialistas es
                              JOIN negocio_config n ON n.id = es.negocio_id
                              WHERE es.usuario_id = ? AND es.negocio_id = ? AND es.estado = 'activo'");
$stmtMi->bind_param("ii", $_SESSION['usuario_id'], $negocio_id);
$stmtMi->execute();
$mi = $stmtMi->get_result()->fetch_assoc();
$stmtMi->close();

if (!$mi) {
	// ¿Tiene ficha en este local pero el administrador la dejó inactiva?
	$stmtIn = $conexion->prepare("SELECT id FROM especialistas WHERE usuario_id = ? AND negocio_id = ? AND estado = 'inactivo'");
	$stmtIn->bind_param("ii", $_SESSION['usuario_id'], $negocio_id);
	$stmtIn->execute();
	$inactivo = $stmtIn->get_result()->fetch_assoc();
	$stmtIn->close();
	header("Location: " . BASE_URL . "/notificaciones/?sin_acceso=" . ($inactivo ? "inactivo" : "1"));
	exit;
}

$ESTADOS_TURNO = array('Confirmado', 'Retrasado', 'Asistido', 'Finalizado', 'Cancelado');

function h($s) { return htmlspecialchars((string)$s); }
?>
