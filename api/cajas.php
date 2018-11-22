<?php

require "cors.php";

if (isset($_GET['ruta'])) {
    

    switch ($_GET['ruta']) {
        
        /* ============================================================================================================================
                                                MUESTRA LAS CAJAS O LOS ITEMS DE LA CAJA
        ============================================================================================================================*/    
        case "cajas":
            // obtienen los datos dela requisicion (numero requisicion y codigo alistador)
            $req=$_POST['req'];

            //crea objeto controlador 
            $controlador=new ControladorCajas($req);

            // si se pasa el numeor de la caja se busca dicha caja 
            if (isset($_POST['numcaja'])) {

                $numcaja=$_POST['numcaja'];
                // regresa el resultado de la buqueda como un objeto JSON
                if (isset($_POST['estado'])) {
                    switch ($_POST['estado']) {
                        
                        case 5:
                            $respuesta=$controlador->ctrBuscarItemError($numcaja);
                            break;
                        default:
                            $respuesta=$controlador->ctrBuscarItemCaja($numcaja);
                            break;
                    }
                }
                
                
            // si no se paso el numero de la caja busca todas las cajas de la requisicion  seleccionada
            }else{
                $numcaja='%%';
                // regresa el resultado de la buqueda como un objeto JSON
                $respuesta=$controlador->ctrBuscarCaja($numcaja);
                
            }


            // muestra el vector como dato JSON

            print json_encode($respuesta);
            return 1;
            break;
        
        /* ============================================================================================================================
                                                CIERRA 1 CAJA
        ============================================================================================================================*/    
        case "cerrar":
            // obtienen los datos dela requisicion (numero requisicion y codigo alistador)
            $req=$_POST['req'];

            //crea objeto controlador 
            $controlador=new ControladorCajas($req);

            // si se pasa el numeor de la caja se busca dicha caja 
            
            $caja=$_POST['caja'];
            $items=$_POST['items'];
            // print json_encode($items);
            // return 0;
            $respuesta=$controlador->ctrCerrarCaja($items,$caja['tipocaja'],$caja['pesocaja'],$caja['no_caja']);            
            
            // muestra el vector como dato JSON
            print json_encode($respuesta);
            return 1;
            break;

        /* ============================================================================================================================
                                                MUESTRA CONDUCTORES O TRANSPORTADORES DISPONIBLES
        ============================================================================================================================*/
        case "conductor":
            $modelo=new Conexion();
            $busqueda=$modelo->buscaritem('usuario','perfil',6);
            
            $cont=0;
            
            if ($busqueda->rowCount()>0) {
                $resultado["estado"]="encontrado";
            
                while($row = $busqueda->fetch()){
                    
                    
                    $resultado["contenido"][$row["id_usuario"]]=$row["nombre"];
                    $cont++;

                }
            }else {
                $resultado["estado"]=false;
                $resultado["contenido"]="No se encontraron usuarios";
            }
            print json_encode($resultado);
            break;

        /* ============================================================================================================================
                                                ASIGNA LAS CAJAS TRANSPORTADOR
        ============================================================================================================================*/
        case "despachar":
            $transportador=$_POST['transportador'];
            $cajas=$_POST['cajas'];
            
            $controlador=new ControladorCajas();
            $busqueda=$controlador->ctrDespacharCajas($cajas,$transportador);
            print json_encode($busqueda);
            return 1;
            break;
        
        /* ============================================================================================================================
                                                CREA DOCUMENTO PLANO 
        ============================================================================================================================*/
        case "documento":
           // obtienen los datos dela requisicion (numero requisicion y codigo alistador)
            $req=$_REQUEST["req"];
            $numcaja=$_REQUEST['numcaja'];
            
            $controlador=new ControladorCajas($req);

            // si el documento creaodo corresponde al de salida de bodega
            if (!isset($_POST['recibido'])) {
                $resultado=$controlador->ctrDocumento($numcaja);
            
            // si el documento creado corresponde al del punto de venta
            }else{
                $controlador=new ControladorPV($req);
                // print json_encode("recibido");
                // return  0;
                $resultado=$controlador->ctrDocumentoR($numcaja);
            }
            print json_encode($resultado); 
            return  1;
            break;
        /* ============================================================================================================================
                                                CREA DOCUMENTO PLANO ITEMS RECIBIDOS
        ============================================================================================================================*/
        case "documentoR":
           // obtienen los datos dela requisicion (numero requisicion y codigo alistador)
            $req=$_REQUEST["req"];
            $numcaja=$_REQUEST['numcaja'];
            
            $controlador=new ControladorPV($req);
            // print json_encode("recibido");
            // return  0;
            $resultado=$controlador->ctrDocumentoRR($numcaja);
        
            print json_encode($resultado); 
            return  1;
            break;
        /* ============================================================================================================================
                                                CREA DOCUMENTO PLANO DE LA CAJA
        ============================================================================================================================*/
        case "eliminar":
            // obtienen los datos dela requisicion (numero requisicion y codigo alistador)
            $req=$_POST["req"];
            $numcaja=$_POST['numcaja'];


            //crea objeto controlador 
            $controlador=new ControladorCajas($req);

            $resultado=$controlador->ctrEliminar($numcaja);


            print json_encode($resultado);
            break;

        /* ============================================================================================================================
                                                MUESTRA LAS CAJAS O LOS ITEMS DE LA CAJA
        ============================================================================================================================*/
        case "modificar":
            // obtienen los datos dela requisicion (numero requisicion y codigo alistador)
            $req=$_POST['req'];
            $numcaja=$_POST['numcaja'];
            $items=$_POST['items'];
            //crea objeto controlador 
            $controlador=new ControladorCajas($req);

            $resultado=$controlador->ctrModificarCaja($numcaja,$items);

            print json_encode($resultado);
            return 1;
            break;
        
        /* ============================================================================================================================
                                                MUESTRA LAS CAJAS O LOS ITEMS DE LA CAJA
        ============================================================================================================================*/
        case "pvcajas":
            // obtienen los datos dela requisicion (numero requisicion y codigo alistador)
            $req=$_POST['req'];

            //crea objeto controlador 
            $controlador=new ControladorPV($req);

            // si se pasa el numeor de la caja se busca dicha caja 
            if (isset($_POST['numcaja'])) {

                $numcaja=$_POST['numcaja'];
                // regresa el resultado de la buqueda como un objeto JSON
                if (isset($_POST['estado'])) {
                                
                    $respuesta=$controlador->ctrBuscarItemrec($numcaja);
                    
                }
                
                
            // si no se paso el numero de la caja busca todas las cajas de la requisicion  seleccionada
            }else{
                $numcaja='%%';
                // regresa el resultado de la buqueda como un objeto JSON
                $respuesta=$controlador->ctrBuscarCaja($numcaja,3);
                // print json_encode($algo);
            }


            // muestra el vector como dato JSON

            print json_encode($respuesta);
            return 1;
            break;

    }
    
}