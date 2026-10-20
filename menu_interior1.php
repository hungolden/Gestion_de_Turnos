<?php require __DIR__ . '/notificaciones_contador.php'; ?>
<header class="topbar">
<h1><a href="../../">Inicio</a></h1>
<p class="session-info">
Conectado como <strong><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></strong>
(<?php echo htmlspecialchars($_SESSION['usuario_rol']); ?>)
&nbsp;|&nbsp; <a href="../../logout.php">Cerrar sesión</a>
</p>
<nav class="navmenu">
<ul>
<li><a href="../../especialidades/">Especialidades</a></li>
<li><a href="../../especialistas/">Especialistas</a></li>
<li><a href="../../servicios/">Servicios</a></li>
<li><a href="../../turnos/">Turnos</a></li>
<li><a href="../../usuarios/">Usuarios</a></li>
<li><a href="../../especialista_servicios/">Administrar Servicios</a></li>
<li><a href="../../historial_turnos/">Historial de Turnos</a></li>
<li><a href="../../negocio_config/">Mi Negocio</a></li>
<li style="margin-left:auto;"><a href="../../turnos_disponibles/">Buscar turno</a></li>
<li><a href="../../mis_turnos/">Mis turnos</a></li>
<li><a href="<?php echo BASE_URL; ?>/notificaciones/">Notificaciones<?php echo $pendientes ? ' (' . $pendientes . ')' : ''; ?></a></li>
</ul>
</nav>
</header>
<?php require __DIR__ . '/notificaciones_aviso.php'; ?>
