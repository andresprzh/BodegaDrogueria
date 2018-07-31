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


    public function mdlMostrarItemPV($Cod_bar){
        $tabla="ITEMS";
        $item='ID_CODBAR';
        
        return $this->buscaritem($tabla,$item,$Cod_bar);
        
    }

    public function mdlRegistrarItems($Items,$numcaja){   
        // guarda datos de la requisicion
        $no_req=$this->Req[0];$persona=$this->Req[1];
        $datos="";
        for($i=0;$i<count($Items);$i++) {
            $datos.="(:Item$i,:No_Req,:no_caja,:recibidos$i),";
        }

        $datos=substr($datos, 0, -1).';';

        $sql='REPLACE INTO recibido(Item,No_Req,no_caja,recibidos) VALUES'. $datos;

        $stmt= $this->link->prepare($sql);

        $i=0;
        foreach ($Items as $row) {
            $stmt->bindParam(":Item$i",$row['item'],PDO::PARAM_STR);
            $stmt->bindParam(":recibidos$i",$row['recibidos'],PDO::PARAM_INT);
            $i++;
        }

        $stmt->bindParam(":No_Req",$no_req,PDO::PARAM_STR);
        $stmt->bindParam(":no_caja",$numcaja,PDO::PARAM_STR);
        
        
        $res= $stmt->execute();
        
        // libera conexion para hace otra sentencia
        $stmt->closeCursor();
        return ($res);  
    }

    //modifica registro en tabla para agregar la fecha en la que fue recibido
    public function mdlModCaja($NumCaja){
        // $this->link->closeCursor();
        $persona=$this->Req[1];
        
        $stmt= $this->link->prepare("UPDATE caja SET encargado_punto=:persona,recibido=NOW() WHERE no_caja=:caja;" );
        
        $stmt->bindParam(":persona",$persona,PDO::PARAM_INT);
        $stmt->bindParam(":caja",$NumCaja,PDO::PARAM_INT);
        

        $res= $stmt->execute();
        
        // libera conexion para hace otra sentencia
        $stmt->closeCursor();
        return ($res);  
    }

    public function mdlMostrarReq(){

        $tabla='requisicion';
        $item='no_req';
        $valor=$this->Req[0];
        
        return $this->buscaritem($tabla,$item,$valor);

        
    }    

    public function mdlMostrarItemsRec($numcaja)
    {
        $sql="SELECT lo_origen,lo_destino,ID_CODBAR,recibidos FROM recibido INNER JOIN requisicion on requisicion.no_req=recibido.no_req INNER JOIN ITEMS on ITEMS.ID_ITEM=recibido.item WHERE no_caja=:no_caja";

        $stmt= $this->link->prepare($sql );

        $stmt->bindParam(":no_caja",$numcaja,PDO::PARAM_INT);
        $stmt->execute();

        return ($stmt);  
    }
}
