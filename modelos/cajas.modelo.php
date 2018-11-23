<?php

class ModeloCaja extends Conexion{
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

    

    // muestra cajas que no se han recibido
    public function mdlMostrarCaja($numcaja,$estado=null)
    {   
        $no_req=$this->req[0];$persona=$this->req[1];
        if ($estado==null) {
            $sql = 'SELECT caja.no_caja,caja.num_caja,caja.estado, usuario.nombre,tipo_caja,abrir,cerrar,recibido
            FROM caja 
            LEFT JOIN alistado ON alistado.no_caja=caja.no_caja
            LEFT JOIN errores ON errores.no_caja_recibido=caja.no_caja
            INNER JOIN usuario ON usuario.id_usuario=Alistador
            WHERE caja.no_caja LIKE :numcaja 
            AND (alistado.no_req=:no_req OR errores.no_req=:no_req)
            GROUP BY caja.no_caja,caja.num_caja,caja.estado;';
            $stmt= $this->link->prepare($sql);
        }else {
            $sql = 'SELECT caja.no_caja,caja.num_caja,caja.estado, usuario.nombre,tipo_caja,abrir,cerrar,recibido
            FROM caja 
            LEFT JOIN alistado ON alistado.no_caja=caja.no_caja
            LEFT JOIN errores ON errores.no_caja_recibido=caja.no_caja
            INNER JOIN usuario ON usuario.id_usuario=Alistador
            WHERE caja.no_caja LIKE :numcaja 
            AND (alistado.no_req=:no_req OR errores.no_req=:no_req)
            AND caja.estado >= :estado
            AND caja.encargado_punto=:persona
            GROUP BY caja.no_caja,caja.num_caja,caja.estado;';
            $stmt= $this->link->prepare($sql);
            $stmt->bindParam(":estado",$estado,PDO::PARAM_INT);
            $stmt->bindParam(":persona",$persona,PDO::PARAM_INT);
        }
       
        $stmt->bindParam(":numcaja",$numcaja,PDO::PARAM_STR);
        $stmt->bindParam(":no_req",$no_req,PDO::PARAM_STR);
        

        $stmt->execute();


        return $stmt;

        // cierra la conexion
        $stmt=null;
    }

    // modifica caja asignando fecha en la que se envio y cambiando su estado 
    public function mdlModificarCaja($numcaja)
    {
        $no_req=$this->req[0];$alistador=$this->req[1];

        $stmt= $this->link->prepare("UPDATE caja SET enviado=NOW(),estado=2 WHERE no_caja=:no_caja AND estado<>3;");

        $stmt->bindParam(":no_caja",$numcaja,PDO::PARAM_STR);

        $res=$stmt->execute();
        $stmt->closeCursor();
        return $res;
        // cierra la conexion
        $stmt=null;

    }

    // elimina una caja
    public function mdlEliminarCaja($numcaja)
    {
        $no_req=$this->req[0];$alistador=$this->req[1];

        // $stmt= $this->link->prepare("UPDATE caja SET estado=9 WHERE no_caja=:no_caja");
        $stmt= $this->link->prepare("DELETE FROM caja WHERE no_caja=:no_caja");

        $stmt->bindParam(":no_caja",$numcaja,PDO::PARAM_STR);

        $res=$stmt->execute();
        $stmt->closeCursor();
        return $res;
        // cierra la conexion
        $stmt=null;
    }
    // elimina items de la tabla alistado
    public function mdlEliminarItems($numcaja)
    {
        $no_req=$this->req[0];$alistador=$this->req[1];

        $stmt= $this->link->prepare("DELETE FROM alistado WHERE no_caja=:no_caja");

        $stmt->bindParam(":no_caja",$numcaja,PDO::PARAM_STR);

        $res=$stmt->execute();
        $stmt->closeCursor();
        return $res;
        // cierra la conexion
        $stmt=null;
    }
    // elimina items de la tabla recibido
    public function mdlEliminarRecibidos($numcaja)
    {

        $stmt= $this->link->prepare("DELETE FROM recibido WHERE no_caja=:no_caja");

        $stmt->bindParam(":no_caja",$numcaja,PDO::PARAM_STR);

        $res=$stmt->execute();
        $stmt->closeCursor();
        return $res;
        // cierra la conexion
        $stmt=null;
    }
    // elimina item de la tabla errores
    public function mdlEliminarErrores($numcaja)
    {

        $stmt= $this->link->prepare("DELETE FROM errores WHERE no_caja_recibido=:no_caja");

        $stmt->bindParam(":no_caja",$numcaja,PDO::PARAM_STR);

        $res=$stmt->execute();
        $stmt->closeCursor();
        return $res;
        // cierra la conexion
        $stmt=null;
    }

