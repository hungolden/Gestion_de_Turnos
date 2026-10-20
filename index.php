<?php
require("auth_check.php");

// El cliente (rol 'usuario') y el especialista (que además puede usar el sistema
// como cliente; su panel de trabajo está aparte, en especialista/) tienen como pantalla principal la búsqueda de
// turnos disponibles, no este panel. "Registrar mi negocio" queda como
// una opción secundaria dentro de esa misma pantalla (ver menu_usuario.php).
if ($_SESSION['usuario_rol'] === 'usuario' || $_SESSION['usuario_rol'] === 'especialista') {
	header("Location: turnos_disponibles/");
	exit;
}

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Menu Principal</title>
<link rel="stylesheet" href="estilos.css">
</head>

<body>
<?php require __DIR__ . '/notificaciones_contador.php'; ?>
<header class="topbar">
<h1>Inicio</h1>
<p class="session-info">
Conectado como <strong><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></strong>
(<?php echo htmlspecialchars($_SESSION['usuario_rol']); ?>)
&nbsp;|&nbsp; <a href="logout.php">Cerrar sesión</a>
</p>
<?php if ($_SESSION['usuario_rol'] === 'admin') { ?>
<nav class="navmenu">
<ul>
<li><a href="especialidades/">Especialidades</a></li>
<li><a href="especialistas/">Especialistas</a></li>
<li><a href="servicios/">Servicios</a></li>
<li><a href="turnos/">Turnos</a></li>
<li><a href="usuarios/">Usuarios</a></li>
<li><a href="especialista_servicios/">Administrar Servicios</a></li>
<li><a href="historial_turnos/">Historial de Turnos</a></li>
<li><a href="negocio_config/">Mi Negocio</a></li>
<li style="margin-left:auto;"><a href="turnos_disponibles/">Buscar turno</a></li>
<li><a href="mis_turnos/">Mis turnos</a></li>
<li><a href="<?php echo BASE_URL; ?>/notificaciones/">Notificaciones<?php echo $pendientes ? ' (' . $pendientes . ')' : ''; ?></a></li>
</ul>
</nav>
<?php } ?>
</header>
<?php require __DIR__ . '/notificaciones_aviso.php'; ?>

<main class="container">
<?php if ($_SESSION['usuario_rol'] === 'admin') { ?>
	<p>Usá el menú de arriba para gestionar tu negocio.</p>
<?php } else { ?>
	<p>Sesión iniciada correctamente.</p>
<?php } ?>
</main>
</body>
</html>
