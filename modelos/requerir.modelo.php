<?php

class ModeloRequierir extends Conexion{
    
    
    function __construct() {
      
        parent::__construct();
        
    }

    // MUESTRA REQUISICIONES
    public function mdlMostrarReq($item=null,$valor=null){
        
        $tabla='requisicion';
        
        if ($valor==null) {
            $stmt= $this->link-> prepare("SELECT requisicion.no_req,creada,lo_origen,lo_destino,solicitante,enviado,recibido,
            requisicion.estado,sedes.descripcion,tip_inventario,SUM(pendientes) AS pendientes,SUM(pedido) AS pedido
            FROM $tabla 
            INNER JOIN sedes ON requisicion.lo_destino=sedes.codigo
            INNER JOIN pedido ON pedido.no_req=requisicion.no_req
            WHERE requisicion.estado = 0
            GROUP BY  requisicion.no_req,creada,lo_origen,lo_destino,solicitante,enviado,recibido,
            requisicion.estado,sedes.descripcion,tip_inventario;");
        }else if($item=='estado') {
            
            $stmt= $this->link-> prepare("SELECT requisicion.no_req,creada,lo_origen,lo_destino,solicitante,enviado,recibido,
            requisicion.estado,sedes.descripcion,tip_inventario,SUM(pendientes) AS pendientes,SUM(pedido) AS pedido
            FROM $tabla 
            INNER JOIN sedes ON requisicion.lo_destino=sedes.codigo
            INNER JOIN pedido ON pedido.no_req=requisicion.no_req
            WHERE requisicion.estado < :item
            GROUP BY  requisicion.no_req,creada,lo_origen,lo_destino,solicitante,enviado,recibido,
            requisicion.estado,sedes.descripcion,tip_inventario;");
        }else {
            $stmt= $this->link-> prepare("SELECT requisicion.no_req,creada,lo_origen,lo_destino,solicitante,enviado,recibido,
            requisicion.estado,sedes.descripcion,tip_inventario,SUM(pendientes) AS pendientes,SUM(pedido) AS pedido
            FROM $tabla 
            INNER JOIN sedes ON requisicion.lo_destino=sedes.codigo
            INNER JOIN pedido ON pedido.no_req=requisicion.no_req
            WHERE requisicion.estado = :item
            GROUP BY  requisicion.no_req,creada,lo_origen,lo_destino,solicitante,enviado,recibido,
            requisicion.estado,sedes.descripcion,tip_inventario;");
            
        }
        
        //para evitar sql injection
        $stmt->bindParam(":item",$valor,PDO::PARAM_STR);
        //ejecuta el comando sql
        $stmt->execute();
        //busca las requisiciones
        return $stmt;
    }

    // MUESTRA 1 ITEM DE LA TABLA ITEMS
    public function mdlMostrarItem($valor){
        
        $tabla='ITEMS';
        $stmt= $this->link->prepare(
        "SELECT ID_ITEM,ID_REFERENCIA
        FROM ITEMS
        WHERE (ID_ITEM LIKE :valor
        OR ID_REFERENCIA LIKE :valor)
        LIMIT 1;"
        );
        $stmt->bindParam(":valor",$valor,PDO::PARAM_STR);
        $stmt->execute();

        
        // $stmt->closeCursor();
        return $stmt;

        // cierra la conexion
        $stmt=null;
        //busca los items
        return $this->buscaritem($tabla,$item,$valor);
    }

    // MUESTRA TODOS LOS ITEMS DE UNA REQUISICION 
    public function mdlMostrarItems($req){
       
       

        $stmt= $this->link->prepare('SELECT pedido.item,pedido.estado,pedido.no_req,pedido,pedido.disp,pedido.ubicacion,
        ITEMS.ID_REFERENCIA, ITEMS.ID_REFERENCIA,ITEMS.DESCRIPCION,
        requisicion.lo_origen,requisicion.lo_destino,
        MIN(COD_BARRAS.ID_CODBAR) AS ID_CODBAR
        FROM COD_BARRAS
        INNER JOIN ITEMS ON ID_ITEM=ID_ITEMS	
        INNER JOIN pedido ON Item=ID_ITEM	
        INNER JOIN requisicion ON requisicion.no_req=pedido.no_req
        WHERE pedido.no_req = :no_req
        GROUP BY  pedido.item,pedido.estado,pedido.no_req,pedido,pedido.disp,pedido.ubicacion,
        ITEMS.ID_REFERENCIA, ITEMS.ID_REFERENCIA,ITEMS.DESCRIPCION,
        requisicion.lo_origen,requisicion.lo_destino;');

        
        $stmt->bindParam(":no_req",$req,PDO::PARAM_STR);
        

        $stmt->execute();

        
        // $stmt->closeCursor();
        return $stmt;

        // cierra la conexion
        $stmt=null;
    }

    // AGREGA LA REQUISICION A LA BASE DE DATOS
    public function mdlSubirReq($cabecera)
    {
        $tabla="requisicion";
        
        $stmt= $this->link->prepare("INSERT INTO $tabla(no_req,creada,lo_origen,lo_destino,tip_inventario,solicitante) VALUES(:no_req,:fecha,:lo_origen,:lo_destino,:tip_inventario,:solicitante)");
        
        $stmt->bindParam(":no_req",$cabecera[0],PDO::PARAM_STR);
        $stmt->bindParam(":fecha",$cabecera[1],PDO::PARAM_STR);
        // $stmt->bindParam(":hora",$cabecera[2],PDO::PARAM_STR);
        $stmt->bindParam(":lo_origen",$cabecera[2],PDO::PARAM_STR);
        $stmt->bindParam(":lo_destino",$cabecera[3],PDO::PARAM_STR);
        $stmt->bindParam(":tip_inventario",$cabecera[4],PDO::PARAM_INT);
        $stmt->bindParam(":solicitante",$cabecera[5],PDO::PARAM_STR);
        
        
        $res=$stmt->execute();
        
        // $tabla="pedido";

        // $sql='INSERT INTO '.$tabla.'(item,no_req,ubicacion,disp,pedido) VALUES'. $items;
        
        // $stmt= $this->link->prepare($sql);
        
        // $res= $stmt->execute();
        $stmt->closeCursor();
        return $res;
    }

    // ELIMINA UNA  REQUISICION
    public function mdlEliReq($req)
    {
        $tabla="requisicion";
        $stmt= $this->link->prepare("DELETE FROM $tabla WHERE no_req=:no_req");
        $stmt->bindParam(":no_req",$req,PDO::PARAM_STR);

        $res= $stmt->execute();
        $stmt->closeCursor();
        return $res;
    }

    // AGREGA 1 ITEM DE LA REQUISICION A LA BASE DE DATOS 
    public function mdlSubirItem($item)
    {
        $tabla="pedido";
        $stmt= $this->link->prepare("INSERT INTO $tabla(item,no_req,ubicacion,disp,pedido) VALUES(:item,:no_req,:ubicacion,:disp,:pedido);");

        $stmt->bindParam(":item",$item["iditem"],PDO::PARAM_STR);
        $stmt->bindParam(":no_req",$item["no_req"],PDO::PARAM_STR);
        $stmt->bindParam(":ubicacion",$item["ubicacion"],PDO::PARAM_STR);
        $stmt->bindParam(":disp",$item["disp"],PDO::PARAM_INT);
        $stmt->bindParam(":pedido",$item["pedido"],PDO::PARAM_INT);

        $res= $stmt->execute();
        
        $stmt->closeCursor();
        return $res;
    }

    // AGREGA VARIOS ITEMS DE LA REQUISICION A LA BASE DE DATOS
    public function mdlSubirItems($items)
    {
         $sql="INSERT INTO pedido(item,no_req,ubicacion,disp,pedido)
        VALUES";
        for ($i=0; $i <count($items) ; $i++) { 
            $sql.="(:item$i,:no_req$i,:ubicacion$i,:disp$i,:pedido$i),";
        }
        $sql=substr($sql,0,-1).";";
        
        $stmt= $this->link->prepare($sql);
        
        $count=0;
        foreach ($items as $i=> $item) { 
            $stmt->bindParam(":item$count",$item["iditem"],PDO::PARAM_STR);
            $stmt->bindParam(":no_req$count",$item["no_req"],PDO::PARAM_STR);
            $stmt->bindParam(":ubicacion$count",$item["ubicacion"],PDO::PARAM_STR);
            $stmt->bindParam(":disp$count",$item["disp"],PDO::PARAM_INT);
            $stmt->bindParam(":pedido$count",$item["pedido"],PDO::PARAM_INT);
            $count++;
        }
        
        $res= $stmt->execute();
        
        $stmt->closeCursor();
        return $res;
    }
}