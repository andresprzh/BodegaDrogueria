<?php

class ModeloPV extends Conexion{
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

    // busca y muestra un item
    public function mdlMostrarItemPV($cod_bar){
        $tabla="ITEMS";
        // $item='ID_CODBAR';
        
        // return $this->buscaritem($tabla,$item,$cod_bar);
        $stmt= $this->link->prepare("SELECT * FROM COD_BARRAS  
        INNER JOIN ITEMS ON ID_ITEM=ID_ITEMS
        WHERE ID_CODBAR=:cod_bar OR ID_ITEM=:cod_bar OR ID_REFERENCIA=:cod_bar OR LOWER(DESCRIPCION) = :cod_bar;" );
        
        $stmt->bindParam(":cod_bar",$cod_bar,PDO::PARAM_STR);

        $stmt->execute();
        
        return $stmt;
    }

    // registra el item en la abla de recibidos
    public function mdlRegistrarItems($items,$numcaja){   
        // guarda datos de la requisicion
        $no_req=$this->req[0];$persona=$this->req[1];
        $datos="";
        for($i=0;$i<count($items);$i++) {
            $datos.="(:item$i,:no_req,:no_caja,:recibidos$i),";
        }

        $datos=substr($datos, 0, -1).';';

        $sql='REPLACE INTO recibido(item,no_req,no_caja,recibidos) VALUES'. $datos;

        $stmt= $this->link->prepare($sql);

        $i=0;
        foreach ($items as $row) {
            $stmt->bindParam(":item$i",$row['item'],PDO::PARAM_STR);
            $stmt->bindParam(":recibidos$i",$row['recibidos'],PDO::PARAM_INT);
            $i++;
        }

        $stmt->bindParam(":no_req",$no_req,PDO::PARAM_STR);
        $stmt->bindParam(":no_caja",$numcaja,PDO::PARAM_STR);
        
        
        $res= $stmt->execute();
        
        // libera conexion para hace otra sentencia
        $stmt->closeCursor();
        return ($res);  
    }

    //modifica registro en tabla para agregar la fecha en la que fue recibido
    public function mdlModCaja($NumCaja,$estado=3){
        // $this->link->closeCursor();
        $persona=$this->req[1];

        $stmt= $this->link->prepare("UPDATE caja SET encargado_punto=:persona,estado=:estado,recibido=NOW() WHERE no_caja=:caja;" );
        
        $stmt->bindParam(":persona",$persona,PDO::PARAM_INT);
        $stmt->bindParam(":estado",$estado,PDO::PARAM_INT);
        $stmt->bindParam(":caja",$NumCaja,PDO::PARAM_INT);
        

        $res= $stmt->execute();
        
        // libera conexion para hace otra sentencia
        $stmt->closeCursor();
        return ($res);  
    }

    // muestra una requisicion
    public function mdlMostrarReq(){

        $tabla='requisicion';
        $item='no_req';
        $valor=$this->req[0];
        
        return $this->buscaritem($tabla,$item,$valor);

        
    }    

    // muestra los items recibidos
    public function mdlMostrarItemsRec($numcaja){
        // $sql="SELECT recibido.item AS iditem,DESCRIPCION AS descripcion,pedido.no_caja AS cajap,recibido.no_caja AS cajar,pedido.alistado,
        // recibido.recibidos,pedido.estado AS ped_estado,recibido.estado AS rec_estado,lo_origen,lo_destino,MIN(ID_CODBAR) AS codigo
        // FROM recibido
        // INNER JOIN ITEMS ON ITEMS.ID_ITEM=recibido.item
        // INNER JOIN COD_BARRAS ON ID_ITEMS=ID_ITEM
        // INNER JOIN requisicion ON requisicion.no_req=recibido.no_req
        // LEFT JOIN  bodegadrogueria.pedido ON pedido.item=recibido.item
        // WHERE recibido.no_caja=:no_caja
        // GROUP BY recibido.item,DESCRIPCION ,pedido.no_caja ,recibido.no_caja ,pedido.alistado,
        // recibido.recibidos,pedido.estado,recibido.estado,lo_origen,lo_destino;";
        $sql="SELECT recibido.item AS iditem,DESCRIPCION AS descripcion,pedido.alistado,
        pedido.no_caja AS cajap,recibido.no_caja AS cajar,
        recibido.recibidos,recibido.estado AS rec_estado
        FROM recibido
        INNER JOIN ITEMS ON ITEMS.ID_ITEM=recibido.item
        INNER JOIN requisicion ON requisicion.no_req=recibido.no_req
        LEFT JOIN  bodegadrogueria.pedido ON pedido.item=recibido.item
        WHERE recibido.no_caja=:no_caja";

        $stmt= $this->link->prepare($sql );

        $stmt->bindParam(":no_caja",$numcaja,PDO::PARAM_INT);
        $stmt->execute();

        return ($stmt);  
    }

    // muestra las cajas enviadas
    public function mdlMostrarCajaPV($NumCaja){
        $no_req=$this->req[0];$alistador=$this->req[1];
        

        $stmt= $this->link->prepare("CALL buscarcaja(:NumCaja,:no_req,2);");

        $stmt->bindParam(":NumCaja",$NumCaja,PDO::PARAM_STR);
        $stmt->bindParam(":no_req",$no_req,PDO::PARAM_STR);

        $stmt->execute();


        return $stmt;

        // cierra la conexion
        $stmt=null;
    }

    public function mdlMostrarrecibidos($numcaja){

        $sql="SELECT item as iditem,recibido.no_req,no_caja,recibidos,recibido.estado,requisicion.lo_origen,requisicion.lo_destino
        FROM recibido
        INNER JOIN requisicion ON requisicion.no_req=recibido.no_req
        WHERE no_caja=:no_caja;";
        $stmt= $this->link->prepare($sql );

        $stmt->bindParam(":no_caja",$numcaja,PDO::PARAM_INT);
        $stmt->execute();

        return ($stmt);  
    }
}
