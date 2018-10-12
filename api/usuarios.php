<?php
session_start();

include "../controladores/usuarios.controlador.php";

require "../modelos/conexion.php";

require "../modelos/usuarios.modelo.php";

require "cors.php";

if (isset($_GET["ruta"])) {
    

    switch ($_GET["ruta"]) {
        

        /* ============================================================================================================================
                                                MODIFICA UN USUARIO
        ============================================================================================================================*/
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
        
        /* ============================================================================================================================
                                            BUSCA PERFILES DE USUARIOS
        ============================================================================================================================*/
        case "perfiles":

            $perfil=$_SESSION["usuario"]["perfil"];
            $controlador=new ControladorUsuarios();
            
            $resultado=$controlador->ctrBuscarPerfiles($perfil);
            
            print json_encode($resultado);
            
            break;
        /* ============================================================================================================================
                                                BUSCA USUARIOS
        ============================================================================================================================*/
        case "usuarios":
            $perfil=$_SESSION["usuario"]["perfil"];
            $controlador=new ControladorUsuarios();
            
            $resultado=$controlador->ctrBuscarUsuarios($perfil);
            
            echo json_encode($resultado);
            
            break;
        /* ============================================================================================================================
                                                        REALIZA EL LOGIN EN LA PAGINA
        ============================================================================================================================*/
        case "login":
            $dato=$_POST["username"];
            print json_encode("hay conexion ".$dato);
            break;
    }
}