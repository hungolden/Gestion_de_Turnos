<?php
require("../../require_admin.php");
error_reporting(E_ALL ^ E_NOTICE);
$negocio_id = $_SESSION['usuario_negocio_id'];

$stmtEd = $conexion->prepare("SELECT id, nombre FROM especialidades WHERE negocio_id = ? ORDER BY nombre");
$stmtEd->bind_param("i", $negocio_id);
$stmtEd->execute();
$especialidades = $stmtEd->get_result();

$mensajes = array(
	'noexiste'    => 'Ese correo no está registrado en el sistema. La persona primero tiene que crearse una cuenta desde "Registrarse"; después podés volver a darla de alta acá.',
	'nodisponible'=> 'Esa cuenta ya administra o trabaja en un negocio, así que no se la puede contratar.',
	'inactivo'    => 'Esa cuenta está inactiva.',
	'pendiente'   => 'Ya le enviaste una invitación a esa persona y todavía no respondió.',
	'datos'       => 'Completá todos los campos.'
);
$error = isset($_GET['error']) && isset($mensajes[$_GET['error']]) ? $mensajes[$_GET['error']] : '';
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Nuevo Especialista</title>
<link rel="stylesheet" href="../../estilos.css">
</head>
<body>
<?php require("../../menu_interior1.php"); ?>
<main class="container">

<?php if ($error !== '') { ?><p style="color:#a8433c;"><?php echo htmlspecialchars($error); ?></p><?php } ?>

<form action="alta.php" method="post">
<h3>Especialidad</h3>
<select name="especialidad_id" id="especialidad_id" required>
<option value="">-- Seleccione --</option>
<?php while ($e = $especialidades->fetch_assoc()) { ?>
<option value="<?php echo $e['id']; ?>"><?php echo htmlspecialchars($e['nombre']); ?></option>
<?php } ?>
</select><br>
<h3>Nombre</h3>
<input type="text" name="nombre" id="nombre" value="<?php echo isset($_GET['nombre']) ? htmlspecialchars($_GET['nombre']) : ''; ?>" required><br>
<h3>Correo electrónico</h3>
<input type="email" name="email" id="email" value="<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>" required><br>
<p style="color:#666;font-size:0.9em;">La persona tiene que estar registrada en el sistema con ese correo. Se le enviará una invitación y recién cuando la acepte va a quedar activa como especialista de tu local.</p>
<input type="submit" value="Enviar invitación">
<button type="button" onclick="location.href='../'">Volver</button>
</form>
</main>
</body>
</html>
