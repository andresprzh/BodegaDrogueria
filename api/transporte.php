<?php

require "cors.php";

if (isset($_GET['ruta'])) {
    

    switch ($_GET['ruta']) {
        
        /* ============================================================================================================================
                                        MUESTRA LOS DESTINOS ASIGNADOS AL TRANSPORTADOR 
        ============================================================================================================================*/   
        case "destinos":
            $usuario=$_GET['usuario'];
            $modelo=new ModeloTransportador($usuario);
            $busqueda=$modelo->mdlMostrarDestinos('usuario','perfil',6);
            
            
            if ($busqueda->rowCount()>0) {

                $resultado["estado"]="encontrado";
                $resultado["contenido"]=$busqueda->fetchAll();

            }else {

                $resultado["estado"]=false;
                $resultado["contenido"]="No se encontraron ubicaciones";

            }
            print json_encode($resultado);
            break;
        /* ============================================================================================================================
                                       MUESTRA EL PEDIDO(CAJAS) ASIGNADAS AL TRANSPORTADOR
        ============================================================================================================================*/   
        case "pedidos":
            $usuario=$_GET["usuario"];
            $controlador= new ControladorTransportador($usuario);
            $resultado=$controlador->ctrBuscarPedidos();
            print json_encode($resultado);
            break;
        /* ============================================================================================================================
                                ENTREGA ITEMS AL PUNTO DE VENTA CAMBIANDO EL ESTADO DE LA CAJA A ENTREGADO
        ============================================================================================================================*/   
        case "entregar":
            $cajas=$_POST["cajas"];
            $controlador= new ControladorTransportador();
            $resultado=$controlador->ctrEntregarCajas($cajas);
            print json_encode($resultado);
            break;

    }
    
}