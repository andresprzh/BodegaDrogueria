<?php


include "../controladores/alistar.controlador.php";

require "../modelos/conexion.php";

require "../modelos/alistar.modelo.php";



// obtienen los datos dela requisicion (numero requisicion y codigo alistador)
$req=$_POST["Req"];
//crea objeto controlador 
$controlador=new ControladorAlistar($req);

$Cod_barras=$_POST['codigo'];

// regresa el resultado de la buqueda como un objeto JSON
$respuesta=$controlador->ctrBuscarItems($Cod_barras);

// muestra el vector como dato JSON
print json_encode( $respuesta);