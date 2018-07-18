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


//crea caja nueva para el usuario
$respuesta=$controlador->ctrCrearCaja();


// muestra el vector como dato JSON
print json_encode( $respuesta);