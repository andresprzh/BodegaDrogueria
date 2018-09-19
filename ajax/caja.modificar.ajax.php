<?php

include '../controladores/cajas.controlador.php';


require '../modelos/conexion.php';
require '../modelos/cajas.modelo.php';
require '../modelos/alistar.modelo.php';
/* ============================================================================================================================
                                                MUESTRA LAS CAJAS O LOS ITEMS DE LA CAJA
============================================================================================================================*/
// obtienen los datos dela requisicion (numero requisicion y codigo alistador)
$req=$_POST['req'];
$numcaja=$_POST['numcaja'];
$items=$_POST['items'];
//crea objeto controlador 
$controlador=new ControladorCajas($req);

$resultado=$controlador->ctrModificarCaja($numcaja,$items);

print json_encode($resultado);