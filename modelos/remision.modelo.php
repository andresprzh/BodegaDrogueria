<?php

class ModeloRemision extends Conexion{
    
    
    function __construct() {
      
        parent::__construct();
        
    }

    public function mdlMostrarRem($folder){
    
        $tabla='remisiones';
        $stmt= $this->link-> prepare(
        "SELECT no_rem2
        FROM $tabla
        WHERE no_rem1=:no_rem 
        ORDER BY no_rem2 DESC
        LIMIT 1;");

        $stmt->bindParam(":no_rem",$folder,PDO::PARAM_STR);
        //ejecuta el comando sql
        $stmt->execute();
        //busca las requisiciones
        
        // return $this->buscaritem($tabla,$item,$valor);
        return $stmt;
    }


    // public function mdlSubirReq($cabecera,$items)
    public function mdlSubirRem($folder)
    {
        $tabla="remisiones";
        
        $stmt= $this->link->prepare("INSERT INTO $tabla(no_rem1) VALUES(:no_rem)");
        $stmt->bindParam(":no_rem",$folder,PDO::PARAM_STR);
        
        $res=$stmt->execute();
        
        $stmt->closeCursor();
        return $res;
    }

    public function mdlEliRem($folder,$no_rem2)
    {
        $tabla="remisiones";
        $stmt= $this->link->prepare("DELETE FROM $tabla WHERE no_rem1=:no_rem1 AND no_rem2=:no_rem2");
        $stmt->bindParam(":no_reqm1",$folder,PDO::PARAM_STR);
        $stmt->bindParam(":no_reqm2",$no_rem2,PDO::PARAM_STR);

        $res= $stmt->execute();
        $stmt->closeCursor();
        return $res;
    }

    public function mdlSubirItem($item,$folder,$no_rem2)
    {
        $tabla="pedido_remisiones";
        $stmt= $this->link->prepare(
        "INSERT INTO $tabla(item,no_rem1,no_rem2,cantidad,unidad,valor,descuento,impuesto,total,costo,rent) 
        VALUES(:item,:no_rem1,:no_rem2,:cantidad,:unidad,:valor,:descuento,:impuesto,:total,:costo,:rent);");
        // return $item["rent  "];
        $stmt->bindParam(":item",$item["item"],PDO::PARAM_STR);
        $stmt->bindParam(":no_rem1",$folder,PDO::PARAM_STR);
        $stmt->bindParam(":no_rem2",$no_rem2,PDO::PARAM_INT);
        $stmt->bindParam(":cantidad",$item["cantidad"],PDO::PARAM_INT);
        $stmt->bindParam(":unidad",$item["unidad"],PDO::PARAM_STR);
        $stmt->bindParam(":valor",$item["valor"],PDO::PARAM_STR);
        $stmt->bindParam(":descuento",$item["descuento"],PDO::PARAM_STR);
        $stmt->bindParam(":impuesto",$item["impuesto"],PDO::PARAM_STR);
        $stmt->bindParam(":total",$item["total"],PDO::PARAM_STR);
        $stmt->bindParam(":costo",$item["costo"],PDO::PARAM_STR);
        $stmt->bindParam(":rent",$item["rent"],PDO::PARAM_STR);

        $res= $stmt->execute();
        // return $stmt->errorInfo();
        $stmt->closeCursor();
        return $res;
    }
}