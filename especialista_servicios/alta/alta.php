<?php
require("../../require_admin.php");
header("Content-Type: text/html;charset=utf-8");

$negocio_id      = $_SESSION['usuario_negocio_id'];
$especialista_id = $_POST['especialista_id'];
$servicios_ids   = isset($_POST['servicio_id']) ? $_POST['servicio_id'] : [];

if (empty($servicios_ids)) {
	die("Debe seleccionar al menos un servicio.");
}

// Verificamos que el especialista elegido sea de mi propio negocio
$stmtCheck = $conexion->prepare("SELECT id FROM especialistas WHERE id = ? AND negocio_id = ?");
$stmtCheck->bind_param("ii", $especialista_id, $negocio_id);
$stmtCheck->execute();
if (!$stmtCheck->get_result()->fetch_assoc()) {
	die("Especialista no encontrado.");
}
$stmtCheck->close();

// INSERT IGNORE evita error si la combinación especialista/servicio ya existía
$stmt = $conexion->prepare("INSERT IGNORE INTO especialista_servicios (especialista_id, servicio_id) VALUES (?, ?)");
foreach ($servicios_ids as $servicio_id) {
	$stmt->bind_param("ii", $especialista_id, $servicio_id);
	$stmt->execute();
}
$stmt->close();

header("Location: ../");
?>
