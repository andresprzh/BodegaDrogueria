<?php

include "../controladores/pv.controlador.php";



require "../modelos/conexion.php";
require "../modelos/pv.modelo.php";
require "../modelos/cajas.modelo.php";
require "../modelos/alistar.modelo.php";

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
            $req=$_POST["req"];
            $controlador=new ControladorPV($req);
            $codigo=$_POST["codigo"];


            $busqueda=$controlador->ctrBuscarItemPV($codigo);

            print json_encode($busqueda);
            break;
        
        /* ============================================================================================================================
                                                REGISTRA LOS ITEMS DE LA CAJA Y HACE EL INFORME
        ============================================================================================================================*/
        case "registrar":

            $req=$_POST['req'];
            $items=$_POST['items'];
            $numcaja=$_POST['caja'];
            
            $controlador=new ControladorPV($req);
            
            $resultado=$controlador->ctrRegistrarItems($items,$numcaja);
            
            print json_encode($resultado);

            break;
            
    }

}