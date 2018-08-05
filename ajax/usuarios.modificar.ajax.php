<?php


include "../controladores/usuarios.controlador.php";

require "../modelos/conexion.php";

require "../modelos/usuarios.modelo.php";

$datosusuario=$_POST['datosusuario'];
$button=$_POST['button'];

$controlador=new ControladorUsuarios();

//encripta la contraseña
$datosusuario['password']=password_hash($datosusuario['password'], PASSWORD_BCRYPT);
if ($button=='agregar') {
    $resultado=$controlador->ctrCrearUsuario($datosusuario);
}else {
    $resultado=$controlador->ctrModificarUsuario($datosusuario);
}

print json_encode($resultado);

