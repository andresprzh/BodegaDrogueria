<?php

include "../controladores/alistar.controlador.php";

require "../modelos/conexion.php";

require "../modelos/alistar.modelo.php";

// obtienen los datos dela requisicion (numero requisicion y codigo alistador)
$req=$_POST["Req"];
$TipoCaja=$_POST['TipoCaja'];
$Items=$_POST['Items'];
//crea objeto controlador 
$controlador=new ControladorAlistar($req);

$respuesta=$controlador->ctrCerrarCaja($TipoCaja,$Items,$req);

print ($respuesta);

