<?php
require("../../require_admin.php");
header("Content-Type: text/html;charset=utf-8");

$id           = $_POST['id'];
$nombre       = trim($_POST['nombre']);
$duracion_min = $_POST['duracion_min'];
$precio       = $_POST['precio'];
$estado       = $_POST['estado'];

// No se permite quedar con el mismo nombre que otro servicio del negocio
$stmtD = $conexion->prepare("SELECT id FROM servicios WHERE negocio_id = ? AND id <> ? AND LOWER(TRIM(nombre)) = LOWER(?)");
$stmtD->bind_param("iis", $_SESSION['usuario_negocio_id'], $id, $nombre);
$stmtD->execute();
$existe = $stmtD->get_result()->fetch_assoc();
$stmtD->close();
if ($existe) {
	header("Location: index.php?id=" . urlencode($id) . "&error=duplicado");
	exit;
}

$stmt = $conexion->prepare("UPDATE servicios SET nombre = ?, duracion_min = ?, precio = ?, estado = ? WHERE id = ? AND negocio_id = ?");
$stmt->bind_param("sidsii", $nombre, $duracion_min, $precio, $estado, $id, $_SESSION['usuario_negocio_id']);

if ($stmt->execute()) {
	header("Location: ../");
} else {
	die("Error al modificar: " . $stmt->error);
}
$stmt->close();
?>
