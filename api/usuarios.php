<?php
session_start();

include "../controladores/usuarios.controlador.php";

require "../modelos/conexion.php";

require "../modelos/usuarios.modelo.php";


if (isset($_GET["ruta"])) {
    

    switch ($_GET["ruta"]) {
        
        case "modificar":

            $datosusuario=$_POST["datosusuario"];
            $button=$_POST["button"];

            $controlador=new ControladorUsuarios();

            //encripta la contraseña
            $datosusuario["password"]=password_hash($datosusuario["password"], PASSWORD_BCRYPT);
            if ($button=="agregar") {
                $resultado=$controlador->ctrCrearUsuario($datosusuario);
            }else {
                $resultado=$controlador->ctrModificarUsuario($datosusuario);
            }

            print json_encode($resultado);

            break;
        
        case "perfiles":

            $perfil=$_SESSION["usuario"]["perfil"];
            $controlador=new ControladorUsuarios();
            
            $resultado=$controlador->ctrBuscarPerfiles($perfil);
            
            print json_encode($resultado);
            
            break;

        case "usuarios":
            $perfil=$_SESSION["usuario"]["perfil"];
            $controlador=new ControladorUsuarios();
            
            $resultado=$controlador->ctrBuscarUsuarios($perfil);
            
            echo json_encode($resultado);
            
            break;
    }
}