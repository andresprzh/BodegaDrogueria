<?php

class ModeloPV extends Conexion{
    /* ============================================================================================================================
                                                        ATRIBUTOS  
    ============================================================================================================================*/
    
    private $req;

    /* ============================================================================================================================
                                                        CONSTRUCTOR   
    ============================================================================================================================*/
    function __construct($req=null) {

        $this->req=$req;
        parent::__construct();

    }

      /* ============================================================================================================================
                                                        FUNCIONES   
    ============================================================================================================================*/

    // busca y muestra un item
    public function mdlMostrarItemPV($cod_bar){
        $tabla="ITEMS";
        
        $stmt= $this->link->prepare("SELECT * FROM COD_BARRAS  
        INNER JOIN ITEMS ON ID_ITEM=ID_ITEMS
        WHERE ID_CODBAR=:cod_bar OR ID_ITEM=:cod_bar OR ID_REFERENCIA=:cod_bar OR LOWER(DESCRIPCION) = :cod_bar;" );
        
        $stmt->bindParam(":cod_bar",$cod_bar,PDO::PARAM_STR);

        $stmt->execute();
        
        return $stmt;
    }

    // registra el item en la abla de recibidos
    public function mdlRegistrarItems($items,$numcaja){   
        
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
            $stmt->bindParam(":item$i",$row['iditem'],PDO::PARAM_STR);
            $stmt->bindParam(":recibidos$i",$row['recibidos'],PDO::PARAM_INT);
            $i++;
        }

        $stmt->bindParam(":no_req",$no_req,PDO::PARAM_STR);
        $stmt->bindParam(":no_caja",$numcaja,PDO::PARAM_STR);
        
        
        $res= $stmt->execute();
        return $stmt->errorInfo();
        // libera conexion para hace otra sentencia
        $stmt->closeCursor();
        return ($res);  
        $stmt=null;
    }

    // registra el item en la abla de recibidos_remisiones
    public function mdlRegistrarRemision($items,$rem){ 
        
        $no_rem=$rem['no_rem'];$persona=$rem['id_usuario'];
        $datos="";
        for($i=0;$i<count($items);$i++) {
            $datos.="(:item$i,:no_rem,:recibidos$i),";
        }

        $datos=substr($datos, 0, -1).' ';

        $sql='REPLACE INTO recibido_remisiones(item,no_rem,recibidos) VALUES'. $datos;
        

        $stmt= $this->link->prepare($sql);

        $i=0;
        foreach ($items as $row) {
            $stmt->bindParam(":item$i",$row['item'],PDO::PARAM_STR);
            $stmt->bindParam(":recibidos$i",$row['recibidos'],PDO::PARAM_INT);
            $i++;
        }

        $stmt->bindParam(":no_rem",$no_rem,PDO::PARAM_STR);
        
        
        
        $res= $stmt->execute();
        
        // libera conexion para hace otra sentencia
        $stmt->closeCursor();
        return ($res);  
        $stmt=null;
    }

