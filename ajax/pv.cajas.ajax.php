<?php

include "../controladores/pv.controlador.php";

require "../modelos/conexion.php";
require "../modelos/pv.modelo.php";
require "../modelos/cajas.modelo.php";
require "../modelos/alistar.modelo.php";
/* ============================================================================================================================
                                                MUESTRA LAS CAJAS QUE LLEGARON AL PUNTO DE VENTA
============================================================================================================================*/
// obtienen los datos dela requisicion (numero requisicion y codigo alistador)
$Req=$_POST["Req"];
$controlador=new ControladorPV($Req);

$cont=0;
// regresa el resultado de la buqueda como un objeto JSON
$respuesta=$controlador->ctrBuscarCajaPV("%%");
if ($respuesta['estado']=='encontrado') {
    
    foreach ($respuesta['contenido'] as $row) {
        $res['cajas'][$cont]=$row["no_caja"];
        $cont++;
    }   

    $res['requisicion']=$controlador->ctrBuscarReq();
    
}else{
    $res=false;
}
// muestra el vector     como dato JSON
print json_encode($res);


