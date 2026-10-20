<?php
require("../../require_admin.php");
header("Content-Type: text/html;charset=utf-8");

$negocio_id      = $_SESSION['usuario_negocio_id'];
$especialista_id = isset($_POST['especialista_id']) ? (int)$_POST['especialista_id'] : 0;
$marcados        = isset($_POST['servicio_id']) ? array_map('intval', $_POST['servicio_id']) : array();

// El especialista tiene que ser de mi negocio
$stmt = $conexion->prepare("SELECT id FROM especialistas WHERE id = ? AND negocio_id = ?");
$stmt->bind_param("ii", $especialista_id, $negocio_id);
$stmt->execute();
if (!$stmt->get_result()->fetch_assoc()) {
	die("Especialista no encontrado.");
}
$stmt->close();

// Solo se consideran los servicios activos de mi negocio (los mismos que se muestran en la pantalla);
// cualquier otro id que llegue en el POST se ignora.
$stmt = $conexion->prepare("SELECT id FROM servicios WHERE negocio_id = ? AND estado = 'activo'");
$stmt->bind_param("i", $negocio_id);
$stmt->execute();
$validos = array_column($stmt->get_result()->fetch_all(MYSQLI_ASSOC), 'id');
$stmt->close();

// Marcado -> se asigna; desmarcado -> se quita. Todo junto o nada.
$conexion->begin_transaction();
$ins = $conexion->prepare("INSERT IGNORE INTO especialista_servicios (especialista_id, servicio_id) VALUES (?, ?)");
$del = $conexion->prepare("DELETE FROM especialista_servicios WHERE especialista_id = ? AND servicio_id = ?");
foreach ($validos as $servicio_id) {
	if (in_array((int)$servicio_id, $marcados, true)) {
		$ins->bind_param("ii", $especialista_id, $servicio_id);
		$ins->execute();
	} else {
		$del->bind_param("ii", $especialista_id, $servicio_id);
		$del->execute();
	}
}
$ins->close();
$del->close();
$conexion->commit();

header("Location: ../?sel=" . $especialista_id);
exit;
?>
