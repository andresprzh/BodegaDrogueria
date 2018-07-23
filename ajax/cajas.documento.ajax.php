<?php

include "../controladores/cajas.controlador.php";


require "../modelos/conexion.php";
require "../modelos/cajas.modelo.php";
require "../modelos/alistar.modelo.php";

// obtienen los datos dela requisicion (numero requisicion y codigo alistador)
$Req=$_POST["Req"];
$NumCaja=$_POST['NumCaja'];
$Mensaje=$_POST['Mensaje'];

//crea objeto controlador 
$controlador=new ControladorCajas($Req);

$resultado=$controlador->ctrDocumento($NumCaja,$Mensaje);

print $resultado;
