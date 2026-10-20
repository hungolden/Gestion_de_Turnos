<?php
require("../require_admin.php");
header("Content-Type: text/html;charset=utf-8");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header("Location: ./");
	exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

// Alterna activo <-> inactivo, solo sobre servicios de mi negocio
$stmt = $conexion->prepare("UPDATE servicios SET estado = IF(estado = 'activo', 'inactivo', 'activo') WHERE id = ? AND negocio_id = ?");
$stmt->bind_param("ii", $id, $_SESSION['usuario_negocio_id']);
$stmt->execute();
$stmt->close();

header("Location: ./?ok=estado");
exit;
?>
