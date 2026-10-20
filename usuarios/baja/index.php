<?php
require("../../require_admin.php");
header("Content-Type: text/html;charset=utf-8");

$id = (int)$_GET['id'];

// Borrar la cuenta de alguien que es especialista dejaría su ficha huérfana y a la
// persona sin acceso. Para sacarlo, se lo deja "Inactivo". Tampoco se puede borrar la propia cuenta.
$stmtV = $conexion->prepare("SELECT COUNT(*) AS c FROM especialistas WHERE usuario_id = ?");
$stmtV->bind_param("i", $id);
$stmtV->execute();
$vinculos = (int)$stmtV->get_result()->fetch_assoc()['c'];
$stmtV->close();
if ($vinculos > 0 || $id === (int)$_SESSION['usuario_id']) {
	header("Location: ../?error=fk");
	exit;
}

try {
	$stmt = $conexion->prepare("DELETE FROM usuarios WHERE id = ? AND negocio_id = ?");
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
