<?php

include "../controladores/pv.controlador.php";



require "../modelos/conexion.php";
require "../modelos/pv.modelo.php";
require "../modelos/cajas.modelo.php";
require "../modelos/alistar.modelo.php";
/* ============================================================================================================================
                                                REGISTRA LOS ITEMS DE LA CAJA Y HACE EL INFORME
============================================================================================================================*/
$Req=$_POST['Req'];
$Items=$_POST['Items'];
$NumCaja=$_POST['Caja'];

$controlador=new ControladorPV($Req);

$resultado=$controlador->ctrRegistrarItems($Items,$NumCaja);

print json_encode($resultado);