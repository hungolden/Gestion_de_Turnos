<?php
// "Negocio_Config" ahora es la ficha del negocio del admin logueado
// (cada admin tiene exactamente uno), no un listado de todos los negocios.
require("../require_admin.php");
header("Location: modificacion/index.php");
exit;
?>
