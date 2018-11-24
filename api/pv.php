<?php

require "cors.php";

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
                   
                    $res['cajas'][]=["no_caja"=>$row["no_caja"],
                                          "num_caja"=>$row["num_caja"]];
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
            // si es franquicia
            if (isset($_REQUEST["no_rem"])) {
                
                $modelo=new ModeloPV();
                $busqueda=$modelo->mdlMostraRecibidoRemision($_REQUEST["no_rem"]);
                $resultado=$busqueda->fetchAll();
            }else {

                if (isset($_POST["req"])) {
                    $controlador=new ControladorPV($_POST["req"]);
                }else {
                    $controlador=new ControladorPV();
                }

                $codigo=$_POST["codigo"];
                $resultado=$controlador->ctrBuscarItemPV($codigo);
            }
            print json_encode($resultado);
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
                    $rem=$_POST["rem"];
                    $controlador=new ControladorPV();
                    $resultado=$controlador->ctrRegistrarRemision($items,$rem,$franquicia);
                }
                
                
            }else {
                $items=$_GET["items"];
                $franquicia=$_GET["franquicia"];
                $controlador=new ControladorPV();
                $resultado=$controlador->ctrDocumentoRemision($franquicia,$items);
                

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
            $data=$_REQUEST["data"];
            $controlador=new ControladorPV();
            $resultado=$controlador->ctrEnviarMail($data); 
            print json_encode($resultado);                       
            
            break;
         /* ============================================================================================================================
                                                                GENERA DOCUMENTO REMISION
        ============================================================================================================================*/
        case "documento":
            $franquicia=$_REQUEST["franquicia"];
            $no_rem=$_REQUEST["no_rem"];
            
            $controlador=new ControladorPV();
            $resultado=$controlador->ctrDocumentoRemision($franquicia,$no_rem); 
            print json_encode($resultado);                       
            
            
            break;
            
    }

}