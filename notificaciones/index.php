<?php
require("../auth_check.php");
error_reporting(E_ALL ^ E_NOTICE);
function h($s) { return htmlspecialchars((string)$s); }

$stmt = $conexion->prepare("SELECT n.id, n.mensaje, n.estado, n.fecha_creacion, nc.nombre_local
                            FROM notificaciones n
                            JOIN negocio_config nc ON nc.id = n.negocio_id
                            WHERE n.usuario_id = ?
                            ORDER BY (n.estado = 'pendiente') DESC, n.fecha_creacion DESC");
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$notis = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$rol = $_SESSION['usuario_rol'];
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Notificaciones</title>
<link rel="stylesheet" href="../estilos.css">
<link rel="stylesheet" href="../especialista/especialista.css">
</head>
<body>
<?php
if ($rol === 'especialista') { require("../menu_especialista.php"); }
elseif ($rol === 'admin')    { require("../menu_interior.php"); }
else                         { require("../menu_usuario.php"); }
?>
<main class="container">
<h2 style="margin-top:0;">Notificaciones</h2>

<?php if (isset($_GET['ok']) && $_GET['ok'] === 'rechazada') { ?><p class="aviso-ok">Rechazaste la invitación.</p><?php } ?>
<?php if (isset($_GET['error'])) { ?><p class="aviso-err"><?php echo ($_GET['error'] === '2') ? 'No podés aceptar: tu cuenta ya pertenece a un negocio.' : 'Esa invitación ya no está disponible.'; ?></p><?php } ?>
<?php if (isset($_GET['sin_acceso'])) { ?><p class="aviso-err"><?php echo ($_GET['sin_acceso'] === 'inactivo') ? 'El administrador del local dejó inactiva tu ficha de especialista. Cuando la reactive, vas a poder entrar de nuevo al panel.' : 'Todavía no tenés un local activo. Aceptá una invitación pendiente o hablá con el administrador del local.'; ?></p><?php } ?>

<?php foreach ($notis as $n) { ?>
<div style="border:1px solid var(--border);border-radius:8px;padding:1rem 1.25rem;margin-bottom:.75rem;">
	<strong><?php echo h($n['nombre_local']); ?></strong>
	<span style="color:var(--text-muted);font-size:.85rem;"> · <?php echo h($n['fecha_creacion']); ?></span>
	<p style="margin:.4rem 0 .75rem;"><?php echo h($n['mensaje']); ?></p>
	<?php if ($n['estado'] === 'pendiente') { ?>
	<form action="responder.php" method="post" style="display:flex;gap:.5rem;">
		<input type="hidden" name="id" value="<?php echo $n['id']; ?>">
		<button type="submit" name="accion" value="aceptar" style="background:var(--primary);color:#fff;border-color:var(--primary);">Aceptar</button>
		<button type="submit" name="accion" value="rechazar" onclick="return confirm('¿Rechazar esta invitación?');">Rechazar</button>
	</form>
	<?php } else { ?>
	<span class="estado <?php echo ($n['estado'] === 'aceptada') ? 'est-Asistido' : 'est-Cancelado'; ?>"><?php echo ($n['estado'] === 'aceptada') ? 'Aceptada' : 'Rechazada'; ?></span>
	<?php } ?>
</div>
<?php } ?>
<?php if (empty($notis)) { ?><p style="color:var(--text-muted);">No tenés notificaciones.</p><?php } ?>
</main>
</body>
</html>
