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
    function __construct($Req) {
        
        parent::__construct($Req);
        $this->modelo=new ModeloPV($Req);

    }

    /* ============================================================================================================================
                                                        FUNCIONES   
    ============================================================================================================================*/

    // busca el item en la requisicion
    public function ctrBuscarItemPV($Cod_Bar){
        $busqueda=$this->modelo->mdlMostrarItemPV($Cod_Bar);

        if ($busqueda->rowCount() > 0) {

            $row = $busqueda->fetch();
            $item=["estado"=>"encontrado",
            "contenido"=> ["codigo"=>$row["ID_CODBAR"],
                            "referencia"=>$row["ID_REFERENCIA"],
                            "descripcion"=>$row["DESCRIPCION"],
                          ]
            ];

        }else {
            $item['estado']='error';
            $item['contenido']='Item no encontrado';
        }

        return $item;
    }
    // busca cajas visibles para el punto de venta
    public function ctrBuscarCajaPV($NumCaja){
        $busqueda=$this->modelo->mdlMostrarCajaPV($NumCaja);
        
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

            $cajabus= ['estado'=>"error",
                    'contenido'=>"Caja no encontrado en la base de datos!"];

        }
        // libera conexion para hace otra sentencia
        $busqueda->closeCursor();
        return $cajabus;
    }


    public function ctrRegistrarItems($Items,$NumCaja){   
        $i=0;
        // busca el id del item usando el codigo de barras de cada item
        foreach ($Items as $row) {

            $busqueda=$this->modelo->mdlMostrarItemPV($row['codbarras']);
            $IDitem = $busqueda->fetch();
            $Items[$i]['item']=$IDitem['ID_ITEM'];
            $i++;
        }
        // libera conexion para hace otra sentencia
        $busqueda->closeCursor();

        // agrega los datos en la datbla de recibidos
        $resultado['estado']=$this->modelo->mdlRegistrarItems($Items,$NumCaja);

        // libera conexion para hace otra sentencia
        
        //si registra los items, modifica la tabla de pedido para que la caja aperezca como recibida
        if ($resultado) {
            $resultado['estado']=$this->modelo->mdlModCaja($NumCaja);
            if($resultado['estado']){
                $resultado['contenido']=$this->ctrDocumentoR($NumCaja);
            }
        }
        
        return $resultado;
        
    }

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
            $requisicion['contenido']='Requisicion no encontrada';
        }
        // libera conexion para hace otra sentencia
        $busqueda->closeCursor();
        return $requisicion;
    }

    // crea archivo plano de la caja recibida
    private function ctrDocumentoR($NumCaja){
        $busqueda=$this->modelo->mdlMostrarItemsRec($NumCaja);

        
        if ($busqueda->rowCount()>0) {
            $recibidos=$busqueda->fetchAll();
            $i=0;
            $string='';
            foreach ($recibidos as $row) {

                $origen=str_replace('BD','',$row["lo_origen"]);
                $destino=str_replace('VE','',$row["lo_destino"]);
                $origen=$origen.substr($destino,1,-1);
                $localicacion=str_replace('-','',$origen.$row["lo_destino"].'I');
                $localicacion=str_pad($localicacion,11+15," ",STR_PAD_RIGHT);
                $codigo=str_pad($row["ID_CODBAR"],13+15," ",STR_PAD_RIGHT);
                $num=$row["recibidos"]*1000;
                $alistado=str_pad($num,12,'0',STR_PAD_LEFT);
                $alistado=str_pad($alistado,12+32," ",STR_PAD_RIGHT);
                

                switch ($row['estado']) {
                    case 0:
                        
                        if ($row['recibidos']==0) {
                            $mensaje='Item no Recibido';
                        }else {
                            $mensaje='Menos items';
                        }
                        break;

                    case 1:
                        $mensaje='Mas items';
                        break;

                    case 2:
                        $mensaje='Caja diferente';
                        break;

                    case 3:
                        $mensaje='Req diferente';
                        break;
                    
                    default:
                        $mensaje='Ok';
                        break;
                }
                
                $string.=($localicacion.$codigo.$alistado.$mensaje."\n");
            }
        }else {
            $string='error';
        }

        return $string;
    }
    
    
}