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

                return $cajabus;

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
        // $resultado->closeCursor();
        //si registra los tems modifica la tabla de pedido para que la caja aperesca como recibida
        if ($resultado) {
            $resultado=$this->modelo->mdlModCaja($NumCaja);
        }
        
        return ($resultado);
        
    }

    // crea archivo plano de la caja recibida
    private function ctrDocumentoR(){
        # code...
    }
    
    
}