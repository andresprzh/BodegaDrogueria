<?php

class ModeloRemision extends Conexion{
    
    /* ============================================================================================================================
                                                        CONSTRUCTOR   
    ============================================================================================================================*/
    function __construct() {
      
        parent::__construct();
        
    }
     /* ============================================================================================================================
                                                        FUNCIONES   
    ============================================================================================================================*/
    public function mdlAsignarLote($item,$no_rem)
    {
        $tabla="pedido_remisiones";
        $stmt= $this->link->prepare(
        "UPDATE $tabla
        SET lote=:lote,
        vencimiento=:vencimiento
        WHERE item=:item
        AND no_rem=:no_rem;");
        // return $item["rent  "];
        $stmt->bindParam(":item",$item["item"],PDO::PARAM_STR);
        $stmt->bindParam(":no_rem",$no_rem,PDO::PARAM_INT);
        $stmt->bindParam(":lote",$item["lote"],PDO::PARAM_STR);
        $stmt->bindParam(":vencimiento",$item["vencimiento"],PDO::PARAM_STR);
        $res= $stmt->execute();
        
        $stmt->closeCursor();
        return $res; 
    }
    public function mdlMostrarRem($franquicia,$estado=null)
    {
        if (isset($estado)) {
            $stmt= $this->link->prepare(
            "SELECT no_rem,creada,franquicia,estado
            FROM remisiones
            WHERE franquicia=:franquicia
            AND estado=:estado;");
            $stmt->bindParam(":estado",$estado,PDO::PARAM_INT); 
        }else {
            $stmt= $this->link->prepare(
            "SELECT no_rem,creada,franquicia,estado
            FROM remisiones
            WHERE franquicia=:franquicia");
        }
        
        $stmt->bindParam(":franquicia",$franquicia,PDO::PARAM_STR); 

        $res= $stmt->execute();
                
        return $stmt;
        $stmt->closeCursor();
        $stmt=null;
    }

    // public function mdlSubirReq($cabecera,$items)
    public function mdlSubirRem($usuario,$franquicia,$fecha)
    {
        $tabla="remisiones";
        
        $stmt= $this->link->prepare("INSERT INTO $tabla(encargado,franquicia,creada,estado) VALUES(:usuario,:franquicia,:fecha,2)");
        $stmt->bindParam(":usuario",$usuario,PDO::PARAM_INT);
        $stmt->bindParam(":franquicia",$franquicia,PDO::PARAM_STR);
        $stmt->bindParam(":fecha",$fecha,PDO::PARAM_STR);
        
        $res=$stmt->execute();
        
        $stmt->closeCursor();

        if ($res) {
            
            $stmt= $this->link->prepare("SELECT LAST_INSERT_ID() AS id;");
            $stmt->execute();
            $res=$stmt->fetch()['id'];
            $stmt->closeCursor();
        }
        return $res;
    }

    public function mdlSubirItem($item,$no_rem)
    {
        $tabla="pedido_remisiones";
        $stmt= $this->link->prepare(
        "INSERT INTO $tabla(item,no_rem,cantidad,unidad,valor,descuento,impuesto,total,costo,rent,ubicacion) 
        VALUES(:item,:no_rem,:cantidad,:unidad,:valor,:descuento,:impuesto,:total,:costo,:rent,:ubicacion);");
        // return $item["rent  "];
        $stmt->bindParam(":item",$item["item"],PDO::PARAM_STR);
        $stmt->bindParam(":no_rem",$no_rem,PDO::PARAM_INT);
        $stmt->bindParam(":cantidad",$item["cantidad"],PDO::PARAM_INT);
        $stmt->bindParam(":unidad",$item["unidad"],PDO::PARAM_STR);
        $stmt->bindParam(":valor",$item["valor"],PDO::PARAM_STR);
        $stmt->bindParam(":descuento",$item["descuento"],PDO::PARAM_STR);
        $stmt->bindParam(":impuesto",$item["impuesto"],PDO::PARAM_STR);
        $stmt->bindParam(":total",$item["total"],PDO::PARAM_STR);
        $stmt->bindParam(":costo",$item["costo"],PDO::PARAM_STR);
        $stmt->bindParam(":rent",$item["rent"],PDO::PARAM_STR);
        $stmt->bindParam(":ubicacion",$item["local"],PDO::PARAM_STR);

        $res= $stmt->execute();
        
        $stmt->closeCursor();
        return $res;
    }
    
    public function mdlSubirItems($items,$no_rem)
    {
        $sql="INSERT INTO pedido_remisiones(item,no_rem,cantidad,unidad,valor,descuento,impuesto,total,costo,rent,ubicacion) 
        VALUES";
        for ($i=0; $i <count($items) ; $i++) { 
            $sql.="(:item$i,:no_rem$i,:cantidad$i,:unidad$i,:valor$i,:descuento$i,:impuesto$i,:total$i,:costo$i,:rent$i,:ubicacion$i),";
            // $keys[]=$i;
        }
        $sql=substr($sql,0,-1).";";
        
        $stmt= $this->link->prepare($sql);
        
        $count=0;
        // for ($i=0; $i <count($items) ; $i++) { 
        foreach ($items as $i=> $item) { 
            $stmt->bindParam(":item$count",$item["item"],PDO::PARAM_STR);
            $stmt->bindParam(":no_rem$count",$no_rem,PDO::PARAM_INT);
            $stmt->bindParam(":cantidad$count",$item["cantidad"],PDO::PARAM_INT);
            $stmt->bindParam(":unidad$count",$item["unidad"],PDO::PARAM_STR);
            $stmt->bindParam(":valor$count",$item["valor"],PDO::PARAM_STR);
            $stmt->bindParam(":descuento$count",$item["descuento"],PDO::PARAM_STR);
            $stmt->bindParam(":impuesto$count",$item["impuesto"],PDO::PARAM_STR);
            $stmt->bindParam(":total$count",$item["total"],PDO::PARAM_STR);
            $stmt->bindParam(":costo$count",$item["costo"],PDO::PARAM_STR);
            $stmt->bindParam(":rent$count",$item["rent"],PDO::PARAM_STR);
            $stmt->bindParam(":ubicacion$count",$item["local"],PDO::PARAM_STR);
            // $keys[]=$i;
            
            $count++;
            
        }
        // return count($keys);
        $res= $stmt->execute();
        return $stmt->errorInfo();
        $stmt->closeCursor();
        return $res;
    }

    public function mdlMostrarRemDoc($no_rem)
    {
        $stmt= $this->link->prepare(
        "SELECT item,valor,cantidad,pedido_remisiones.lote AS lote,descuento,unidad,
        vencimiento,ubicacion,total,remisiones.no_rem,remisiones.creada,
        nit,cod_sucursal,ITEMS.DESCRIPCION,ITEMS.LOTE AS eslote
        FROM pedido_remisiones
        INNER JOIN ITEMS ON ITEMS.ID_ITEM=pedido_remisiones.item
        INNER JOIN remisiones ON remisiones.no_rem=pedido_remisiones.no_rem
        INNER JOIN franquicias ON franquicias.codigo=remisiones.franquicia
        WHERE pedido_remisiones.no_rem=:no_rem;");

        $stmt->bindParam(":no_rem",$no_rem,PDO::PARAM_INT); 

        $res= $stmt->execute();
                
        return $stmt;
        $stmt->closeCursor();
        $stmt=null;
    }

    public function mdlMostrarFranquicias()
    {
        $stmt= $this->link->prepare(
        "SELECT codigo,descripcion
        FROM franquicias
        WHERE codigo<>'NFRA'
        ORDER BY descripcion ASC;");

        $res= $stmt->execute();
                
        return $stmt;
        $stmt->closeCursor();
        $stmt=null;
    }
   
}