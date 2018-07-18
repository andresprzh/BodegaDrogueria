<?php
include "alistar.controlador.php";

class ControladorCajas extends ControladorAlistar {
    /* ============================================================================================================================
                                                        ATRIBUTOS   
    ============================================================================================================================*/
    
    private $modelo;

    /* ============================================================================================================================
                                                        CONSTRUCTOR   
    ============================================================================================================================*/
    function __construct($Req) {

        parent::__construct($Req);
        $this->modelo=new ModeloCaja($Req);

    }

    /* ============================================================================================================================
                                                        FUNCIONES   
    ============================================================================================================================*/
    public function ctrBuscarCaja($NumCaja){

        $busqueda=$this->modelo->mslMostrarCaja($NumCaja);

        if ($busqueda->rowCount() > 0) {

            if($busqueda->rowCount() == 1){

                $row = $busqueda->fetch();

                //guarda los resultados en un arreglo
                $cajabus=["estado"=>"Encontrado",
                           "contenido"=> ["no_caja"=>$row["No_caja"],
                                           "alistador"=>$row["nombre"],
                                           "tipocaja"=>$row["tipo_caja"],
                                           "abrir"=>$row["abrir"],
                                           "cerrar"=>$row["cerrar"],
                                         ]
                         ];
                
                //retorna el item a la funcion
                return $cajabus;

            }else {

                $cajabus["estado"]=["encontrado"];

                $cont=0;

                while($row = $busqueda->fetch()){

                    //Muestra todas las cajas
                    $cajabus["contenido"][$cont]=["no_caja"=>$row["No_caja"],
                                                    "alistador"=>$row["nombre"],
                                                    "tipocaja"=>$row["tipo_caja"],
                                                    "abrir"=>$row["abrir"],
                                                    "cerrar"=>$row["cerrar"],
                                                ];
                    
                    $cont++;

                }

                return $cajabus;

            }

        //si no encuentra resultados devuelve "error"
        }else{

            return ['estado'=>"error",
                    'contenido'=>"Caja no encontrado en la base de datos!"];

        }

    }
    
    public function ctrBuscarItemCaja($NumCaja){
        
        $busqueda=$this->modelo->mslMostrarItemsCaja($NumCaja);

        if ($busqueda->rowCount() > 0) {

            $itembus["estado"]=["encontrado"];

            $cont=0;

            while($row = $busqueda->fetch()){
              
                $itembus["contenido"][$cont]=["codigo"=>$row["ID_CODBAR"],
                                    "referencia"=>$row["id_referencia"],
                                    "descripcion"=>$row["descripcion"],
                                    "disponibilidad"=>$row["disp"],
                                    "pedidos"=>$row["pedido"],
                                    "alistados"=>$row["alistado"],
                                    'ubicacion'=>$row["ubicacion"]
                                    ];
                
                $cont++;

            }
            
            return $itembus;

        //si no encuentra resultados devuelve "error"
        }else{

            return ['estado'=>"error",
                    'contenido'=>"Caja sin Items!"];

        }

    }

    public function ctrDocumento(){
        # code...
    }

    public function ctrBuscarIE(){
        # code...
    }

    public function ctrAgregarIE(){
        # code...
    }
}
