<?php

class ModeloRequierir extends Conexion{
    
    
    function __construct() {
      
        parent::__construct();
        
    }

    public function mdlMostrarReq($item,$valor){
    
        $tabla='requisicion';

        //busca las requisiciones
        return $this->buscaritem($tabla,$item,$valor);
    }

    public function mdlMostrarItem($item,$valor){
        
        $tabla='items';

        //busca los items
        return $this->buscaritem($tabla,$item,$valor);
    }

    public function mdlSubirReq($Cabecera,$Items){
        $tabla="requisicion";
        // $stmt= $this->link->prepare("INSERT INTO $tabla VALUES('$Cabecera[0]','$Cabecera[1]','$Cabecera[2]','$Cabecera[3]','$Cabecera[4]','$Cabecera[5]','$Cabecera[6]','$Cabecera[7]')");
        $stmt= $this->link->prepare("INSERT INTO $tabla VALUES(:No_Req,:fecha,:hora,:Lo_Origen,:Lo_Destino,:Tip_Inventario,:Solicitante,:estado)");
        
        $stmt->bindParam(":No_Req",$Cabecera[0],PDO::PARAM_STR);
        $stmt->bindParam(":fecha",$Cabecera[1],PDO::PARAM_STR);
        $stmt->bindParam(":hora",$Cabecera[2],PDO::PARAM_STR);
        $stmt->bindParam(":Lo_Origen",$Cabecera[3],PDO::PARAM_STR);
        $stmt->bindParam(":Lo_Destino",$Cabecera[4],PDO::PARAM_STR);
        $stmt->bindParam(":Tip_Inventario",$Cabecera[5],PDO::PARAM_INT);
        $stmt->bindParam(":Solicitante",$Cabecera[6],PDO::PARAM_STR);
        $stmt->bindParam(":estado",$Cabecera[7],PDO::PARAM_INT);
        
        $stmt->execute();

        $tabla="pedido";

        $sql='INSERT INTO '.$tabla.' VALUES'. $Items;
        
        
        $stmt= $this->link->prepare($sql);
        
        $res= $stmt->execute();
        
        return $res;
    }
}