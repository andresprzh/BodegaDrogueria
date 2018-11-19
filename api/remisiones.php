<?php

include "../controladores/remision.controlador.php";

require "../modelos/conexion.php";

require "../modelos/remision.modelo.php";
require "../modelos/requerir.modelo.php";

require "cors.php";

if (isset($_GET["ruta"])) {
    

    switch ($_GET["ruta"]) {
        
        /* ============================================================================================================================
                                        GENERA DOCUMENTO REMISION
        ============================================================================================================================*/    
        case "docrem":

            // // print json_encode($resultado);
            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                 
                if (isset($_FILES["files"]) && isset($_POST["franquicia"])) {
                    
                    $franquicia=$_POST["franquicia"];
                    $usuario=$_POST["usuario"];
                    
                    $tipos_permitidos = array("text/plain","text/x-Algol68");//tipos permitidos de archivos
                    $fileInfo = finfo_open(FILEINFO_MIME_TYPE);

                    $all_files = count($_FILES["files"]["tmp_name"]);
                    $resultado="";
                    
                    
                    for ($i = 0; $i < $all_files; $i++) {  
                        $tipo = finfo_file($fileInfo, $_FILES["files"]["tmp_name"][$i]);
                        // solo permite archivos de texto
                        
                        if (in_array($tipo, $tipos_permitidos)) {
                            
                            $archivos[]= file($_FILES["files"]["tmp_name"][$i]);
                            
                        }
                        
                       
                    }
                    $controlador=new ControladorRemision($archivos,$franquicia);
                    
                    $resultado=$controlador->ctrSetCabecera();
                    $resultado=$controlador->ctrSetItems();
                    $resultado=$controlador->ctrSubirRem($usuario);

                    if ($resultado) {
                        $resultado=$controlador->ctrDocRem();
                    }
                    print json_encode($resultado);
                }
            }else {
                $controlador=new ControladorRemision();
                // $resultado=$controlador->ctrDocRemCopi(1);
                $resultado=$controlador->ctrDocRemEA(1);

                print($resultado["documento"]);
            }
                
            
            // print json_encode($resultado);
            break;
        /* ============================================================================================================================
                                            ASIGNA LOTE Y GENERA DOCUMENTO
        ============================================================================================================================*/    
        case "doclotes":
            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                
                if (isset($_POST["items"])) {
                    
                    $items=$_POST["items"];
                    $rem=$_POST["rem"];
                    
                    
                    $controlador=new ControladorRemision();
                    
                    
                    $resultado=$controlador->ctrAsignarLote($items,$rem);
                    // print json_encode($rem);
                    // return 0;
                    if ($resultado) {
                        $resultado=$controlador->ctrDocRem($rem);
                    }
                    print json_encode($resultado);
                }
            }
                
            
            // print json_encode($resultado);
            break;    
        /* ============================================================================================================================
                                                        BUSCA FRANQUICIAS
        ============================================================================================================================*/    
        case "franquicias":
            
            if ($_SERVER["REQUEST_METHOD"] === "GET") {
                
                
                    
                $modelo=new ModeloRemision();

                $resultado=$modelo->mdlMostrarFranquicias();

                print json_encode($resultado->fetchAll());
                    
                
            }
                
            // print json_encode($resultado);
            break;  
        /* ============================================================================================================================
                                                        BUSCA FRANQUICIAS
        ============================================================================================================================*/    
        case "remisiones":
           
            if ($_SERVER["REQUEST_METHOD"] === "GET") {
                
                $modelo=new ModeloRemision();
                if (!isset($_GET["franquicia"]) 
                   || $_SESSION["usuario"]["perfil"]==1 
                   || $_SESSION["usuario"]["perfil"]==8) {
                    
                    $resultado=$modelo->buscaritem("remisiones");
                }
                else {
                    $estado=null;
                    if (isset($_GET["estado"])) {
                        $estado=$_GET["estado"];
                    } 
                    $resultado=$modelo->mdlMostrarRem($_GET["franquicia"],$estado);
                }
                
                print json_encode($resultado->fetchAll());
                    
            }
                
            // print json_encode($resultado);
            break;  

        default:
            print json_encode("Remisiones");
            break;
    }
    return 1;
}