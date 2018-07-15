<?php


require "../modelos/conexion.php";

require "../modelos/requerir.modelo.php";



$modelo=new ModeloRequierir();
$item='estado';
$valor=0;
$res=$modelo->mdlMostrarReq($item,$valor);


$cont=0;//contador para almacenar los datos en un vector
while($row = $res->fetch()) {
    //almacena la busqueda en un vector
    $req[$cont]=$row["No_Req"];
    //aumenta el contador
    $cont++;
}

// muestra el vector     como dato JSON
print json_encode($req);