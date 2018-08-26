<?php
include "cajas.controlador.php";


class ControladorPV extends ControladorCajas{
    /* ============================================================================================================================
                                                        ATRIBUTOS   
    ============================================================================================================================*/
    
    private $modelo;

    /* ============================================================================================================================
                                                        CONSTRUCTOR   
    ============================================================================================================================*/
    function __construct($req) {
        
        parent::__construct($req);
        $this->modelo=new ModeloPV($req);

    }

    /* ============================================================================================================================
                                                        FUNCIONES   
    ============================================================================================================================*/

    // busca el item en la requisicion
    public function ctrBuscarItemPV($cod_bar){
        $busqueda=$this->modelo->mdlMostrarItemPV($cod_bar);

        if ($busqueda->rowCount() > 0) {

            $row = $busqueda->fetch();
            $item=["estado"=>"encontrado",
            "contenido"=> ["codigo"=>$row["ID_CODBAR"],
                            "iditem"=>$row["ID_ITEM"],
                            "referencia"=>$row["ID_REFERENCIA"],
                            "descripcion"=>$row["DESCRIPCION"],
                          ]
            ];

        }else {
            $item["estado"]="error";
            $item["contenido"]="Item no encontrado";
        }

        return $item;
    }


    // busca cajas visibles para el punto de venta
    public function ctrBuscarCajaPV($numcaja){
        $busqueda=$this->modelo->mdlMostrarCajaPV($numcaja);
        
        if ($busqueda->rowCount() > 0) {

            if($busqueda->rowCount() == 1){

                $row = $busqueda->fetch();

                //guarda los resultados en un arreglo
                $cajabus=["estado"=>"encontrado",
                           "contenido"=> ["no_caja"=>$row["no_caja"],
                                           "alistador"=>$row["nombre"],
                                           "tipocaja"=>$row["tipo_caja"],
                                           "abrir"=>$row["abrir"],
                                           "cerrar"=>$row["cerrar"],
                                           "recibido"=>$row["recibido"],
                                         ]
                         ];

                
                //retorna el item a la funcion
                return $cajabus;

            }else {

                $cajabus["estado"]="encontrado";

                $cont=0;

                while($row = $busqueda->fetch()){

                    //Muestra todas las cajas
                    $cajabus["contenido"][$cont]=["no_caja"=>$row["no_caja"],
                                                    "alistador"=>$row["nombre"],
                                                    "tipocaja"=>$row["tipo_caja"],
                                                    "abrir"=>$row["abrir"],
                                                    "cerrar"=>$row["cerrar"],
                                                    "recibido"=>$row["recibido"],
                                                ];

                    
                    $cont++;

                }
                

            }

        //si no encuentra resultados devuelve "error"
        }else{

            $cajabus= ["estado"=>"error",
                    "contenido"=>"Caja no encontrado en la base de datos!"];

        }
        // libera conexion para hace otra sentencia
        $busqueda->closeCursor();
        return $cajabus;
    }

    // modifca los valores de los items en la tabla y luego genera el documento
    public function ctrRegistrarItems($items,$numcaja){   
        
        
        // agrega los datos en la datbla de recibidos
        $resultado["estado"]=$this->modelo->mdlRegistrarItems($items,$numcaja);

        
        //si registra los items, modifica la tabla de pedido para que la caja aperezca como recibida
        if ($resultado==true) {
            $resultado["estado"]=$this->modelo->mdlModCaja($numcaja);
            if($resultado["estado"]==true){
                $resultado["contenido"]=$this->ctrDocumentoR($numcaja);
            }
        }
        
        return $resultado;
        
    }

    // busca la srequisiciones
    public function ctrBuscarReq(){
        
        $busqueda=$this->modelo->mdlMostrarReq();

        if ($busqueda->rowCount() > 0) {

            $row = $busqueda->fetch();
            $requisicion=["estado"=>"encontrado",
            "contenido"=> ["no_req"=>$row["no_req"],
                            "creada"=>$row["creada"],
                            "origen"=>$row["lo_origen"],
                            "destino"=>$row["lo_destino"],
                          ]
            ];

        }else {
            $requisicion['estado']='error';
            $requisicion['contenido']='requisicion no encontrada';
        }
        // libera conexion para hace otra sentencia
        $busqueda->closeCursor();
        return $requisicion;
    }

    // crea archivo plano de la caja recibida
    private function ctrDocumentoR($numcaja){
        $busqueda=$this->modelo->mdlMostrarItemsRec($numcaja);

        $resultado["estado"]="ok";
        if ($busqueda->rowCount()>0) {
            $recibidos=$busqueda->fetchAll();
            $i=0;
            $resultado["string"]="";
            foreach ($recibidos as $row) {

                $origen=str_replace("BD","",$row["lo_origen"]);
                $destino=str_replace("VE","",$row["lo_destino"]);
                $origen=$origen.substr($destino,1,-1);
                $localicacion=str_replace("-","",$origen.$row["lo_destino"]."C");
                $localicacion=str_pad($localicacion,11+15," ",STR_PAD_RIGHT);
                $item=str_pad($row["item"],13+15," ",STR_PAD_RIGHT);
                $num=$row["recibidos"]*1000;
                $alistado=str_pad($num,12,"0",STR_PAD_LEFT);
                $alistado=str_pad($alistado,12+32," ",STR_PAD_RIGHT);
                
                switch ($row["rec_estado"]) {
                    
                    case 0:
                        
                        if ($row["recibidos"]==0) {
                            $mensaje="Item no recibido";
                            $mensajeitem="item no recibido";
                        }else {
                            $mensaje="Menos items";
                            $mensajeitem="Se recibieron menos items, recibidos: ".$row["recibidos"]." alistados: ".$row["alistado"];
                        }
                        break;

                    case 1:
                        $mensaje="Mas items";
                        $mensajeitem="Se recibieron mas items, recibidos: ".$row["recibidos"]." alistados: ".$row["alistado"];                        
                        break;

                    case 2:
                        $mensaje="Req diferente";
                        $mensajeitem="El item  recibido no estaba en la requisicion";
                        break;

                    case 3:
                        $mensaje="Caja diferente";
                        $mensajeitem="Se recibieron mas items, recibidos: ".$row["recibidos"]." alistados: ".$row["alistado"];
                        break;
                    
                    default:
                        $mensaje="Ok";
                        break;
                }
                if ($row["rec_estado"] != 4) {
                    $resultado["estado"]="error0";
                    $resultado["item"][$i]["descripcion"]=$row["DESCRIPCION"];
                    $resultado["item"][$i]["id"]=$row["item"];
                    $resultado["item"][$i]["mensaje"]=$mensajeitem;
                    $i++;
                }
                
                $resultado["string"].=($localicacion.$item.$alistado.$mensaje."\n");
            }
        }else {
            $resultado["estado"]="error1";
        }

        return $resultado;
    }
    
    
}