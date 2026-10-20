<?php
require("../../require_admin.php");
header("Content-Type: text/html;charset=utf-8");

$id = $_GET['id'];

try {
	$stmt = $conexion->prepare("DELETE FROM especialistas WHERE id = ? AND negocio_id = ?");
	$stmt->bind_param("ii", $id, $_SESSION['usuario_negocio_id']);
	$stmt->execute();
	$stmt->close();
	header("Location: ../");
} catch (mysqli_sql_exception $e) {
	if ($e->getCode() == 1451) {
		header("Location: ../?error=fk");
	} else {
		die("Error al eliminar: " . $e->getMessage());
	}
}
exit;
?>
