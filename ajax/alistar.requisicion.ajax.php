<?php


require "../modelos/conexion.php";

require "../modelos/requerir.modelo.php";


/* ============================================================================================================================
                                                MUESTRA LAS REQUISICIONES
============================================================================================================================*/
$modelo=new ModeloRequierir();
$item='enviado';

$res=$modelo->mdlMostrarReq($item);


$cont=0;//contador para almacenar los datos en un vector

// si hay resultados los regresa como json
if ($res->rowCount()) {
    while($row = $res->fetch()) {
        //almacena la busqueda en un vector
        $req[$cont]=$row["no_req"];
        //aumenta el contador
        $cont++;
    }
    // muestra el vector     como dato JSON
    print json_encode($req);
}


