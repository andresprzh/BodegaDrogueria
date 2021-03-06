<?php

require "cors.php";

if (isset($_GET['ruta'])) {
    

    switch ($_GET['ruta']) {

        /* ============================================================================================================================
                                                CREA CAJA NUEVA PARA EL USUARIO
        ============================================================================================================================*/
        case 'cajas':

            // obtienen los datos de la requisicion (numero requisicion y codigo alistador)
            $req=$_POST["req"];
            //crea objeto controlador 
            $controlador=new ControladorAlistar($req);


            //crea caja nueva para el usuario
            $respuesta=$controlador->ctrCrearCaja();


            
            print json_encode( $respuesta);
            
            return 1;

            break;

        /* ============================================================================================================================
                                                ELIMINA 1 ITEM DE LA CAJA
        ============================================================================================================================*/
        case 'eliminaritem':
            $cod_barras=$_POST['iditem'];

            $req=$_POST["req"];
            
            //crea objeto controlador 
            $controlador=new ControladorAlistar($req);
            
            $resultado=$controlador->ctrEliminarItemCaja($cod_barras);
            
            print json_encode($resultado);
            
            return 1;

            break;

        /* ============================================================================================================================
                                                CIERRA LA CAJA
        ============================================================================================================================*/
        case 'empacar':
            // obtienen los datos dela requisicion (numero requisicion y codigo alistador)
            $req=$_POST['req'];
            
            $tipocaja=$_POST['tipocaja'];
            $pesocaja=$_POST['pesocaja'];
            $items=$_POST['items'];
            
            //crea objeto controlador 
            $controlador=new ControladorAlistar($req);
                  
            $respuesta=$controlador->ctrCerrarCaja($items,$tipocaja,$pesocaja);
            // $respuesta=false;
            

            if ($respuesta) {
                $resultado=$controlador->ctrDocList();
                $resultado['estado']=true;
                // print json_encode($resultado);
            }else{
                $resultado=false;
            }
            
            print json_encode($resultado);
            return 1;
            
            break;

        /* ============================================================================================================================
                                                MUESTRA LOS ITEMS DELA REQUISICION 
        ============================================================================================================================*/
        case 'items':
            
            // obtienen los datos dela requisicion (numero requisicion y codigo alistador)
            $req=$_REQUEST['req'];
            
            // BUSCA TODOS LOS ITEMS DE LA REQUISICION
            if ($_SERVER['REQUEST_METHOD']==='GET') {
                
                
                $controlador=new ControladorAlistar($req);

                //si se buca un item en especifico
                if (isset($_GET['codigo'])) {

                    $cod_barras=$_GET['codigo'];
                    $respuesta=$controlador->ctrBuscarItem($cod_barras);
                // de lo contrario se muestran todos los items de la requisicion
                }else {
                    // si busca un estado de item especifico 
                    if (isset($_GET['estado'])) {
                        $respuesta=$controlador->ctrBuscarItemsReq(true);
                    }else {
                        // de lo contratio solo buscan los items no alistados
                        $respuesta=$controlador->ctrBuscarItemsReq();
                    }
                    
                }
                
                print json_encode( $respuesta);

                
                return 1;

            // BUSCA UN ITEM ESPECIFICO DE LA REQUISICION
            }else{
                
                $controlador=new ControladorAlistar($req);


                $item=$_POST['item'];

                
                $respuesta=$controlador->ctrAlistarItem($item);

                
                print json_encode( $respuesta);

                
                return 1;
            }
            break;

        /* ============================================================================================================================
                                                BUSCA LAS REQUISICIONES
        ============================================================================================================================*/
        case 'requisiciones':
            $modelo=new ModeloRequierir();
            $item='estado';
            if (isset($_REQUEST['valor'])) {
                $res=$modelo->mdlMostrarReq($item,$_REQUEST['valor']);
            }else{
                $res=$modelo->mdlMostrarReq($item);
                
            }
            $resultado=$res->fetchAll();

            print json_encode($resultado);
            return 0;
            break;

        /* ============================================================================================================================
                                                CAMBIA ESTADO DE LA REQUISICION
        ============================================================================================================================*/
        case 'terminarreq':
            $req=$_POST['req'];
            $controlador=new ControladorAlistar();
            $resultado=$controlador->ctrTerminarreq($req);
            print json_encode($resultado);
            return 1;
            break;
        
            
        /* ============================================================================================================================
                                                CREA DOCUMENTO LISTA DE ITEMS Y LO MANDA A IMPRIMIR
        ============================================================================================================================*/  
        case 'listadoc':
             
            $req=$_GET['req'];
            $numcaja=$_GET['numcaja'];
            
            
            $controlador=new ControladorAlistar($req);
            
            $resultado=$controlador->ctrDocList($numcaja);
            
            print json_encode($resultado);
            break;    
        /* ============================================================================================================================
                                                                    DEFAULT
        ============================================================================================================================*/  
        default:
            print json_encode("Best REST API :D");
            
            return 1;
            break;
    }
}
