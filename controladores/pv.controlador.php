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
        
        
        // agrega los datos en la tabla de recibidos
        $resultado["estado"]=$this->modelo->mdlRegistrarItems($items,$numcaja);

        
        //si registra los items, modifica la tabla de pedido para que la caja aperezca como recibida
        if ($resultado==true) {
            $resultado["estado"]=$this->modelo->mdlModCaja($numcaja);
            if($resultado["estado"]==true){
                
                $resultado["contenido"]=$this->ctrVerificarRegistro($numcaja);
                //si hay errores en los items recibido en la caja se cambia el estado de la caja
                if ($resultado["contenido"]["estado"]!="ok" ) {
                    $resultado["estado"]=$this->modelo->mdlModCaja($numcaja,5);
                }else {
                    $resultado["contenido"]=$this->ctrDocumentoR($numcaja);
                }
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
    private function ctrVerificarRegistro($numcaja){
        
        if ($this->modelo->mdlVerificarCaja($numcaja)) {             

            $busqueda=$this->modelo->mdlMostrarItemsRec($numcaja);

            $resultado["estado"]="ok";
            if ($busqueda->rowCount()>0) {
                $recibidos=$busqueda->fetchAll();
                $i=0;
                foreach ($recibidos as $row) {

                    switch ($row["rec_estado"]) {
                        
                        case 0:
                            
                            if ($row["recibidos"]==0) {

                                $mensajeitem="item no recibido";
                            }else {
                                
                                $mensajeitem="Se recibieron menos items, recibidos: ".$row["recibidos"]." alistados: ".$row["alistado"];
                            }
                            break;

                        case 1:
                            
                            $mensajeitem="Se recibieron mas items, recibidos: ".$row["recibidos"]." alistados: ".$row["alistado"];                        
                            break;

                        case 2:
                            
                            $mensajeitem="El item  recibido no estaba en la requisicion";
                            break;

                        case 3:
                            
                            if ($row["cajap"]==1) {
                                $mensajeitem="El item recibido no está alistado en niguna caja";
                            }else{
                                $mensajeitem="El item recibido fue alistado en la caja ".$row["cajap"]." y recibido en la caja ".$row["cajar"];
                            }
                            break;
                        
                        default:
                            // $resultado["item"][$i]["descripcion"]=$row["DESCRIPCION"];
                            // $resultado["item"][$i]["iditem"]=$row["item"];
                            // $i++;
                            break;
                    }
                    if ($row["rec_estado"] != 4) {
                        $resultado["estado"]="error0";
                        $resultado["item"][$i]=$row;
                        $resultado["item"][$i]["mensaje"]=$mensajeitem;
                        $i++;
                    }
                    
                }
            }else {
                $resultado["estado"]="error1";
            }
        }
        return $resultado;
        
    }

    public function ctrBuscarItemrec($numcaja)
    {
         $busqueda=$this->modelo->mdlMostrarItemsRec($numcaja);
          
        if ($busqueda->rowCount() > 0) {

            $itembus["estado"]="encontrado";

            $itembus["contenido"]=$busqueda->fetchAll();
            // $cont=0;

            // while($row = $busqueda->fetch()){

            //     $itembus["contenido"][$cont]=["codigo"=>$row["ID_CODBAR"],
            //                         "iditem"=>$row["item"],    
            //                         "referencia"=>$row["ID_REFERENCIA"],
            //                         "descripcion"=>$row["DESCRIPCION"],
            //                         "disponibilidad"=>$row["disp"],
            //                         "pedidos"=>$row["pedido"],
            //                         "alistados"=>$row["alistado"],
            //                         'ubicacion'=>$row["ubicacion"],
            //                         'origen'=>$row["lo_origen"],
            //                         'destino'=>$row["lo_destino"]
            //                         ];
            //     $cont++;

            // }

            
            
            return $itembus;

        //si no encuentra resultados devuelve "error"
        }else{

            return ['estado'=>"error",
                    'contenido'=>"Caja sin Items!"];

        }
    }
    
    public function ctrDocumentoR($numcaja)
    {
        $busqueda=$this->modelo->mdlMostrarrecibidos($numcaja);

        $resultado["estado"]=true;
        if ($busqueda->rowCount()>0) {
            $recibidos=$busqueda->fetchAll();
            $i=0;
            $resultado["string"]="";
            foreach ($recibidos as $row) {
                $mensaje=str_pad($row["no_caja"],19,"0",STR_PAD_LEFT);

                $origen=str_replace("BD","",$row["lo_origen"]);
                $destino=str_replace("VE","",$row["lo_destino"]);
                $origen=$origen.substr($destino,1,-1);
                $localicacion=str_replace("-","",$origen.$row["lo_destino"]."I");
                $localicacion=str_pad($localicacion,11+15," ",STR_PAD_RIGHT);
                $item=str_pad($row["iditem"],13+12," ",STR_PAD_RIGHT);
                $num=$row["recibidos"]*1000;
                $alistado=str_pad($num,12,"0",STR_PAD_LEFT);
                $alistado=str_pad($alistado,12+32," ",STR_PAD_RIGHT);
                
                // $busqueda=$this->modelo->buscaritem('usuario','id_usuario',$this->req[1]);
                // $busqueda=$busqueda->fetch();
                // $mensaje=substr($busqueda['nombre'],0,19);
                
                $resultado["string"].=($localicacion.$item.$alistado.$mensaje."\r\n");
            }
        }else {
            $resultado["estado"]=false;
        }
        
        return $resultado;
    }

    
    
}