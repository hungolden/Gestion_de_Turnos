<?php
// Cuenta las notificaciones sin responder de la persona logueada (sea cual sea su rol).
// Deja el resultado en $pendientes. Si la tabla todavía no existe, queda en 0.
$pendientes = 0;
try {
	$stmtN = $conexion->prepare("SELECT COUNT(*) AS c FROM notificaciones WHERE usuario_id = ? AND estado = 'pendiente'");
	$stmtN->bind_param("i", $_SESSION['usuario_id']);
	$stmtN->execute();
	$pendientes = (int)$stmtN->get_result()->fetch_assoc()['c'];
	$stmtN->close();
} catch (Throwable $e) { $pendientes = 0; }
