<?php

include "../controladores/cajas.controlador.php";


require "../modelos/conexion.php";
require "../modelos/cajas.modelo.php";
require "../modelos/alistar.modelo.php";
/* ============================================================================================================================
                                                MUESTRA LAS CAJAS O LOS ITEMS DE LA CAJA
============================================================================================================================*/
// obtienen los datos dela requisicion (numero requisicion y codigo alistador)
$Req=$_POST["Req"];

//crea objeto controlador 
$controlador=new ControladorCajas($Req);


// si se pasa el numeor de la caja se busca dicha caja 
if (isset($_POST['NumCaja'])) {

    $NumCaja=$_POST['NumCaja'];
    // regresa el resultado de la buqueda como un objeto JSON
    $respuesta=$controlador->ctrBuscarItemCaja($NumCaja);
    
// si no se paso el numero de la caja busca todas las cajas de la requisicion  seleccionada
}else{

    $NumCaja='%%';
    // regresa el resultado de la buqueda como un objeto JSON
    $respuesta=$controlador->ctrBuscarCaja($NumCaja);
    
}


// muestra el vector como dato JSON
print json_encode($respuesta);
