<?php

include "../controladores/pv.controlador.php";

require "../modelos/conexion.php";
require "../modelos/pv.modelo.php";
require "../modelos/cajas.modelo.php";
require "../modelos/alistar.modelo.php";

// obtienen los datos dela requisicion (numero requisicion y codigo alistador)
$Req=$_POST["Req"];
$controlador=new ControladorPV($Req);

$cont=0;
// regresa el resultado de la buqueda como un objeto JSON
$respuesta=$controlador->ctrBuscarCaja("%%");
if ($respuesta['estado']=='encontrado') {
    
    foreach ($respuesta['contenido'] as $row) {
        $res[$cont]=$row["no_caja"];
        $cont++;
    }   
    
}else{
    $res=false;
}

print json_encode($res);

// muestra el vector     como dato JSON
