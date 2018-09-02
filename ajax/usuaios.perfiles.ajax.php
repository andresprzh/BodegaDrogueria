<?php

session_start();

include "../controladores/usuarios.controlador.php";

require "../modelos/conexion.php";

require "../modelos/usuarios.modelo.php";

$perfil=$_SESSION["usuario"]["perfil"];
$controlador=new ControladorUsuarios();

$resultado=$controlador->ctrBuscarPerfiles($perfil);
// if ($resultado['estado']=='encontrado') {
    echo json_encode($resultado);
// }



