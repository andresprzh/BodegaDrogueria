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

    // muestra cajas que no se han recibido
    public function mdlMostrarCaja($NumCaja)
    {
        $No_Req=$this->Req[0];$alistador=$this->Req[1];

        $sql = 'SELECT caja.no_caja, usuario.nombre,tipo_caja,abrir,cerrar,recibido
		FROM caja 
		INNER JOIN pedido ON pedido.no_caja=caja.no_caja
		INNER JOIN usuario ON usuario.id_usuario=Alistador
		WHERE caja.no_caja LIKE :NumCaja 
		AND pedido.no_req=:No_Req
		AND caja.no_caja <> 1
		AND caja.estado <> 2 
        GROUP BY caja.no_caja;';
        
        $stmt= $this->link->prepare($sql);

        $stmt->bindParam(":NumCaja",$NumCaja,PDO::PARAM_STR);
        $stmt->bindParam(":No_Req",$No_Req,PDO::PARAM_STR);

        $stmt->execute();


        return $stmt;

        // cierra la conexion
        $stmt=null;
    }

    // modifica caja asignando fecha en la que se envio y cambiando su estado 
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