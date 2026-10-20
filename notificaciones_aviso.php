<?php
// Cartel que aparece debajo del menú, en todas las pantallas y para todos los roles,
// mientras haya notificaciones sin responder. Necesita $pendientes (ver notificaciones_contador.php).
if (!empty($pendientes) && strpos($_SERVER['SCRIPT_NAME'], '/notificaciones/') === false) { ?>
<div style="background:#fdf0d5;color:#8a5a00;padding:.6rem 2rem;border-bottom:1px solid #ecd9a8;">
Tenés <strong><?php echo (int)$pendientes; ?></strong> notificación<?php echo $pendientes == 1 ? '' : 'es'; ?> sin responder.
<a href="<?php echo BASE_URL; ?>/notificaciones/"><strong>Ver ahora</strong></a>
</div>
<?php } ?>
