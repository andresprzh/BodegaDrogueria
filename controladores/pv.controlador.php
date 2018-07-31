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
        $busqueda=$this->ctrBuscarCaja($NumCaja);

        $cajabus['estado']=$busqueda['estado'];

        if ($busqueda['estado']=='encontrado') {

            // en caso de que solo sea 1 resultado
            if(count($busqueda["contenido"]) == 1){              

                // solo tiene en cuenta cajas que ya han sido cerradas
                if ($busqueda['contenido']["cerrar"]!=null && $busqueda['contenido']["recibido"]==null) {
                    //guarda los resultados en un arreglo
                    $cajabus=["estado"=>"encontrado",
                    "contenido"=> ["no_caja"=>$busqueda['contenido']["no_caja"],
                                    "alistador"=>$busqueda['contenido']["alistador"],
                                    "tipocaja"=>$busqueda['contenido']["tipocaja"],
                                    "abrir"=>$busqueda['contenido']["abrir"],
                                    "cerrar"=>$busqueda['contenido']["cerrar"],
                                ]
                    ];
                
                //retorna el item a la funcion
                return $cajabus;

                } 
                
            // si son 2 o mas resultados    
            }else {

                $cont=0;

                foreach($busqueda['contenido'] as $row){
                    // solo tiene en cuenta cajas que ya han sido cerradas
                    if ($row["cerrar"]!=null && $row["recibido"]==null) {
                        
                        $cajabus["contenido"][$cont]=["no_caja"=>$row["no_caja"],
                                                        "alistador"=>$row["alistador"],
                                                        "tipocaja"=>$row["tipocaja"],
                                                        "abrir"=>$row["abrir"],
                                                        "cerrar"=>$row["cerrar"],
                                                    ];
                        
                        $cont++;
                    }

                    // si hay al menos un resultado en la busqueda
                    if (count($cajabus['contenido'])>0) {
                        $cajabus["estado"]="encontrado";
                    }

                }
                

            }
        }else {
            $cajabus=$busqueda;
        }

       
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
        $resultado=$this->modelo->mdlRegistrarItems($Items,$NumCaja);

        // libera conexion para hace otra sentencia
        
        //si registra los items modifica la tabla de pedido para que la caja aperezca como recibida
        if ($resultado) {
            $resultado=$this->modelo->mdlModCaja($NumCaja);
            if($resultado){
                $resultado=$this->ctrDocumentoR($NumCaja);
            }
        }
        
        return ($resultado);
        
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
            $recibidos=$busqueda->fetch();
            $i=0;
            foreach ($recibidos as $row) {
                $localicacion=str_replace('-','',$row["lo_origen"].$row["lo_destino"].'I');
                $localicacion=str_pad($localicacion,11+15," ",STR_PAD_RIGHT);
                $codigo=str_pad($row["ID_CODBAR"],13+15," ",STR_PAD_RIGHT);
                $num=$row["recibidos"]*1000;
                $alistado=str_pad($num,12,'0',STR_PAD_LEFT);
                $alistado=str_pad($alistado,12+32," ",STR_PAD_RIGHT);
                $Mensaje='---';
                
                $string.=($localicacion.$codigo.$alistado.$Mensaje."\n");
            }
        }else {
            $string='error';
        }

        return $string;
    }
    
    
}