    //modifica registro en tabla para agregar la fecha en la que fue recibido
    public function mdlModCaja($NumCaja,$estado=4){
        $persona=$this->req[1];

        $stmt= $this->link->prepare("UPDATE caja SET encargado_punto=:persona,estado=:estado,registrado=NOW() WHERE no_caja=:caja;" );
        
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

    // muestra los items recibidos de una requisicion
    public function mdlMostrarItemsRec($numcaja){
        $sql="SELECT recibido.item AS iditem,DESCRIPCION AS descripcion,alistado.alistado,
        cajap.num_caja AS cajap,cajar.num_caja AS cajar,
        recibido.recibidos,recibido.estado
        FROM recibido
        INNER JOIN ITEMS ON ITEMS.ID_ITEM=recibido.item
        INNER JOIN requisicion ON requisicion.no_req=recibido.no_req
        LEFT JOIN  alistado ON alistado.item=recibido.item
        INNER JOIN caja AS cajar ON cajar.no_caja=recibido.no_caja
        INNER JOIN caja AS cajap ON cajap.no_caja=alistado.no_caja
        WHERE recibido.no_caja=:no_caja";

        $stmt= $this->link->prepare($sql );

        $stmt->bindParam(":no_caja",$numcaja,PDO::PARAM_INT);
        $stmt->execute();

        return ($stmt);  
    }

    // muestra los items recibidos de una remision
    public function mdlMostrarItemsRem($no_rem)
    {
        $sql=
        "SELECT recibido_remisiones.item AS item,DESCRIPCION AS descripcion,
        pedido_remisiones.cantidad,recibido_remisiones.recibidos,
        recibido_remisiones.estado AS rem_estado
        FROM recibido_remisiones
        INNER JOIN ITEMS ON ITEMS.ID_ITEM=recibido_remisiones.item
        INNER JOIN remisiones ON remisiones.no_rem=recibido_remisiones.no_rem
        LEFT JOIN  pedido_remisiones ON pedido_remisiones.item=recibido_remisiones.item
        WHERE recibido_remisiones.no_rem=:no_rem";

        $stmt= $this->link->prepare($sql );

        $stmt->bindParam(":no_rem",$no_rem,PDO::PARAM_INT);
        $stmt->execute();

        return ($stmt);  
    }

    // muestra items recibidos de una remision
    public function mdlMostraRecibidoRemision($no_rem)
    {
        $stmt= $this->link->prepare(
        "SELECT item,DESCRIPCION AS descripcion, ID_REFERENCIA AS referencia, recibidos, MIN(ID_CODBAR) AS codbarras
        FROM recibido_remisiones
        INNER JOIN ITEMS ON ID_ITEM=item
        INNER JOIN COD_BARRAS ON ID_ITEMS=ID_ITEM
        WHERE no_rem=:no_rem
        GROUP BY item,DESCRIPCION,ID_REFERENCIA,recibidos;
        ");

        $stmt->bindParam(":no_rem",$no_rem,PDO::PARAM_INT);
        $stmt->execute();

        return ($stmt);  
    }
    // muestra las cajas enviadas
    public function mdlMostrarCajaPV($numcaja){
        $no_req=$this->req[0];$alistador=$this->req[1];
        

        // $stmt= $this->link->prepare("CALL buscarcaja(:numcaja,:no_req,3);");
        $sql = 'SELECT caja.no_caja,caja.num_caja,caja.estado, usuario.nombre,tipo_caja,abrir,cerrar,recibido
        FROM caja 
        LEFT JOIN alistado ON alistado.no_caja=caja.no_caja
        INNER JOIN usuario ON usuario.id_usuario=Alistador
        WHERE caja.no_caja LIKE :numcaja 
        AND (alistado.no_req=:no_req)
        AND caja.estado = 3
        GROUP BY caja.no_caja,caja.num_caja,caja.estado
        ORDER BY caja.num_caja ASC;';
        $stmt= $this->link->prepare($sql);

        $stmt->bindParam(":numcaja",$numcaja,PDO::PARAM_STR);
        $stmt->bindParam(":no_req",$no_req,PDO::PARAM_STR);

        $stmt->execute();


        return $stmt;

        // cierra la conexion
        $stmt=null;
    }

    // muestra items recibidos de una requisicion
    public function mdlMostrarrecibidos($caja){
        
        $sql="SELECT item as iditem,recibido.no_req,recibido.no_caja,num_caja,recibidos,recibido.estado,requisicion.lo_origen,requisicion.lo_destino
        FROM recibido
        INNER JOIN requisicion ON requisicion.no_req=recibido.no_req
        INNER JOIN caja ON caja.no_caja=recibido.no_caja
        WHERE ";

        // si caja es un array concatena todos los numeros de caja en la condicion
        if (is_array($caja)) {

            for($i=0;$i<count($caja);$i++) {

            $sql.=" recibido.no_caja=:no_caja$i OR";
            }
            
            $sql=substr($sql, 0, -2)."ORDER BY num_caja ASC;";
            $stmt= $this->link->prepare($sql);
    
            foreach ($caja as $i => &$numcaja) {
                $stmt->bindParam(":no_caja$i",$numcaja,PDO::PARAM_INT);   
            } 
        
        // si es solo una caja
        }else {
            $sql.="recibido.no_caja=:no_caja ORDER BY num_caja ASC;";
            $stmt= $this->link->prepare($sql );
            $stmt->bindParam(":no_caja",$caja,PDO::PARAM_INT);
        }
    
        $stmt->execute();

        return ($stmt);  
    }

    // verificar caja resibida
    public function mdlVerificarCaja($numcaja)
    {
        $no_req=$this->req[0];$alistador=$this->req[1];
        

        $stmt= $this->link->prepare('SELECT VerificarCaja(:no_caja,:no_req) AS verificar ');

        $stmt->bindParam(":no_caja",$numcaja,PDO::PARAM_INT);
        $stmt->bindParam(":no_req",$no_req,PDO::PARAM_STR);

        // $res=$stmt->execute();
        if ($stmt->execute()) {
            $res["estado"]=$stmt->fetch()["verificar"];
        }else {
            $res=false;
        }
        $stmt->closeCursor();  
        // retorna el resultado de la sentencia
	    return $res;
         
        // cierra la conexion
        $stmt=null;
    }

    // verifica estado de los items recibidos de una remision
    public function mdlVerificarRemision($no_rem)
    {
        $no_req=$this->req[0];$alistador=$this->req[1];
        
        // $stmt= $this->link->prepare('SELECT COUNT(item) AS cantidad
        // FROM recibido
        // WHERE no_caja=:no_caja
        // AND no_req=:no_req
        // AND estado <>4;
        // ');

        $stmt= $this->link->prepare('SELECT VerificarRemision(:no_rem) AS verificar ');

        
        $stmt->bindParam(":no_rem",$no_rem,PDO::PARAM_STR);

        $res=$stmt->execute();
        $stmt->closeCursor();  
        // retorna el resultado de la sentencia
	    return $res;
        // $stmt->closeCursor();  
        // cierra la conexion
        $stmt=null;
    }
    
    // muestra ubicacion 
    public function mdlMostrarUbicacion($franquicia)
    {
        $stmt= $this->link->prepare(
        'SELECT descripcion
        FROM franquicias
        WHERE codigo=:codigo;
        ');

        $stmt->bindParam(":codigo",$franquicia,PDO::PARAM_STR);

        $res=$stmt->execute();
         
        // retorna el resultado de la sentencia
	    return $stmt;
        $stmt->closeCursor();  
        // cierra la conexion
        $stmt=null;
    }

    
}
