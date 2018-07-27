<?php

class ModeloRequierir extends Conexion{
    
    
    function __construct() {
      
        parent::__construct();
        
    }

    public function mdlMostrarReq($item,$valor=null){
    
        $tabla='requisicion';
        if ($valor==null) {
            $stmt= $this->link-> prepare("SELECT * FROM $tabla WHERE $item IS NULL");
        }else {
            $stmt= $this->link-> prepare("SELECT * FROM $tabla WHERE $item = :$item");
        }
        
        //para evitar sql injection
        $stmt->bindParam(":".$item,$valor,PDO::PARAM_STR);
        //ejecuta el comando sql
        $stmt->execute();
        //busca las requisiciones
        // return $this->buscaritem($tabla,$item,$valor);
        return $stmt;
    }

    public function mdlMostrarItem($item,$valor){
        
        $tabla='ITEMS';

        //busca los items
        return $this->buscaritem($tabla,$item,$valor);
    }

    public function mdlSubirReq($Cabecera,$Items){
        $tabla="requisicion";
        // $stmt= $this->link->prepare("INSERT INTO $tabla(No_Req,creada,Lo_Origen,Lo_Destino,Tip_Inventario,Solicitante) VALUES('$Cabecera[0]','$Cabecera[1]','$Cabecera[2]','$Cabecera[3]','$Cabecera[4]','$Cabecera[5]');");
        $stmt= $this->link->prepare("INSERT INTO $tabla(No_Req,creada,Lo_Origen,Lo_Destino,Tip_Inventario,Solicitante) VALUES(:No_Req,:fecha,:Lo_Origen,:Lo_Destino,:Tip_Inventario,:Solicitante)");
        
        $stmt->bindParam(":No_Req",$Cabecera[0],PDO::PARAM_STR);
        $stmt->bindParam(":fecha",$Cabecera[1],PDO::PARAM_STR);
        // $stmt->bindParam(":hora",$Cabecera[2],PDO::PARAM_STR);
        $stmt->bindParam(":Lo_Origen",$Cabecera[2],PDO::PARAM_STR);
        $stmt->bindParam(":Lo_Destino",$Cabecera[3],PDO::PARAM_STR);
        $stmt->bindParam(":Tip_Inventario",$Cabecera[4],PDO::PARAM_INT);
        $stmt->bindParam(":Solicitante",$Cabecera[5],PDO::PARAM_STR);
        
        
        $stmt->execute();
        
        $tabla="pedido";

        $sql='INSERT INTO '.$tabla.'(Item,No_Req,ubicacion,disp,pedido,alistado) VALUES'. $Items;
        
        $stmt= $this->link->prepare($sql);
        
        $res= $stmt->execute();
        
        return $res;
    }
}