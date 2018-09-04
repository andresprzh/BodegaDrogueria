<?php

class ModeloCaja extends Conexion{
    /* ============================================================================================================================
                                                        ATRIBUTOS  
    ============================================================================================================================*/
    
    private $req;

    /* ============================================================================================================================
                                                        CONSTRUCTOR   
    ============================================================================================================================*/
    function __construct($req) {

        $this->req=$req;
        parent::__construct();

    }

      /* ============================================================================================================================
                                                        FUNCIONES   
    ============================================================================================================================*/

    // muestra cajas que no se han recibido
    public function mdlMostrarCaja($numcaja)
    {
        $no_req=$this->req[0];$alistador=$this->req[1];

        $sql = 'SELECT caja.no_caja,caja.estado, usuario.nombre,tipo_caja,abrir,cerrar,recibido
		FROM caja 
		LEFT JOIN pedido ON pedido.no_caja=caja.no_caja
        INNER JOIN recibido ON recibido.no_caja=caja.no_caja
		INNER JOIN usuario ON usuario.id_usuario=Alistador
		WHERE caja.no_caja LIKE :numcaja 
		AND (pedido.no_req=:no_req OR recibido.no_req=:no_req)
		AND caja.no_caja <> 1
		AND caja.estado <> 3 
        GROUP BY caja.no_caja,caja.estado;';
        
        $stmt= $this->link->prepare($sql);

        $stmt->bindParam(":numcaja",$numcaja,PDO::PARAM_STR);
        $stmt->bindParam(":no_req",$no_req,PDO::PARAM_STR);

        $stmt->execute();


        return $stmt;

        // cierra la conexion
        $stmt=null;
    }

    // modifica caja asignando fecha en la que se envio y cambiando su estado 
    public function mdlModificarCaja($numcaja)
    {
        $no_req=$this->req[0];$alistador=$this->req[1];

        $stmt= $this->link->prepare("UPDATE caja SET enviado=NOW(),estado=2 WHERE no_caja=:no_caja");

        $stmt->bindParam(":no_caja",$numcaja,PDO::PARAM_STR);

        $res=$stmt->execute();
        $stmt->closeCursor();
        return $res;
        // cierra la conexion
        $stmt=null;

    }

    public function mdlCancelarCaja($numcaja)
    {
        $no_req=$this->req[0];$alistador=$this->req[1];

        $stmt= $this->link->prepare("UPDATE caja SET estado=9 WHERE no_caja=:no_caja");

        $stmt->bindParam(":no_caja",$numcaja,PDO::PARAM_STR);

        $res=$stmt->execute();
        $stmt->closeCursor();
        return $stmt->errorInfo();
        // cierra la conexion
        $stmt=null;
    }

    public function mdlCancelarItems($numcaja)
    {
        $no_req=$this->req[0];$alistador=$this->req[1];

        $stmt= $this->link->prepare("UPDATE pedido SET no_caja=1,alistado=0,estado=0 WHERE no_caja=:no_caja");

        $stmt->bindParam(":no_caja",$numcaja,PDO::PARAM_STR);

        $res=$stmt->execute();
        $stmt->closeCursor();
        return $res;;
        // cierra la conexion
        $stmt=null;
    }

    public function mdlMostrarItemCancelados($numcaja)
    {
        
        $sql="SELECT item,recibido.no_req,no_caja,recibido.estado,
        MIN(ID_CODBAR) AS ID_CODBAR,ID_REFERENCIA,DESCRIPCION,
        lo_origen,lo_destino
        FROM recibido
        INNER JOIN requisicion ON requisicion.no_req=recibido.no_req
        INNER JOIN ITEMS ON ID_ITEM=ITEM
        INNER JOIN COD_BARRAS ON ID_ITEMS=ID_ITEM
        WHERE recibido.no_caja = :no_caja
        GROUP BY item,no_req,no_caja,estado;";

        $stmt= $this->link->prepare($sql);

        $stmt->bindParam(":no_caja",$numcaja,PDO::PARAM_STR);

        $stmt->execute();

        return $stmt;
        // cierra la conexion
        $stmt=null;
    }
}