<?php

include "../controladores/cajas.controlador.php";


require "../modelos/conexion.php";
require "../modelos/cajas.modelo.php";
require "../modelos/alistar.modelo.php";

/* ============================================================================================================================
                                                CREA DOCUMENTO PLANO DE LA CAJA
============================================================================================================================*/
// obtienen los datos dela requisicion (numero requisicion y codigo alistador)
$req=$_POST["req"];
$numcaja=$_POST['numcaja'];


//crea objeto controlador 
$controlador=new ControladorCajas($req);

$resultado=$controlador->ctrCancelar($numcaja);


print json_encode($resultado);