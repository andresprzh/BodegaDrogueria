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

    public function mdlSubirReq($cabecera,$items){
        $tabla="requisicion";
        // $stmt= $this->link->prepare("INSERT INTO $tabla(no_req,creada,lo_origen,lo_destino,tip_inventario,solicitante) VALUES('$cabecera[0]','$cabecera[1]','$cabecera[2]','$cabecera[3]','$cabecera[4]','$cabecera[5]');");
        $stmt= $this->link->prepare("INSERT INTO $tabla(no_req,creada,lo_origen,lo_destino,tip_inventario,solicitante) VALUES(:no_req,:fecha,:lo_origen,:lo_destino,:tip_inventario,:solicitante)");
        
        $stmt->bindParam(":no_req",$cabecera[0],PDO::PARAM_STR);
        $stmt->bindParam(":fecha",$cabecera[1],PDO::PARAM_STR);
        // $stmt->bindParam(":hora",$cabecera[2],PDO::PARAM_STR);
        $stmt->bindParam(":lo_origen",$cabecera[2],PDO::PARAM_STR);
        $stmt->bindParam(":lo_destino",$cabecera[3],PDO::PARAM_STR);
        $stmt->bindParam(":tip_inventario",$cabecera[4],PDO::PARAM_INT);
        $stmt->bindParam(":solicitante",$cabecera[5],PDO::PARAM_STR);
        
        
        $stmt->execute();
        
        $tabla="pedido";

        $sql='INSERT INTO '.$tabla.'(item,no_req,ubicacion,disp,pedido,alistado) VALUES'. $items;
        
        $stmt= $this->link->prepare($sql);
        
        $res= $stmt->execute();
        
        return $res;
    }
}