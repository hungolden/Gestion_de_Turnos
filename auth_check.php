<?php
// Este archivo se incluye al principio de toda página que requiera estar
// logueado. Arranca sesión (si no está iniciada) y, si no hay un usuario
// autenticado, corta la ejecución y redirige al login.
require_once __DIR__ . "/conexion.php";

if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

if (!isset($_SESSION['usuario_id'])) {
	header("Location: " . BASE_URL . "/login/");
	exit;
}

// La sesión no alcanza: se confirma contra la base en cada pantalla que la cuenta siga
// existiendo y activa, y se refrescan rol y negocio (por si el admin los cambió, o si
// la persona aceptó/dejó un local). Así nadie sigue "adentro" con una cuenta borrada
// o desactivada, ni ve un rol viejo.
$stmtSes = $conexion->prepare("SELECT nombre, email, rol, estado, negocio_id FROM usuarios WHERE id = ?");
$stmtSes->bind_param("i", $_SESSION['usuario_id']);
$stmtSes->execute();
$filaSes = $stmtSes->get_result()->fetch_assoc();
$stmtSes->close();

if (!$filaSes || $filaSes['estado'] !== 'activo') {
	$inactiva = ($filaSes && $filaSes['estado'] !== 'activo');
	$_SESSION = array();
	session_destroy();
	header("Location: " . BASE_URL . "/login/" . ($inactiva ? "?error=2" : ""));
	exit;
}

$_SESSION['usuario_nombre']     = $filaSes['nombre'];
$_SESSION['usuario_email']      = $filaSes['email'];
$_SESSION['usuario_rol']        = $filaSes['rol'];
$_SESSION['usuario_negocio_id'] = $filaSes['negocio_id'];
?>
