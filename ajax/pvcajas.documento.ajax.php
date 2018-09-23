<?php

include "../controladores/pv.controlador.php";

require "../modelos/conexion.php";
require "../modelos/pv.modelo.php";
require "../modelos/cajas.modelo.php";
require "../modelos/alistar.modelo.php";
/* ============================================================================================================================
                                                MUESTRA LAS CAJAS O LOS ITEMS DE LA CAJA
============================================================================================================================*/
// obtienen los datos dela requisicion (numero requisicion y codigo alistador)
$req=$_POST['req'];
$numcaja=$_POST['numcaja'];
//crea objeto controlador 
$controlador=new ControladorPV($req);

// cea documento de la caj arecibida
$respuesta=$controlador->ctrDocumentoR($numcaja);


// muestra el vector como dato JSON

print json_encode($respuesta);