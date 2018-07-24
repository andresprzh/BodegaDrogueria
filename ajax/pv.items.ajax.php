<?php


include "../controladores/pv.controlador.php";



require "../modelos/conexion.php";
require "../modelos/pv.modelo.php";
require "../modelos/cajas.modelo.php";
require "../modelos/alistar.modelo.php";




// obtienen los datos dela requisicion (numero requisicion y codigo alistador)
$Req=$_POST["Req"];
$controlador=new ControladorPV($Req);
$codigo=$_POST["codigo"];


$busqueda=$controlador->ctrBuscarItemPV($codigo);

print json_encode($busqueda);