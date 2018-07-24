<?php

class ModeloPV extends Conexion{
    /* ============================================================================================================================
                                                        ATRIBUTOS  
    ============================================================================================================================*/
    
    private $Req;

    /* ============================================================================================================================
                                                        CONSTRUCTOR   
    ============================================================================================================================*/
    function __construct($Req) {

        $this->Req=$Req;
        parent::__construct();

    }

      /* ============================================================================================================================
                                                        FUNCIONES   
    ============================================================================================================================*/

    public function mdlMostrarCaja($NumCaja)
    {
        $No_Req=$this->Req[0];$alistador=$this->Req[1];
        

        $stmt= $this->link->prepare("CALL buscarcaja(:NumCaja,:No_Req);");

        $stmt->bindParam(":NumCaja",$NumCaja,PDO::PARAM_STR);
        $stmt->bindParam(":No_Req",$No_Req,PDO::PARAM_STR);

        $stmt->execute();


        return $stmt;

        // cierra la conexion
        $stmt=null;
    }

    public function mdlMostrarItemPV($Cod_bar)
    {
        $tabla="items";
        $item='ID_CODBAR';
        
        return $this->buscaritem($tabla,$item,$Cod_bar);
        
    }

}