    // muestra items que tuvieron errores al resivirse
    public function mdlMostrarItemError($numcaja)
    {
        
        $sql="SELECT errores.item,errores.no_req,errores.no_caja,errores.estado,errores.ubicacion,errores.recibidos,errores.alistado,
        MIN(ID_CODBAR) AS ID_CODBAR,ID_REFERENCIA,DESCRIPCION
        FROM errores
        INNER JOIN ITEMS ON ID_ITEM=errores.item
        INNER JOIN COD_BARRAS ON ID_ITEMS=ID_ITEM
        INNER JOIN recibido ON recibido.item=errores.item
        WHERE errores.no_caja_recibido = :no_caja
        AND recibido.no_caja = :no_caja
        AND recibido.estado <> 4
        GROUP BY errores.item,errores.no_req,errores.no_caja,errores.estado,errores.ubicacion,errores.recibidos,errores.alistado;";

        $stmt= $this->link->prepare($sql);

        $stmt->bindParam(":no_caja",$numcaja,PDO::PARAM_STR);

        $stmt->execute();

        return $stmt;
        // cierra la conexion
        $stmt=null;
    }

    // muestra los datos necesarios para crear el archivo plano
    public function mdlMostrarDocumento($caja)
    {   
        $sql="SELECT alistado.item AS iditem,alistado.alistado,num_caja,
        lo_origen,lo_destino,documentos
        FROM alistado
        INNER JOIN caja ON caja.no_caja=alistado.no_caja
        INNER JOIN pedido ON pedido.item=alistado.item
        INNER JOIN requisicion ON requisicion.no_req=pedido.no_req
        WHERE";
        for($i=0;$i<count($caja);$i++) {

            $sql.=" alistado.no_caja=:no_caja$i OR";
        }
        
        $sql=substr($sql, 0, -2)."ORDER BY num_caja ASC;";
        
        $stmt= $this->link->prepare($sql);
        
        
        
        foreach ($caja as $i => &$numcaja) {
            $stmt->bindParam(":no_caja$i",$numcaja,PDO::PARAM_INT);   
        }  
        
        $stmt->execute();
        
        return $stmt;  
        
        // cierra la conexion
        $stmt->closeCursor();
        $stmt=null;
    }

