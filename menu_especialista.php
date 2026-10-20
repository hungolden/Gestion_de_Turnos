<?php require __DIR__ . '/notificaciones_contador.php'; ?>
<header class="topbar">
<h1><a href="<?php echo BASE_URL; ?>/">Inicio</a></h1>
<p class="session-info">
Conectado como <strong><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></strong>
(especialista)
&nbsp;|&nbsp; <a href="<?php echo BASE_URL; ?>/logout.php">Cerrar sesión</a>
</p>
<nav class="navmenu">
<ul>
<li><a href="<?php echo BASE_URL; ?>/especialista/"><strong>Panel de especialista:</strong> Mi agenda</a></li>
<li><a href="<?php echo BASE_URL; ?>/especialista/agenda.php">Agenda del local</a></li>
<li><a href="<?php echo BASE_URL; ?>/especialista/nuevo.php">Nuevo turno</a></li>
<li><a href="<?php echo BASE_URL; ?>/notificaciones/">Notificaciones<?php echo $pendientes ? ' (' . $pendientes . ')' : ''; ?></a></li>
<li style="margin-left:auto;"><a href="<?php echo BASE_URL; ?>/turnos_disponibles/">Buscar turno</a></li>
<li><a href="<?php echo BASE_URL; ?>/mis_turnos/">Mis turnos</a></li>
</ul>
</nav>
</header>
<?php require __DIR__ . '/notificaciones_aviso.php'; ?>
