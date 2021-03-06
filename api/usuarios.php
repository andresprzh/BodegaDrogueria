<?php

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
            $controlador=new ControladorLoginUsuario();
            
            $resultado=$controlador->ctrBuscarUsuarios($perfil);
            
            echo json_encode($resultado);
            
            break;
        /* ============================================================================================================================
                                                        MUESTRA TODAS LAS FRANQUICIAS
        ============================================================================================================================*/
        case "franquicias":
            $modelo = new ModeloUsuarios();
            $resultado=$modelo->mdlMostrarFranquicias();
            print json_encode($resultado->fetchAll());
            break;
        /* ============================================================================================================================
                                                        REALIZA EL LOGIN EN LA PAGINA
        ============================================================================================================================*/
        case "login":
            $username=$_POST["username"];
            $password=$_POST["password"];

            $controlador = new ControladorLoginUsuario();
            $resultado=$controlador->ctrIngresoUsuario($username,$password);
            print json_encode($resultado);
            break;
    }
}