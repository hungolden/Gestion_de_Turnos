<?php
require("../auth_check.php");
header("Content-Type: text/html;charset=utf-8");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header("Location: ./"); exit; }

$uid    = (int)$_SESSION['usuario_id'];
$id     = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$accion = isset($_POST['accion']) ? $_POST['accion'] : '';

// La invitación tiene que ser de este usuario y seguir pendiente
$stmt = $conexion->prepare("SELECT id, negocio_id, especialista_id FROM notificaciones WHERE id = ? AND usuario_id = ? AND tipo = 'contratacion' AND estado = 'pendiente'");
$stmt->bind_param("ii", $id, $uid);
$stmt->execute();
$noti = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$noti || ($accion !== 'aceptar' && $accion !== 'rechazar')) {
	header("Location: ./?error=1");
	exit;
}

try {
	$conexion->begin_transaction();

	if ($accion === 'aceptar') {
		// Solo puede aceptar quien sigue siendo un usuario común, sin negocio
		$stmtU = $conexion->prepare("SELECT rol, negocio_id FROM usuarios WHERE id = ? FOR UPDATE");
		$stmtU->bind_param("i", $uid);
		$stmtU->execute();
		$u = $stmtU->get_result()->fetch_assoc();
		$stmtU->close();
		if ($u['rol'] !== 'usuario' || $u['negocio_id'] !== null) {
			$conexion->rollback();
			header("Location: ./?error=2");
			exit;
		}

		// Se activa su ficha de especialista y su cuenta pasa a ser del local
		$st = $conexion->prepare("UPDATE especialistas SET estado = 'activo', usuario_id = ? WHERE id = ? AND negocio_id = ? AND estado = 'pendiente'");
		$st->bind_param("iii", $uid, $noti['especialista_id'], $noti['negocio_id']);
		$st->execute();
		$activada = $st->affected_rows;
		$st->close();
		if ($activada !== 1) {
			$conexion->rollback();
			header("Location: ./?error=1");
			exit;
		}

		$st = $conexion->prepare("UPDATE usuarios SET rol = 'especialista', negocio_id = ? WHERE id = ?");
		$st->bind_param("ii", $noti['negocio_id'], $uid);
		$st->execute();
		$st->close();

		// Una cuenta pertenece a un solo local: las demás invitaciones pendientes quedan rechazadas
		$st = $conexion->prepare("UPDATE especialistas SET estado = 'rechazado' WHERE estado = 'pendiente' AND id IN (SELECT especialista_id FROM (SELECT especialista_id FROM notificaciones WHERE usuario_id = ? AND estado = 'pendiente' AND id <> ?) x)");
		$st->bind_param("ii", $uid, $id);
		$st->execute();
		$st->close();
		$st = $conexion->prepare("UPDATE notificaciones SET estado = 'rechazada', fecha_respuesta = NOW() WHERE usuario_id = ? AND estado = 'pendiente' AND id <> ?");
		$st->bind_param("ii", $uid, $id);
		$st->execute();
		$st->close();

		$st = $conexion->prepare("UPDATE notificaciones SET estado = 'aceptada', fecha_respuesta = NOW() WHERE id = ?");
		$st->bind_param("i", $id);
		$st->execute();
		$st->close();

		$conexion->commit();

		// Actualizamos la sesión para que entre directo a su pantalla de especialista
		$_SESSION['usuario_rol']        = 'especialista';
		$_SESSION['usuario_negocio_id'] = $noti['negocio_id'];
		header("Location: " . BASE_URL . "/especialista/");
		exit;
	}

	// Rechazar
	$st = $conexion->prepare("UPDATE especialistas SET estado = 'rechazado' WHERE id = ? AND estado = 'pendiente'");
	$st->bind_param("i", $noti['especialista_id']);
	$st->execute();
	$st->close();
	$st = $conexion->prepare("UPDATE notificaciones SET estado = 'rechazada', fecha_respuesta = NOW() WHERE id = ?");
	$st->bind_param("i", $id);
	$st->execute();
	$st->close();
	$conexion->commit();
	header("Location: ./?ok=rechazada");
	exit;

} catch (mysqli_sql_exception $e) {
	$conexion->rollback();
	die("Error al responder la invitación: " . $e->getMessage());
}
?>
