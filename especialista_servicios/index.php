<?php
require("../require_admin.php");
error_reporting(E_ALL ^ E_NOTICE);

$buscar     = isset($_POST['buscar']) ? trim($_POST['buscar']) : '';
$like       = '%' . $buscar . '%';
$negocio_id = $_SESSION['usuario_negocio_id'];
$sel        = isset($_GET['sel']) ? (int)$_GET['sel'] : 0; // especialista a dejar marcado (al volver de "Editar")

// Lista izquierda: especialistas activos de mi negocio (con su especialidad base)
$stmt = $conexion->prepare("SELECT e.id, e.nombre, esp.nombre AS especialidad
                            FROM especialistas e
                            LEFT JOIN especialidades esp ON esp.id = e.especialidad_id
                            WHERE e.negocio_id = ? AND e.estado = 'activo' AND e.nombre LIKE ?
                            ORDER BY e.nombre");
$stmt->bind_param("is", $negocio_id, $like);
$stmt->execute();
$especialistas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Servicios asignados a cada uno, para el panel informativo de la derecha
$stmt = $conexion->prepare("SELECT esv.especialista_id, s.nombre, s.duracion_min, s.precio
                            FROM especialista_servicios esv
                            JOIN servicios s ON s.id = esv.servicio_id
                            JOIN especialistas e ON e.id = esv.especialista_id
                            WHERE e.negocio_id = ?
                            ORDER BY s.nombre");
$stmt->bind_param("i", $negocio_id);
$stmt->execute();
$filas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$servicios_de = array();
foreach ($filas as $f) {
	$servicios_de[$f['especialista_id']][] = $f;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Administrar Servicios</title>
<link rel="stylesheet" href="../estilos.css">
<style>
.dividido { display: flex; gap: 1.5rem; align-items: flex-start; }
.dividido .mitad { flex: 1 1 0; min-width: 0; }
.barra { display: flex; gap: .75rem; align-items: center; }
.barra form { flex: 1; }
table.table tr.fila { cursor: pointer; }
table.table tr.fila.sel td { background: #e4ebf3; }
.ficha { border: 1px solid var(--border); border-radius: 8px; padding: 1.25rem 1.5rem; background: #f8f9fb; position: sticky; top: 1rem; }
.ficha h2 { margin-top: 0; }
.ficha .dato { color: var(--text-muted); font-size: .9rem; margin: 0 0 1rem; }
.ficha ul { list-style: none; margin: 0; padding: 0; }
.ficha li { display: flex; justify-content: space-between; gap: 1rem; padding: .5rem 0; border-bottom: 1px solid var(--border); }
.ficha li:last-child { border-bottom: none; }
.ficha li span { color: var(--text-muted); white-space: nowrap; }
.vacio { color: var(--text-muted); }
@media (max-width: 800px) { .dividido { flex-direction: column; } .dividido .mitad { width: 100%; } }
</style>
</head>
<body>
<?php require("../menu_interior.php"); ?>
<main class="container">
<div class="dividido">

	<!-- MITAD IZQUIERDA: lista simple -->
	<div class="mitad">
		<div class="barra">
			<form action="./" method="post">
				<input style="width:100%;max-width:none;" type="text" name="buscar" value="<?php echo htmlspecialchars($buscar); ?>" placeholder="Buscar por especialista">
			</form>
		</div>
		<table class="table table-striped table-hover">
		<tr><th>#</th><th>Especialista</th><th>Acciones</th></tr>
		<?php $i = 1; foreach ($especialistas as $e) { ?>
		<tr class="fila<?php echo ($e['id'] == $sel) ? ' sel' : ''; ?>" data-id="<?php echo $e['id']; ?>">
			<td><?php echo $i; ?></td>
			<td><?php echo htmlspecialchars($e['nombre']); ?></td>
			<td><button type="button" onclick="event.stopPropagation(); location.href='modificacion/?especialista_id=<?php echo $e['id']; ?>'">Editar</button></td>
		</tr>
		<?php $i++; } ?>
		<?php if (empty($especialistas)) { ?>
		<tr><td colspan="3">No hay especialistas activos que coincidan con la búsqueda.</td></tr>
		<?php } ?>
		</table>
	</div>

	<!-- MITAD DERECHA: ficha informativa (solo lectura) -->
	<div class="mitad">
		<div class="ficha">
			<div id="ficha-vacia" class="vacio"<?php echo $sel ? ' hidden' : ''; ?>>Hacé clic en un especialista para ver su información.</div>
			<?php foreach ($especialistas as $e) { ?>
			<div class="info" id="info-<?php echo $e['id']; ?>"<?php echo ($e['id'] == $sel) ? '' : ' hidden'; ?>>
				<h2><?php echo htmlspecialchars($e['nombre']); ?></h2>
				<p class="dato">Especialidad: <?php echo htmlspecialchars($e['especialidad'] ?? 'Sin especialidad'); ?></p>
				<h3 style="margin-top:0;">Servicios que realiza</h3>
				<?php if (empty($servicios_de[$e['id']])) { ?>
					<p class="vacio">Sin servicios asignados.</p>
				<?php } else { ?>
				<ul>
					<?php foreach ($servicios_de[$e['id']] as $s) { ?>
					<li><?php echo htmlspecialchars($s['nombre']); ?> <span><?php echo (int)$s['duracion_min']; ?> min · $<?php echo htmlspecialchars($s['precio']); ?></span></li>
					<?php } ?>
				</ul>
				<?php } ?>
			</div>
			<?php } ?>
		</div>
	</div>

</div>
</main>
<script>
// Al hacer clic en una fila se marca y se muestra su ficha. No se edita nada desde acá.
var filas = document.querySelectorAll('tr.fila');
filas.forEach(function (fila) {
	fila.addEventListener('click', function () {
		filas.forEach(function (f) { f.classList.remove('sel'); });
		fila.classList.add('sel');
		document.querySelectorAll('.info').forEach(function (d) { d.hidden = true; });
		document.getElementById('ficha-vacia').hidden = true;
		document.getElementById('info-' + fila.dataset.id).hidden = false;
	});
});
</script>
</body>
</html>
