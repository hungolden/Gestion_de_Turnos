<?php
require("../require_especialista.php");
error_reporting(E_ALL ^ E_NOTICE);

// Servicios que este especialista realmente ofrece (especialista_servicios)
$stmtS = $conexion->prepare("SELECT s.id, s.nombre, s.duracion_min
                             FROM servicios s
                             JOIN especialista_servicios esv ON esv.servicio_id = s.id
                             WHERE esv.especialista_id = ? AND s.negocio_id = ? AND s.estado = 'activo'
                             ORDER BY s.nombre");
$stmtS->bind_param("ii", $mi['id'], $negocio_id);
$stmtS->execute();
$servicios = $stmtS->get_result()->fetch_all(MYSQLI_ASSOC);
$stmtS->close();

$error = '';
$servicio_id = isset($_POST['servicio_id']) ? (int)$_POST['servicio_id'] : 0;
$cliente     = isset($_POST['cliente_nombre']) ? trim($_POST['cliente_nombre']) : '';
$contacto    = isset($_POST['cliente_contacto']) ? trim($_POST['cliente_contacto']) : '';
$fecha       = isset($_POST['fecha']) ? $_POST['fecha'] : (isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d'));
$hora        = isset($_POST['hora_inicio']) ? $_POST['hora_inicio'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$duracion = 0;
	foreach ($servicios as $s) { if ($s['id'] == $servicio_id) { $duracion = (int)$s['duracion_min']; } }

	if ($duracion === 0) {
		$error = "Elegí un servicio de los que realizás.";
	} elseif ($cliente === '') {
		$error = "Ingresá el nombre del cliente.";
	} elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha) || $fecha < date('Y-m-d')) {
		$error = "La fecha no puede ser anterior a hoy.";
	} elseif (!preg_match('/^\d{2}:\d{2}/', $hora)) {
		$error = "Ingresá la hora de inicio.";
	} else {
		$hora_inicio = date("H:i:s", strtotime($hora));
		$hora_fin    = date("H:i:s", strtotime($hora_inicio) + $duracion * 60);

		if ($hora_inicio < $mi['apertura'] || $hora_fin <= $hora_inicio || $hora_fin > $mi['cierre']) {
			$error = "El turno tiene que entrar dentro del horario del local (" . substr($mi['apertura'], 0, 5) . " a " . substr($mi['cierre'], 0, 5) . ").";
		} else {
			// Mismo control que la reserva online: sin solapamientos para este especialista
			$stmtC = $conexion->prepare("SELECT id FROM turnos WHERE especialista_id = ? AND fecha = ? AND estado <> 'Cancelado' AND hora_inicio < ? AND hora_fin > ?");
			$stmtC->bind_param("isss", $mi['id'], $fecha, $hora_fin, $hora_inicio);
			$stmtC->execute();
			$ocupado = $stmtC->get_result()->fetch_assoc();
			$stmtC->close();

			if ($ocupado) {
				$error = "Ese horario se superpone con otro turno tuyo. Elegí otro.";
			} else {
				$stmt = $conexion->prepare("INSERT INTO turnos (negocio_id, especialista_id, servicio_id, cliente_nombre, cliente_contacto, fecha, hora_inicio, hora_fin, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Confirmado')");
				$stmt->bind_param("iiisssss", $negocio_id, $mi['id'], $servicio_id, $cliente, $contacto, $fecha, $hora_inicio, $hora_fin);
				$stmt->execute();
				$stmt->close();
				header("Location: ./?fecha=" . urlencode($fecha) . "&ok=2");
				exit;
			}
		}
	}
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Nuevo turno</title>
<link rel="stylesheet" href="../estilos.css">
<link rel="stylesheet" href="especialista.css">
</head>
<body>
<?php require("../menu_especialista.php"); ?>
<main class="container">
<h2 style="margin-top:0;">Registrar nuevo turno</h2>
<?php if ($error !== '') { ?><p class="aviso-err"><?php echo h($error); ?></p><?php } ?>

<?php if (empty($servicios)) { ?>
<p>Todavía no tenés servicios asignados, así que no podés registrar turnos. Pedile al administrador del local que te los asigne.</p>
<?php } else { ?>
<form action="nuevo.php" method="post">
<h3>Servicio</h3>
<select name="servicio_id" required>
	<option value="">-- Seleccione --</option>
	<?php foreach ($servicios as $s) { ?>
	<option value="<?php echo $s['id']; ?>" <?php echo ($s['id'] == $servicio_id) ? 'selected' : ''; ?>><?php echo h($s['nombre']); ?> (<?php echo (int)$s['duracion_min']; ?> min)</option>
	<?php } ?>
</select>
<h3>Cliente</h3>
<input type="text" name="cliente_nombre" value="<?php echo h($cliente); ?>" required>
<h3>Contacto del cliente</h3>
<input type="text" name="cliente_contacto" value="<?php echo h($contacto); ?>">
<h3>Fecha</h3>
<input type="date" name="fecha" value="<?php echo h($fecha); ?>" min="<?php echo date('Y-m-d'); ?>" required>
<h3>Hora de inicio</h3>
<input type="time" name="hora_inicio" value="<?php echo h($hora); ?>" required>
<p style="color:var(--text-muted);font-size:.9em;">La hora de fin se calcula sola según la duración del servicio. El turno se registra a tu nombre.</p>
<input type="submit" value="Guardar">
<button type="button" onclick="location.href='./'">Volver</button>
</form>
<?php } ?>
</main>
</body>
</html>
