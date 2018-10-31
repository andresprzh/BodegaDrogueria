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
            $controlador=new ControladorRemision();
            $resultado=$controlador->ctrDocRem();
            print ($resultado);
            return 0;
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                
                
                
                if (isset($_FILES['files'])) {
                    
                    $folder=$_POST['folder'];
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
                    $controlador=new ControladorRemision($archivos);
                    
                    
                    $resultado=$controlador->ctrSetItems();
                    $resultado=$controlador->ctrSubirRem();

                    if ($resultado) {
                        $resultado=$controlador->ctrDocRem();
                    }
                    print json_encode($resultado);
                    // foreach($archivos as $archivo){
                    //     foreach($archivo as $linea){
                    //         print json_encode($linea);
                    //     }
                    // }
                    // print json_encode($archivo[0][1]);
                    
                // if ($errors) print json_encode($resultado);
                }
            }
                
            
            // print json_encode($resultado);
            break;  

        default:
            print json_encode("Remisiones");
            break;
    }
    return 1;
}