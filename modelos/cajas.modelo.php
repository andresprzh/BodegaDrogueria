<?php

class ModeloCaja extends Conexion{
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

    public function mslMostrarCaja($NumCaja)
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

    public function mslMostrarItemsCaja($NumCaja)
    {
        $No_Req=$this->Req[0];$alistador=$this->Req[1];
        

        $stmt= $this->link->prepare("CALL buscarcod('%%',:No_Req,:alistador,:NumCaja);");

        
        $stmt->bindParam(":No_Req",$No_Req,PDO::PARAM_STR);
        $stmt->bindParam(":alistador",$alistador,PDO::PARAM_INT);
        $stmt->bindParam(":NumCaja",$NumCaja,PDO::PARAM_STR);

        $stmt->execute();

        return $stmt;

        // cierra la conexion
        $stmt=null;
    }

}