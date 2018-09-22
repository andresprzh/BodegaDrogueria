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

//crea objeto controlador 
$controlador=new ControladorPV($req);

// si se pasa el numeor de la caja se busca dicha caja 
if (isset($_POST['numcaja'])) {

    $numcaja=$_POST['numcaja'];
    // regresa el resultado de la buqueda como un objeto JSON
    if (isset($_POST['estado'])) {
                    
        $respuesta=$controlador->ctrBuscarItemCaja($numcaja);
        
    }
    
    
// si no se paso el numero de la caja busca todas las cajas de la requisicion  seleccionada
}else{
    $numcaja='%%';
    // regresa el resultado de la buqueda como un objeto JSON
    $respuesta=$controlador->ctrBuscarCaja($numcaja,3);
    // print json_encode($algo);
}


// muestra el vector como dato JSON

print json_encode($respuesta);
