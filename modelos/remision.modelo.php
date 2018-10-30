<?php

class ModeloRemision extends Conexion{
    
    
    function __construct() {
      
        parent::__construct();
        
    }

    public function mdlMostrarRem(){
    
        $tabla='remisiones';
        $stmt= $this->link-> prepare(
        "SELECT no_rem
        FROM $tabla 
        ORDER BY creada DESC
        LIMIT 1;");
            
        //ejecuta el comando sql
        $stmt->execute();
        //busca las requisiciones
        // return $this->buscaritem($tabla,$item,$valor);
        return $stmt;
    }


    // public function mdlSubirReq($cabecera,$items)
    public function mdlSubirRem()
    {
        $tabla="remisiones";
        
        $stmt= $this->link->prepare("INSERT INTO $tabla(estado) VALUES(0)");
                
        
        $res=$stmt->execute();
        
        $stmt->closeCursor();
        return $res;
    }

    public function mdlEliReq($req)
    {
        $tabla="requisicion";
        $stmt= $this->link->prepare("DELETE FROM $tabla WHERE no_req=:no_req");
        $stmt->bindParam(":no_req",$req,PDO::PARAM_STR);

        $res= $stmt->execute();
        $stmt->closeCursor();
        return $res;
    }

    public function mdlSubirItem($item,$no_rem)
    {
        $tabla="pedido_remisiones";
        $stmt= $this->link->prepare(
        "INSERT INTO $tabla(item,no_rem,cantidad,unidad,valor,descuento,impuesto,total,costo,rent) 
        VALUES(:item,:no_rem,:cantidad,:unidad,:valor,:descuento,:impuesto,:total,:costo,:rent);");

        $stmt->bindParam(":item",$item["item"],PDO::PARAM_STR);
        $stmt->bindParam(":no_rem",$no_rem,PDO::PARAM_INT);
        $stmt->bindParam(":cantidad",$item["cantidad"],PDO::PARAM_INT);
        $stmt->bindParam(":unidad",$item["unidad"],PDO::PARAM_STR);
        $stmt->bindParam(":valor",$item["valor"],PDO::PARAM_INT);
        $stmt->bindParam(":descuento",$item["descuento"],PDO::PARAM_INT);
        $stmt->bindParam(":impuesto",$item["impuesto"],PDO::PARAM_INT);
        $stmt->bindParam(":total",$item["total"],PDO::PARAM_INT);
        $stmt->bindParam(":costo",$item["costo"],PDO::PARAM_INT);
        $stmt->bindParam(":rent",$item["rent"],PDO::PARAM_INT);

        $res= $stmt->execute();
        return $stmt->errorInfo();
        $stmt->closeCursor();
        return $res;
    }
}