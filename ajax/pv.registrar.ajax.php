<?php

include "../controladores/pv.controlador.php";



require "../modelos/conexion.php";
require "../modelos/pv.modelo.php";
require "../modelos/cajas.modelo.php";
require "../modelos/alistar.modelo.php";
/* ============================================================================================================================
                                                REGISTRA LOS ITEMS DE LA CAJA Y HACE EL INFORME
============================================================================================================================*/
$req=$_POST['req'];
$items=$_POST['items'];
$numcaja=$_POST['caja'];

$controlador=new ControladorPV($req);

$resultado=$controlador->ctrRegistrarItems($items,$numcaja);

print json_encode($resultado);