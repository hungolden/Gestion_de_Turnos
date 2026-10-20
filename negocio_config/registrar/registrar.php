<?php
require("../../auth_check.php");
header("Content-Type: text/html;charset=utf-8");

// Doble chequeo por seguridad, igual que en index.php
if ($_SESSION['usuario_rol'] !== 'usuario' || !empty($_SESSION['usuario_negocio_id'])) {
	header("Location: " . BASE_URL . "/");
	exit;
}

$nombre_local   = isset($_POST['nombre_local']) ? trim($_POST['nombre_local']) : '';
$rubro          = isset($_POST['rubro']) ? trim($_POST['rubro']) : '';
$contacto       = isset($_POST['contacto']) ? trim($_POST['contacto']) : '';
$apertura       = isset($_POST['apertura']) ? $_POST['apertura'] : '';
$cierre         = isset($_POST['cierre']) ? $_POST['cierre'] : '';
$cancelacion_hs = isset($_POST['cancelacion_hs']) ? $_POST['cancelacion_hs'] : 2;

if ($nombre_local === '' || $rubro === '' || $apertura === '' || $cierre === '') {
	die("Faltan datos obligatorios.");
}

$stmtNeg = $conexion->prepare("INSERT INTO negocio_config (nombre_local, rubro, contacto, apertura, cierre, cancelacion_hs) VALUES (?, ?, ?, ?, ?, ?)");
$stmtNeg->bind_param("sssssi", $nombre_local, $rubro, $contacto, $apertura, $cierre, $cancelacion_hs);

if (!$stmtNeg->execute()) {
	die("Error al crear el negocio: " . $stmtNeg->error);
}
$negocio_id = $conexion->insert_id;
$stmtNeg->close();

// El usuario que registra el negocio pasa a ser su administrador
$stmtUser = $conexion->prepare("UPDATE usuarios SET rol = 'admin', negocio_id = ? WHERE id = ?");
$stmtUser->bind_param("ii", $negocio_id, $_SESSION['usuario_id']);
$stmtUser->execute();
$stmtUser->close();

// Actualizamos la sesión actual para que el cambio de rol se vea sin volver a loguearse
$_SESSION['usuario_rol']        = 'admin';
$_SESSION['usuario_negocio_id'] = $negocio_id;

header("Location: " . BASE_URL . "/");
exit;
?>
