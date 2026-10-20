<?php
require("../../require_admin.php");
header("Content-Type: text/html;charset=utf-8");

$negocio_id      = $_SESSION['usuario_negocio_id'];
$especialidad_id = isset($_POST['especialidad_id']) ? (int)$_POST['especialidad_id'] : 0;
$nombre          = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$email           = isset($_POST['email']) ? trim($_POST['email']) : '';

function volver($error, $nombre, $email) {
	header("Location: index.php?error=" . $error . "&nombre=" . urlencode($nombre) . "&email=" . urlencode($email));
	exit;
}

if ($especialidad_id === 0 || $nombre === '' || $email === '') {
	volver('datos', $nombre, $email);
}

// La especialidad tiene que ser de mi negocio
$stmt = $conexion->prepare("SELECT nombre FROM especialidades WHERE id = ? AND negocio_id = ?");
$stmt->bind_param("ii", $especialidad_id, $negocio_id);
$stmt->execute();
$especialidad = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$especialidad) { die("Especialidad no encontrada."); }

// 1) La persona tiene que estar registrada como usuario normal
$stmt = $conexion->prepare("SELECT id, rol, negocio_id, estado FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$usuario)                                                  { volver('noexiste', $nombre, $email); }
if ($usuario['rol'] !== 'usuario' || $usuario['negocio_id'] !== null) { volver('nodisponible', $nombre, $email); }
if ($usuario['estado'] !== 'activo')                            { volver('inactivo', $nombre, $email); }

// 2) No repetir una invitación que sigue sin responder
$stmt = $conexion->prepare("SELECT id, estado FROM especialistas WHERE usuario_id = ? AND negocio_id = ?");
$stmt->bind_param("ii", $usuario['id'], $negocio_id);
$stmt->execute();
$previa = $stmt->get_result()->fetch_assoc();
$stmt->close();
if ($previa && $previa['estado'] === 'pendiente') { volver('pendiente', $nombre, $email); }

$stmt = $conexion->prepare("SELECT nombre_local FROM negocio_config WHERE id = ?");
$stmt->bind_param("i", $negocio_id);
$stmt->execute();
$local = $stmt->get_result()->fetch_assoc();
$stmt->close();

$mensaje = "El local \"" . $local['nombre_local'] . "\" te quiere contratar como especialista (" . $especialidad['nombre'] . "). ¿Aceptás?";

try {
	$conexion->begin_transaction();

	if ($previa) {
		// Había rechazado antes: se reutiliza su ficha y se vuelve a invitar
		$especialista_id = $previa['id'];
		$stmt = $conexion->prepare("UPDATE especialistas SET estado = 'pendiente', nombre = ?, especialidad_id = ? WHERE id = ?");
		$stmt->bind_param("sii", $nombre, $especialidad_id, $especialista_id);
	} else {
		$stmt = $conexion->prepare("INSERT INTO especialistas (negocio_id, usuario_id, especialidad_id, nombre, estado) VALUES (?, ?, ?, ?, 'pendiente')");
		$stmt->bind_param("iiis", $negocio_id, $usuario['id'], $especialidad_id, $nombre);
	}
	$stmt->execute();
	if (!$previa) { $especialista_id = $conexion->insert_id; }
	$stmt->close();

	$stmt = $conexion->prepare("INSERT INTO notificaciones (usuario_id, negocio_id, especialista_id, tipo, mensaje) VALUES (?, ?, ?, 'contratacion', ?)");
	$stmt->bind_param("iiis", $usuario['id'], $negocio_id, $especialista_id, $mensaje);
	$stmt->execute();
	$stmt->close();

	$conexion->commit();
} catch (mysqli_sql_exception $e) {
	$conexion->rollback();
	die("Error al guardar: " . $e->getMessage());
}

header("Location: ../?invitado=1");
exit;
?>
