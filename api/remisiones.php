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
            // $controlador=new ControladorRemision();
            // $resultado=$controlador->ctrDocRem();
            // print ($resultado["documento"]);
            // // print json_encode($resultado);
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                 
                if (isset($_FILES['files']) && isset($_POST['franquicia'])) {
                    
                    $franquicia=$_POST['franquicia'];
                    $tipos_permitidos = array('text/plain','text/x-Algol68');//tipos permitidos de archivos
                    $fileInfo = finfo_open(FILEINFO_MIME_TYPE);

                    $all_files = count($_FILES['files']['tmp_name']);
                    $resultado="";
                    
                    
                    for ($i = 0; $i < $all_files; $i++) {  
                        $tipo = finfo_file($fileInfo, $_FILES['files']['tmp_name'][$i]);
                        // solo permite archivos de texto
                        
                        if (in_array($tipo, $tipos_permitidos)) {
                            $archivos[]= file($_FILES['files']['tmp_name'][$i]);
                            
                        }
                        
                       
                    }
                    $controlador=new ControladorRemision($archivos,$franquicia);
                    
                    $resultado=$controlador->ctrSetCabecera();
                    $resultado=$controlador->ctrSetItems();
                    $resultado=$controlador->ctrSubirRem();

                    if ($resultado) {
                        $resultado=$controlador->ctrDocRem();
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
            
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                
                
                    
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
            
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                
                
                    
                $modelo=new ModeloRemision();

                $resultado=$modelo->buscaritem('remisiones');

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