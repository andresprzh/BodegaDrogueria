<?php

include "../controladores/tareas.controlador.php";

require "../modelos/conexion.php";

require "../modelos/tareas.modelo.php";

require "cors.php";

if (isset($_GET["ruta"])) {
    

    switch ($_GET["ruta"]) {
        

        /* ============================================================================================================================
                                                BUSCA USUARIOS DE PERFIL ALISTADOR
        ============================================================================================================================*/
        case "usuarios":

            $modelo=new Conexion();

            $res=$modelo->buscaritem("usuario","perfil",3);
            $resultado["estado"]=false;
            if ($res->rowCount() >0) {
                $resultado["contenido"]=$res->fetchAll();
                $resultado["estado"]="encontrado";
            }
            
            
            print json_encode($resultado);

            break;
        /* ============================================================================================================================
                                                BUSCA  O AISGNA LAS TAREAS 
        ============================================================================================================================*/
        case "tareas":
            // busca tareas
            if ($_SERVER['REQUEST_METHOD']==='GET') {
                $modelo=new Conexion();
                // si hay usuario busca tareas usuario
                if (isset($_GET["usuario"])) {
                    $res=$modelo->buscaritem("tareas","usuario",$_GET["usuario"]);
                //busca todas las teras de la tabla
                }else {
                    $res=$modelo->buscaritem("tareas");
                }
                $res=$modelo->buscaritem("tareas");
                $resultado["estado"]=false;
                if ($res->rowCount() >0) {
                    $resultado["contenido"]=$res->fetchAll();
                    $resultado["estado"]="encontrado";
                }
            // agrega tareas
            }else{
                $controlador=new ControladorTareas();
                $usuario=$_POST["usuario"];

                $resultado=$controlador->ctrCrearTareas($usuario);
            }
            
            
            print json_encode($resultado);
            break;
        
        /* ============================================================================================================================
                                                ASIGNA O BUSCA DETALLE DE TAREA(UBICACION) DE UNA TAREA O USUARIO
        ============================================================================================================================*/    
        case "dettarea":
            
            $usuario=$_REQUEST['usuario'];
            
            $controlador=new ControladorTareas();
            
            // muestra ubicaciones
            if ($_SERVER['REQUEST_METHOD']==='GET') {
                
                $resultado=$controlador->ctrBuscarUbicaciones($usuario);
            // asigna ubicacion
            }else {

                $ubicacion=$_POST['ubicacion'];
                $resultado=$controlador->ctrAsignarUbicacion($ubicacion,$usuario);
            }
            
            
            print json_encode($resultado);
            break;

        /* ============================================================================================================================
                                                BUSCA TODAS LAS UBICACIONES
        ============================================================================================================================*/    
        case "ubicaciones":
            
            
            $controlador=new ControladorTareas();
            
            $resultado=$controlador->ctrBuscarUbicaciones();
            
            
            print json_encode($resultado);
            break;
        default:
            print json_encode("Tareas");
            break;
    }
    return 1;
}