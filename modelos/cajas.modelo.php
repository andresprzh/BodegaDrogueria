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

    public function mdlMostrarCaja($NumCaja)
    {
        $No_Req=$this->Req[0];$alistador=$this->Req[1];
        

        $stmt= $this->link->prepare("CALL buscarcaja(:NumCaja,:No_Req,0);");

        $stmt->bindParam(":NumCaja",$NumCaja,PDO::PARAM_STR);
        $stmt->bindParam(":No_Req",$No_Req,PDO::PARAM_STR);

        $stmt->execute();


        return $stmt;

        // cierra la conexion
        $stmt=null;
    }

    public function mdlModificarCaja($NumCaja)
    {
        $No_Req=$this->Req[0];$alistador=$this->Req[1];

        $stmt= $this->link->prepare("UPDATE caja SET enviado=NOW(),estado=1 WHERE no_caja=:no_caja");

        $stmt->bindParam(":no_caja",$NumCaja,PDO::PARAM_STR);

        return $stmt->execute();
        // cierra la conexion
        $stmt=null;

    }

}