<?php
// Nombre de la carpeta del proyecto dentro de htdocs. Si renombrás la carpeta
// o subís el proyecto a otra ruta, actualizá esta constante.
if (!defined('BASE_URL')) {
    define('BASE_URL', '/gestion_de_turnos');
}

// Datos de la base de datos
$usuario = "root";
$password = "";
$servidor = "localhost";
$basededatos = "gestion_de_turnos";

$conexion = new mysqli($servidor, $usuario, $password, $basededatos);
if ($conexion->connect_errno) {
    echo "Fallo al conectar a MySQL: (" . $conexion->connect_errno . ") " . $conexion->connect_error;
}


$conexion = new mysqli($servidor, $usuario, $password, $basededatos,3306);
if ($conexion->connect_errno) {
    echo "Fallo al conectar a MySQL: (" . $conexion->connect_errno . ") " . $conexion->connect_error;
}
// **AGREGAR ESTA LÍNEA**
mysqli_set_charset($conexion, "utf8");
?>

