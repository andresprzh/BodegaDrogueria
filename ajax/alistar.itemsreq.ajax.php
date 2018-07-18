<?php


include "../controladores/alistar.controlador.php";

require "../modelos/conexion.php";

require "../modelos/alistar.modelo.php";

/* ============================================================================================================================
                                                MUESTRA LOS ITEMS DELA REQUISICION 
============================================================================================================================*/
// obtienen los datos de la requisicion (numero requisicion y codigo alistador)
$req=$_POST["Req"];
//crea objeto controlador 
$controlador=new ControladorAlistar($req);

$Cod_barras='%%';

//crea caja nueva para el usuario
$caja=$controlador->ctrCrearCaja();

// regresa el resultado de la buqueda como un objeto JSON
$respuesta["items"]=$controlador->ctrBuscarItems($Cod_barras);
$respuesta["caja"]=$caja;

// muestra el vector como dato JSON
print json_encode( $respuesta);