<?php

class ControladorTransportador{

      /* ============================================================================================================================
                                                        ATRIBUTOS   
    ============================================================================================================================*/
    
    private $modelo;
    private $transportador;
    /* ============================================================================================================================
                                                        CONSTRUCTOR   
    ============================================================================================================================*/
    function __construct($transportador=null) {

        $this->transportador=$transportador;
        $this->modelo=new ModeloTransportador($transportador);

    }

    /* ============================================================================================================================
                                                        FUNCIONES   
    ============================================================================================================================*/

    public function mdlBuscarPedidos(){
        $busqueda=$this->modelo->mdlMostrarPedidos();
        // $lo_destino=$busqueda->fetchAll()[0]['lo_destino'];
        $datos=$busqueda->fetchAll(); 
        $conteo=0;
        if ($busqueda->rowCount()>0) {

                $resultado["estado"]="encontrado";
                // $resultado["contenido"]=$busqueda->fetchAll();
                for ($i=0; $i <count($datos) ; $i++) { 
                    echo $datos[$i]['lo_destino'].'<br>';
                }

        }else {

            $resultado["estado"]=false;
            $resultado["contenido"]="No tiene pedidos";

        }
    }
}