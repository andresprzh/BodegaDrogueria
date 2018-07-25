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
    public function ctrBuscarItemPV($Cod_Bar)
    {
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

    public function ctrBuscarCajaPV($NumCaja)
    {
        $busqueda=$this->ctrBuscarCaja($NumCaja);

        $cajabus['estado']=$busqueda['estado'];

        if ($busqueda['estado']=='encontrado') {

            // en caso de que solo sea 1 resultado
            if(count($busqueda["contenido"]) == 1){              

                // solo tiene en cuenta cajas que ya han sido cerradas
                if ($busqueda['contenido']["cerrar"]!=null) {
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
                    if ($row["cerrar"]!=null) {
                        
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
    
}