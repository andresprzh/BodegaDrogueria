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

    public function mdlMostrarRemDoc($no_rem)
    {
        $stmt= $this->link->prepare(
        "SELECT item,valor,cantidad,pedido_remisiones.lote AS lote,
        vencimiento,ubicacion,total,remisiones.no_rem,remisiones.creada,
        ITEMS.DESCRIPCION,ITEMS.LOTE AS eslote
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
    
    // public function mdlsubiritemrem()
    // {

    //     $tabla="recibido_remisiones";
    //     $stmt= $this->link->prepare(
    //     "INSERT INTO $tabla(item,no_rem,cantidad,unidad,valor,descuento,impuesto,total,costo,rent) 
    //     VALUES(:recibido_remisiones,:no_rem,:cantidad,:unidad,:valor,:descuento,:impuesto,:total,:costo,:rent);");
    //     // return $item["rent  "];
    //     $stmt->bindParam(":item",$item["item"],PDO::PARAM_STR);
    //     $stmt->bindParam(":no_rem",$no_rem,PDO::PARAM_INT);
    //     $stmt->bindParam(":cantidad",$item["cantidad"],PDO::PARAM_INT);
    //     $stmt->bindParam(":unidad",$item["unidad"],PDO::PARAM_STR);
    //     $stmt->bindParam(":valor",$item["valor"],PDO::PARAM_STR);
    //     $stmt->bindParam(":descuento",$item["descuento"],PDO::PARAM_STR);
    //     $stmt->bindParam(":impuesto",$item["impuesto"],PDO::PARAM_STR);
    //     $stmt->bindParam(":total",$item["total"],PDO::PARAM_STR);
    //     $stmt->bindParam(":costo",$item["costo"],PDO::PARAM_STR);
    //     $stmt->bindParam(":rent",$item["rent"],PDO::PARAM_STR);

    //     $res= $stmt->execute();
        
    //     $stmt->closeCursor();
    //     return $res;
    // }
}