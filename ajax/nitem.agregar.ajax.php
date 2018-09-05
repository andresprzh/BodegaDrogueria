<?php


include "../controladores/alistar.controlador.php";

require "../modelos/conexion.php";

require "../modelos/alistar.modelo.php";

/* ============================================================================================================================
                                                MUESTRA LOS ITEMS DELA reqUISICION 
============================================================================================================================*/

// obtienen los datos dela requisicion (numero requisicion y codigo alistador)
$req = $_POST["req"];
$items = $_POST["items"];
//crea objeto controlador 
$controlador=new ControladorAlistar($req);



// regresa el resultado de la buqueda como un objeto JSON
$respuesta = $controlador->ctrAgregarIE($items);

// muestra el vector como dato JSON
print json_encode( $respuesta);