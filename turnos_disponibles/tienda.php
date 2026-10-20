<?php
require("../auth_check.php");
error_reporting(E_ALL ^ E_NOTICE);

$negocio_id = isset($_GET['id']) ? $_GET['id'] : '';
$servicio_id = isset($_GET['servicio_id']) && $_GET['servicio_id'] !== '' ? $_GET['servicio_id'] : null;
$fecha = isset($_GET['fecha']) && $_GET['fecha'] !== '' ? $_GET['fecha'] : date('Y-m-d');

$stmt = $conexion->prepare("SELECT * FROM negocio_config WHERE id = ?");
$stmt->bind_param("i", $negocio_id);
$stmt->execute();
$negocio = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$negocio) {
	die("Negocio no encontrado.");
}

// Servicios activos que este negocio realmente ofrece (tienen al menos
// un especialista activo asignado)
$stmt = $conexion->prepare("SELECT DISTINCT sv.id, sv.nombre, sv.duracion_min, sv.precio
                            FROM especialista_servicios esv
                            JOIN especialistas es ON es.id = esv.especialista_id AND es.estado = 'activo'
                            JOIN servicios sv ON sv.id = esv.servicio_id AND sv.estado = 'activo'
                            WHERE es.negocio_id = ?
                            ORDER BY sv.nombre");
$stmt->bind_param("i", $negocio_id);
$stmt->execute();
$servicios = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Especialidades que realiza la tienda (las de sus especialistas activos),
// para que el cliente vea de un vistazo qué ofrece cada local.
$stmt = $conexion->prepare("SELECT DISTINCT esp.nombre
                            FROM especialistas es
                            JOIN especialidades esp ON esp.id = es.especialidad_id
                            WHERE es.negocio_id = ? AND es.estado = 'activo'
                            ORDER BY esp.nombre");
$stmt->bind_param("i", $negocio_id);
$stmt->execute();
$especialidades = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$servicio_elegido = null;
foreach ($servicios as $s) {
	if ($s['id'] == $servicio_id) {
		$servicio_elegido = $s;
		break;
	}
}

// Especialistas de este negocio que ofrecen el servicio elegido
$especialistas = array();
if ($servicio_elegido) {
	$stmt = $conexion->prepare("SELECT es.id, es.nombre
                                FROM especialista_servicios esv
                                JOIN especialistas es ON es.id = esv.especialista_id AND es.estado = 'activo'
                                WHERE esv.servicio_id = ? AND es.negocio_id = ?
                                ORDER BY es.nombre");
	$stmt->bind_param("ii", $servicio_id, $negocio_id);
	$stmt->execute();
	$especialistas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
	$stmt->close();
}

// Misma lógica de siempre para calcular horarios libres de un especialista
function turnos_disponibles_slots($conexion, $especialista_id, $apertura, $cierre, $duracion_min, $fecha) {
	$inicio_jornada = strtotime("$fecha $apertura");
	$fin_jornada    = strtotime("$fecha $cierre");
	$paso           = $duracion_min * 60;
	$ahora          = time();

	$stmt = $conexion->prepare("SELECT hora_inicio, hora_fin FROM turnos WHERE especialista_id = ? AND fecha = ? AND estado <> 'Cancelado'");
	$stmt->bind_param("is", $especialista_id, $fecha);
	$stmt->execute();
	$ocupados = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
	$stmt->close();

	$intervalos_ocupados = array();
	foreach ($ocupados as $o) {
		$intervalos_ocupados[] = array(
			strtotime("$fecha " . $o['hora_inicio']),
			strtotime("$fecha " . $o['hora_fin']),
		);
	}

	$slots = array();
	for ($t = $inicio_jornada; $t + $paso <= $fin_jornada; $t += $paso) {
		$slot_inicio = $t;
		$slot_fin    = $t + $paso;

		if ($slot_inicio <= $ahora) {
			continue;
		}

		$libre = true;
		foreach ($intervalos_ocupados as $intervalo) {
			if ($slot_inicio < $intervalo[1] && $slot_fin > $intervalo[0]) {
				$libre = false;
				break;
			}
		}

		if ($libre) {
			$slots[] = date("H:i", $slot_inicio);
		}
	}
	return $slots;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo htmlspecialchars($negocio['nombre_local']); ?></title>
<link rel="stylesheet" href="../estilos.css">
</head>
<body>
<?php require("../menu_usuario.php"); ?>
<main class="container">
<p><a href="./">&larr; Volver a la búsqueda</a></p>
<h1 style="text-align:center;margin:1rem 0 .2rem;font-size:2rem;color:var(--text);"><?php echo htmlspecialchars($negocio['nombre_local']); ?></h1>
<p style="margin:.2rem 0 1rem;color:#666;text-align:center;">
	<?php echo htmlspecialchars($negocio['rubro']); ?>
	<?php if (!empty($negocio['contacto'])) { ?> &nbsp;|&nbsp; <?php echo htmlspecialchars($negocio['contacto']); ?><?php } ?>
</p>

<h3 style="text-align:center;margin-bottom:.5rem;">Especialidades</h3>
<?php if (empty($especialidades)) { ?>
<p style="text-align:center;color:#666;">Esta tienda todavía no tiene especialidades cargadas.</p>
<?php } else { ?>
<div style="display:flex;flex-wrap:wrap;justify-content:center;gap:.4rem;margin-bottom:1.5rem;">
<?php foreach ($especialidades as $esp) { ?>
	<span style="display:inline-block;padding:.25rem .8rem;border-radius:999px;background:#e3edf7;color:#2c4a63;font-size:.85rem;font-weight:600;"><?php echo htmlspecialchars($esp['nombre']); ?></span>
<?php } ?>
</div>
<?php } ?>
<hr>

<h3>Servicio</h3>
<?php if (empty($servicios)) { ?>
<p>Este negocio todavía no tiene servicios disponibles.</p>
<?php } else { ?>
<div style="display:flex;flex-wrap:wrap;gap:.4rem;margin-bottom:1.25rem;">
<?php foreach ($servicios as $s) {
	$activo = ($servicio_elegido && $s['id'] == $servicio_elegido['id']);
	$estilo = $activo ? 'background:var(--primary);color:#fff;border-color:var(--primary);' : '';
?>
	<button type="button" style="<?php echo $estilo; ?>" onclick="location.href='tienda.php?id=<?php echo $negocio_id; ?>&servicio_id=<?php echo $s['id']; ?>&fecha=<?php echo htmlspecialchars($fecha); ?>'">
		<?php echo htmlspecialchars($s['nombre']); ?> ($<?php echo htmlspecialchars($s['precio']); ?>)
	</button>
<?php } ?>
</div>
<?php } ?>

<?php if ($servicio_elegido) { ?>
<form action="tienda.php" method="get">
<input type="hidden" name="id" value="<?php echo $negocio_id; ?>">
<input type="hidden" name="servicio_id" value="<?php echo $servicio_elegido['id']; ?>">
<h3>Fecha</h3>
<input type="date" name="fecha" value="<?php echo htmlspecialchars($fecha); ?>" min="<?php echo date('Y-m-d'); ?>" onchange="this.form.submit()"><br>
</form>

<hr>

<?php if (isset($_GET['error']) && $_GET['error'] === '1') { ?>
<p style="color:#a8433c;">Ese horario se acaba de ocupar. Elegí otro.</p>
<?php } ?>

<h3>Horarios disponibles — <?php echo htmlspecialchars($servicio_elegido['nombre']); ?> (<?php echo $servicio_elegido['duracion_min']; ?> min)</h3>

<?php if (empty($especialistas)) { ?>
<p>No hay especialistas disponibles para este servicio.</p>
<?php } ?>

<?php foreach ($especialistas as $e) {
	$slots = turnos_disponibles_slots($conexion, $e['id'], $negocio['apertura'], $negocio['cierre'], $servicio_elegido['duracion_min'], $fecha);
?>
<div style="border:1px solid #dde1e6;border-radius:8px;padding:1rem 1.25rem;margin-bottom:1rem;">
	<h3 style="margin-top:0;text-transform:none;font-size:1rem;color:#2b303b;letter-spacing:0;">
		Con <?php echo htmlspecialchars($e['nombre']); ?>
	</h3>
	<?php if (empty($slots)) { ?>
	<p style="color:#a8433c;">Sin horarios disponibles para esa fecha.</p>
	<?php } else { ?>
	<div>
	<?php foreach ($slots as $hora) { ?>
		<button type="button" onclick="if(confirm('¿Reservar el turno de las <?php echo $hora; ?>?')) location.href='reservar.php?especialista_id=<?php echo $e['id']; ?>&servicio_id=<?php echo $servicio_elegido['id']; ?>&fecha=<?php echo $fecha; ?>&hora=<?php echo $hora; ?>'"><?php echo $hora; ?></button>
	<?php } ?>
	</div>
	<?php } ?>
</div>
<?php } ?>

<?php } else { ?>
<p style="color:#666;">Elegí un servicio arriba para ver los horarios disponibles.</p>
<?php } ?>
</main>
</body>
</html>
