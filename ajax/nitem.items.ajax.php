<?php


include "../controladores/alistar.controlador.php";

require "../modelos/conexion.php";

require "../modelos/alistar.modelo.php";

/* ============================================================================================================================
                                                MUESTRA LOS ITEMS DELA reqUISICION 
============================================================================================================================*/

// obtienen los datos dela requisicion (numero requisicion y codigo alistador)
$req = $_POST["req"];
$item = $_POST["item"];
//crea objeto controlador 
$controlador=new ControladorAlistar($req);



// regresa el resultado de la buqueda como un objeto JSON
$respuesta = $controlador->ctrBuscarIE($item);

// muestra el vector como dato JSON
print json_encode( $respuesta);