    // modifica los items despues de recibidos en el punto de venta
    public function mdlModificarItem($items,$numcaja)
    {
        $no_req=$this->req[0];$alistador=$this->req[1];
        $iditem=$items["iditem"];
        $alistados=$items["alistados"];
        $estado=3;//estado de item corregido
        
        // si no fue alistado en ninguna caja se hace un update del item para que quede en la caja
        if (($items["estado"]==3) &&
            ($items["cajar"]==1 || !(is_numeric($items["cajar"])) ) ) {
            $stmt= $this->link->prepare("UPDATE  pedido 
            SET alistado=:alistados,
            estado=:estado
            WHERE iditem=:iditem
            AND no_req=:noreq;
            ");
        // de lo contrario se agrega el item a la caja, si el item ya estaba en la caja solo se modifica el estado y la cantidad alistada
        }else {
            $stmt= $this->link->prepare("INSERT INTO pedido(item,no_req,no_caja,disp,pedido,alistado,estado) 
            VALUES(:iditem,:no_req,:no_caja,:alistados,:alistados,:alistados,:estado)
            ON DUPLICATE KEY UPDATE
            alistado=:alistados,
            estado=:estado;
            ");
            $stmt->bindParam(":no_caja",$numcaja,PDO::PARAM_INT);
        }
        
        $stmt->bindParam(":iditem",$iditem,PDO::PARAM_STR);
        $stmt->bindParam(":alistados",$alistados,PDO::PARAM_INT);
        $stmt->bindParam(":estado",$estado,PDO::PARAM_INT);
        $stmt->bindParam(":no_req",$no_req,PDO::PARAM_STR);

        $res=$stmt->execute();
        $stmt->closeCursor();
        // retorna el resultado de la sentencia
        return $res;
        // return $stmt->errorInfo();

        

        // cierra la conexion
        $stmt=null;
    }

    // elimina items recibidos
    public function mdlEliminarItemPedido($item,$numcaja)
    {
        $no_req=$this->req[0];$alistador=$this->req[1];
        $stmt= $this->link->prepare('DELETE FROM recibido
        WHERE item=:item
        AND no_req=:no_req
        AND no_caja=:no_caja;');

        $stmt->bindParam(":item",$item,PDO::PARAM_STR);
        $stmt->bindParam(":no_caja",$numcaja,PDO::PARAM_INT);
        $stmt->bindParam(":no_req",$no_req,PDO::PARAM_STR);

        $res=$stmt->execute();
        
        // retorna el resultado de la sentencia
	    return $res;
        $stmt->closeCursor();  
        // cierra la conexion
        $stmt=null;
    }

    //verifica el estado de la caja
    public function mdlVerificarCaja($numcaja)
    {
        $no_req=$this->req[0];$alistador=$this->req[1];
        
        // $stmt= $this->link->prepare('SELECT COUNT(item) AS cantidad
        // FROM recibido
        // WHERE no_caja=:no_caja
        // AND no_req=:no_req
        // AND estado <>4;
        // ');

        $stmt= $this->link->prepare('SELECT VerificarCaja(:no_caja,:no_req) AS verificar ');

        $stmt->bindParam(":no_caja",$numcaja,PDO::PARAM_INT);
        $stmt->bindParam(":no_req",$no_req,PDO::PARAM_STR);

        $res=$stmt->execute();
        $stmt->closeCursor();  
        // retorna el resultado de la sentencia
	    return $res;
        // $stmt->closeCursor();  
        // cierra la conexion
        $stmt=null;
    }

    //aasigna cajas a un tranposrtador
    public function mdlDespachar($numcaja,$transportador)
    {
        
        $stmt= $this->link->prepare('UPDATE caja SET transportador=:transportador,estado=2,enviado=NOW() WHERE no_caja=:no_caja');
        $stmt->bindParam(":transportador",$transportador,PDO::PARAM_INT);
        $stmt->bindParam(":no_caja",$numcaja,PDO::PARAM_INT);
        
        $res=$stmt->execute();
        $stmt->closeCursor();
        // retorna el resultado de la sentencia
	    return $res;
          
        // cierra la conexion
        $stmt=null;
    }

    // incrementa el conteo en documentos
    public function mdlRequisicionDoc()
    {
        $no_req=$this->req[0];$alistador=$this->req[1];
        $stmt= $this->link->prepare('UPDATE requisicion SET documentos=documentos+1 WHERE no_req=:no_req');
        
        $stmt->bindParam(":no_req",$no_req,PDO::PARAM_STR);

        $res=$stmt->execute();
        $stmt->closeCursor();  
        // retorna el resultado de la sentencia
	    return $res;
        // $stmt->closeCursor();  
        // cierra la conexion
        $stmt=null;

    }

}