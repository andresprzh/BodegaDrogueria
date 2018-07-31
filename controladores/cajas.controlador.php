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

        $busqueda=$this->modelo->mdlMostrarCaja($NumCaja);

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

                return $cajabus;

            }

        //si no encuentra resultados devuelve "error"
        }else{

            return ['estado'=>"error",
                    'contenido'=>"Caja no encontrado en la base de datos!"];

        }

    }
    // crea documento de texto
    public function ctrDocumento($Items){

        // $busqueda=$this->ctrBuscarItemCaja($NumCaja);

        // if ($busqueda["estado"]=="encontrado") {
        //     $string='';
        //     foreach($busqueda["contenido"] as $row){
    
               
        //         $localicacion=str_replace('-','',$row["origen"].$row["destino"].'I');
        //         $localicacion=str_pad($localicacion,11+15," ",STR_PAD_RIGHT);
        //         $codigo=str_pad($row["codigo"],13+15," ",STR_PAD_RIGHT);
        //         $num=$row["alistados"]*1000;
        //         $alistado=str_pad($num,12,'0',STR_PAD_LEFT);
        //         $alistado=str_pad($alistado,12+32," ",STR_PAD_RIGHT);
        //         $Mensaje="algo";
                
                    
        //         $string.=($localicacion.$codigo.$alistado.$Mensaje."\n");
                
                
        //     }
            
        //     return $string;
            
        // }else{
        //     return "error";
        // }

        foreach($Items as $row){

            $localicacion=str_replace('-','',$row["origen"].$row["destino"].'I');
            $localicacion=str_pad($localicacion,11+15," ",STR_PAD_RIGHT);
            $codigo=str_pad($row["codigo"],13+15," ",STR_PAD_RIGHT);
            $num=$row["alistados"]*1000;
            $alistado=str_pad($num,12,'0',STR_PAD_LEFT);
            $alistado=str_pad($alistado,12+32," ",STR_PAD_RIGHT);
            $Mensaje=$row['mensajes'];
            
            $string.=($localicacion.$codigo.$alistado.$Mensaje."\n");
        
        }

        return $string;

    }

    public function ctrBuscarIE(){
        # code...
    }

    public function ctrAgregarIE(){
        # code...
    }
}
