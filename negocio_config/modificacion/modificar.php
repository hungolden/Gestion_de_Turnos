<?php
require("../../require_admin.php");
header("Content-Type: text/html;charset=utf-8");

// Se ignora cualquier id que pudiera venir por POST: siempre se edita
// el negocio de la sesión actual, nunca uno elegido desde afuera.
$id = $_SESSION['usuario_negocio_id'];
if (empty($id)) {
	header("Location: " . BASE_URL . "/");
	exit;
}

$nombre_local   = $_POST['nombre_local'];
$rubro          = $_POST['rubro'];
$contacto       = $_POST['contacto'];
$apertura       = $_POST['apertura'];
$cierre         = $_POST['cierre'];
$cancelacion_hs = $_POST['cancelacion_hs'];

$stmt = $conexion->prepare("UPDATE negocio_config SET nombre_local = ?, rubro = ?, contacto = ?, apertura = ?, cierre = ?, cancelacion_hs = ? WHERE id = ?");
$stmt->bind_param("sssssii", $nombre_local, $rubro, $contacto, $apertura, $cierre, $cancelacion_hs, $id);

if ($stmt->execute()) {
	header("Location: ../");
} else {
	die("Error al modificar: " . $stmt->error);
}
$stmt->close();
?>
