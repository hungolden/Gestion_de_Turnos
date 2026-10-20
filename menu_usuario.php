<?php require __DIR__ . '/notificaciones_contador.php'; ?>
<header class="topbar">
<h1><a href="../">Inicio</a></h1>
<p class="session-info">
Conectado como <strong><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></strong>
&nbsp;|&nbsp; <a href="../logout.php">Cerrar sesión</a>
</p>
<nav class="navmenu">
<ul>
<li><a href="../turnos_disponibles/">Buscar turno</a></li>
<li><a href="../mis_turnos/">Mis turnos</a></li>
<li><a href="<?php echo BASE_URL; ?>/notificaciones/">Notificaciones<?php echo $pendientes ? ' (' . $pendientes . ')' : ''; ?></a></li>
<?php if ($_SESSION['usuario_rol'] === 'especialista') { ?>
<li style="margin-left:auto;"><a href="../especialista/"><strong>Panel de especialista</strong></a></li>
<?php } elseif (empty($_SESSION['usuario_negocio_id'])) { ?>
<li><a href="../negocio_config/registrar/">Registrar mi negocio</a></li>
<?php } else { ?>
<li style="margin-left:auto;"><a href="../">Gestionar mi negocio</a></li>
<?php } ?>
</ul>
</nav>
</header>
<?php require __DIR__ . '/notificaciones_aviso.php'; ?>
