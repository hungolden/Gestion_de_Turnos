<?php
require("../../require_admin.php");
header("Content-Type: text/html;charset=utf-8");

$negocio_id   = $_SESSION['usuario_negocio_id'];
$nombre       = trim($_POST['nombre']);
$duracion_min = $_POST['duracion_min'];
$precio       = $_POST['precio'];
$estado       = $_POST['estado'];

// No se permiten dos servicios con el mismo nombre en el mismo negocio
$stmtD = $conexion->prepare("SELECT id FROM servicios WHERE negocio_id = ? AND LOWER(TRIM(nombre)) = LOWER(?)");
$stmtD->bind_param("is", $negocio_id, $nombre);
$stmtD->execute();
$existe = $stmtD->get_result()->fetch_assoc();
$stmtD->close();
if ($existe) {
	header("Location: index.php?error=duplicado&nombre=" . urlencode($nombre) . "&duracion_min=" . urlencode($duracion_min) . "&precio=" . urlencode($precio) . "&estado=" . urlencode($estado));
	exit;
}

$stmt = $conexion->prepare("INSERT INTO servicios (negocio_id, nombre, duracion_min, precio, estado) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("isids", $negocio_id, $nombre, $duracion_min, $precio, $estado);

if ($stmt->execute()) {
	header("Location: ../");
} else {
	die("Error al guardar: " . $stmt->error);
}
$stmt->close();
?>
