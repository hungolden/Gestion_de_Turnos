<?php
// Se incluye en vez de auth_check.php en toda página del panel de gestión
// (especialidades, especialistas, servicios, turnos, usuarios, etc.).
// Además de exigir que haya una sesión iniciada, exige que el rol sea 'admin'.
require_once __DIR__ . "/auth_check.php";

if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'admin') {
	header("Location: " . BASE_URL . "/");
	exit;
}
?>
