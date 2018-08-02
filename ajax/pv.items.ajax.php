<?php


include "../controladores/pv.controlador.php";



require "../modelos/conexion.php";
require "../modelos/pv.modelo.php";
require "../modelos/cajas.modelo.php";
require "../modelos/alistar.modelo.php";
/* ============================================================================================================================
                                                MUESTRA EL ITEM BUSCADO
============================================================================================================================*/
// obtienen los datos dela requisicion (numero requisicion y codigo alistador)
$req=$_POST["req"];
$controlador=new ControladorPV($req);
$codigo=$_POST["codigo"];


$busqueda=$controlador->ctrBuscarItemPV($codigo);

print json_encode($busqueda);