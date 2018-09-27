<?php

// include "../controladores/alistar.controlador.php";
include "../controladores/cajas.controlador.php";


require "../modelos/conexion.php";
require "../modelos/alistar.modelo.php";
require "../modelos/requerir.modelo.php";
require "../modelos/cajas.modelo.php";

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
                        case 9:
                            $respuesta=$controlador->ctrBuscarItemCancelados($numcaja);
                            break;
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
            return 0;
            break;
        
        /* ============================================================================================================================
                                                CREA DOCUMENTO PLANO DE LA CAJA
        ============================================================================================================================*/
        case "documento":
           // obtienen los datos dela requisicion (numero requisicion y codigo alistador)
            $req=$_POST["req"];
            $numcaja=$_POST['numcaja'];
            $items=$_POST['items'];

            //crea objeto controlador 
            $controlador=new ControladorCajas($req);

            $resultado=$controlador->ctrDocumento($items,$numcaja);


            print json_encode($resultado);
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

            $resultado=$controlador->ctrCancelar($numcaja);


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
        
    }
    
}