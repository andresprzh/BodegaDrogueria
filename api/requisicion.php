<?php


include "../controladores/requerir.controlador.php";

require "../modelos/conexion.php";

require "../modelos/requerir.modelo.php";

require "cors.php";

if (isset($_GET["ruta"])) {
    

    switch ($_GET["ruta"]) {
        

        /* ============================================================================================================================
                                                sube una requisicion
        ============================================================================================================================*/
        case "req":

            //comprueba si hay algun error con el archivo
            // $resultado['estado']=true;
            // print json_encode($resultado);
            // return 0;
            if (isset($_FILES["archivo"]["tmp_name"])) {
                
                if (0 != $_FILES['archivo']['error']) {

                    $resultado["estado"]=false;
                    $resultado["contenido"]='¡Error al subir el acrhivo¡';
                }
                    //abre el archivo si no hay errores
                else {

                    $tipos_permitidos = array('text/plain','text/x-Algol68');//tipos permitidos de archivos
                    $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
                    $tipo = finfo_file($fileInfo, $_FILES['archivo']['tmp_name']);//tipo de archivo subido
                        // SI EL ARCHIVO NO ES DE TIPO TEXTO NO LO ABRE
                        
                    if (!in_array($tipo, $tipos_permitidos)) {
                        $resultado["estado"]=false;
                        $resultado["contenido"]='¡Tipo de archivo no valido¡';
                    } else {
                        
                        $archivo = file($_FILES['archivo']['tmp_name']); 
                        //se crea objeto requerir, que busca y manda los items a la base de datos
                        $controlador = new ControladorRequerir($archivo);
                        $resultado=$controlador->resultado;
                        // if (isset($Requerir)) {
                        //     echo ('<script> 
                        //     $("#carga").addClass("hide");
                        // </script>');   
                        // }
                        // $resultado["estado"]=true;
                        // $resultado["contenido"]='todo bonito';
                    }

                    finfo_close($fileInfo);

                }
                
            
            }
            print json_encode($resultado);
            break;
        
    }
}