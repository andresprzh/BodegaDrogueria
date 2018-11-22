<?php

include "../controladores/loginusuario.controlador.php";
include "../controladores/usuarios.controlador.php";
include "../controladores/tareas.controlador.php";
include "../controladores/alistar.controlador.php";
include "../controladores/cajas.controlador.php";
include "../controladores/pv.controlador.php";
include "../controladores/remision.controlador.php";
include "../controladores/requerir.controlador.php";
include "../controladores/transportador.controlador.php";


require "../modelos/conexion.php";
require "../modelos/loginusuario.modelo.php";
require "../modelos/usuarios.modelo.php";
require "../modelos/tareas.modelo.php";
require "../modelos/alistar.modelo.php";
require "../modelos/cajas.modelo.php";
require "../modelos/pv.modelo.php";
require "../modelos/remision.modelo.php";
require "../modelos/requerir.modelo.php";
require "../modelos/transportador.modelo.php";


// function cors() {

// Allow from any origin
if (isset($_SERVER['HTTP_ORIGIN'])) {
    // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
    // you want to allow, and if so:
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    // header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        // may also be using PUT, PATCH, HEAD etc
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}
session_start();
// }