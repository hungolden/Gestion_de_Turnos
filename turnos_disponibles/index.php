<?php
require("../auth_check.php");
error_reporting(E_ALL ^ E_NOTICE);

$buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$like   = '%' . $buscar . '%';

// Un resultado por negocio (no por cada servicio/especialista que ofrezca),
// solo negocios que tengan al menos un especialista activo con un servicio
// activo. La búsqueda se hace por el rol (rubro) de la tienda.
$sql = "SELECT DISTINCT n.id AS negocio_id, n.nombre_local, n.rubro, n.contacto
        FROM especialista_servicios esv
        JOIN especialistas es ON es.id = esv.especialista_id AND es.estado = 'activo'
        JOIN servicios sv ON sv.id = esv.servicio_id AND sv.estado = 'activo'
        JOIN negocio_config n ON n.id = es.negocio_id
        WHERE n.rubro LIKE ?
        ORDER BY n.nombre_local";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $like);
$stmt->execute();
$negocios = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Buscar turno</title>
<link rel="stylesheet" href="../estilos.css">
</head>
<body>
<?php require("../menu_usuario.php"); ?>
<main class="container">
<h2>Buscar turno disponible</h2>

<form action="./" method="get">
<h3>Rol de la tienda</h3>
<input type="text" name="buscar" value="<?php echo htmlspecialchars($buscar); ?>" placeholder="Ej: peluquería, manicura, barbería..."><br>
<br>
<input type="submit" value="Buscar">
</form>

<hr>

<?php if (empty($negocios)) { ?>
<p>No se encontraron tiendas con ese rol.</p>
<?php } ?>

<?php foreach ($negocios as $n) { ?>
<div style="border:1px solid #dde1e6;border-radius:8px;padding:1rem 1.25rem;margin-bottom:1rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
	<div>
		<h3 style="margin:0;text-transform:none;font-size:1.05rem;color:#2b303b;letter-spacing:0;">
			<?php echo htmlspecialchars($n['nombre_local']); ?>
		</h3>
		<p style="margin:.2rem 0 0;color:#666;font-size:.9rem;">
			<?php echo htmlspecialchars($n['rubro']); ?>
			<?php if (!empty($n['contacto'])) { ?> &nbsp;|&nbsp; <?php echo htmlspecialchars($n['contacto']); ?><?php } ?>
		</p>
	</div>
	<button type="button" onclick="location.href='tienda.php?id=<?php echo $n['negocio_id']; ?>'">Ver tienda</button>
</div>
<?php } ?>
</main>
</body>
</html>
