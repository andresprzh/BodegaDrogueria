<?php

include "../controladores/pv.controlador.php";



require "../modelos/conexion.php";
require "../modelos/pv.modelo.php";
require "../modelos/cajas.modelo.php";
require "../modelos/alistar.modelo.php";
require "../modelos/tareas.modelo.php";

if (isset($_GET['ruta'])) {
    

    switch ($_GET['ruta']) {
        
        /* ============================================================================================================================
                                                MUESTRA LAS CAJAS QUE LLEGARON AL PUNTO DE VENTA
        ============================================================================================================================*/
        case "cajas":
            // obtienen los datos dela requisicion (numero requisicion y codigo alistador)
            $req=$_POST["req"];
            $controlador=new ControladorPV($req);

            $cont=0;
            // regresa el resultado de la buqueda como un objeto JSON
            $respuesta=$controlador->ctrBuscarCajaPV("%%");
            if ($respuesta['estado']=='encontrado') {
                
                foreach ($respuesta['contenido'] as $row) {
                    // print json_encode(isset($row[0]));
                    // si solo es un resultado
                    
                    if (!is_array($row)) {
                        $res['cajas']=$row;
                        break;
                    }
                    $res['cajas'][$cont]=$row["no_caja"];
                    $cont++;
                }   

                $res['requisicion']=$controlador->ctrBuscarReq();
                
            }else{
                $res=false;
            }
            // muestra el vector     como dato JSON
            print json_encode($res);

            break;

        /* ============================================================================================================================
                                                MUESTRA EL ITEM BUSCADO
        ============================================================================================================================*/
        case "items":
            // obtienen los datos dela requisicion (numero requisicion y codigo alistador)
            if (isset($_POST["req"])) {
                $controlador=new ControladorPV($_POST["req"]);
            }else {
                $controlador=new ControladorPV();
            }

            $codigo=$_POST["codigo"];


            $busqueda=$controlador->ctrBuscarItemPV($codigo);

            print json_encode($busqueda);
            break;
        
        /* ============================================================================================================================
                                                REGISTRA LOS ITEMS DE LA CAJA Y HACE EL INFORME
        ============================================================================================================================*/
        case "registrar":
            if ($_SERVER["REQUEST_METHOD"]==="POST") {
                
                $items=$_POST["items"];
                // si no es franquicia
                if (!isset($_POST["franquicia"])) {
                    $req=$_POST["req"];
                
                    $numcaja=$_POST["caja"];
                    
                    $controlador=new ControladorPV($req);
                    $resultado=$controlador->ctrRegistrarItems($items,$numcaja); 
                }else {
                    
                    $franquicia=$_POST["franquicia"];
                    $controlador=new ControladorPV();
                    $resultado=$controlador->ctrDocumentoProducto($items,$franquicia);
                }
                
                
            }else {
                $items=$_GET["items"];
                $franquicia=$_GET["franquicia"];
                $controlador=new ControladorPV();
                $resultado=$controlador->ctrDocumentoProducto($items,$franquicia);
                

                // envia correo
                // $controlador->ctrEnviarMail($resultado); 

            }
            print json_encode($resultado);                       
            // print($resultado);

            break;
        
        /* ============================================================================================================================
                                                ENVIA MAIL CN ARCHIVO ADJUNTO
        ============================================================================================================================*/
        case "mail":
            $data=$_GET["data"];
            $controlador=new ControladorPV();
            $resultado=$controlador->ctrEnviarMail($data); 
            print json_encode($resultado);                       
            
            break;
            
    }

}